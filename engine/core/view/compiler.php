<?php
class compiler extends core {

	const dirCompiler = STORAGE.'.cache/compiler/';

	static $_section;

	protected static function genCache($view, $dirV) {
		
		if (!file_exists(core::dirCache))
					if (!mkdir(core::dirCache))
						die('cache dir, error create');
		if (!file_exists(self::dirCompiler))
					if (!mkdir(self::dirCompiler))
						die('cache dir, error create');
		
		if (config::get('app.debug'))
			return 1;
		
		$cacheViewName = md5($dirV.$view);
		$cacheViewIndex = self::dirCompiler.".index";
		$md5Hash = md5_file($dirV.$view.".php");
		$createIndex = function($cacheViewIndex,$obj) {
			if (file_put_contents($cacheViewIndex,json_encode((is_callable($obj) ? $obj() : $obj))))
				return true;
		};
		if (file_exists($cacheViewIndex)) {
			$cacheIndex = json_decode(file_get_contents($cacheViewIndex));
			$checkIndex = function($cacheIndex,$cacheViewName) {
				foreach($cacheIndex as $key=>$index) {
					if ($index->name==$cacheViewName)
						return $key;
				}
				return -1;
			};
			$keyIndex = $checkIndex($cacheIndex,$cacheViewName);
			if ($keyIndex>=0) {
				if ($cacheIndex[$keyIndex]->hash!=$md5Hash) {
					$cacheIndex[$keyIndex]->hash = $md5Hash;
					return $createIndex($cacheViewIndex,$cacheIndex);
				}
			}else{
				$cacheIndex[] = ['name'=>$cacheViewName,'hash'=>$md5Hash];
				return $createIndex($cacheViewIndex,$cacheIndex);
			}
		}else
			return $createIndex($cacheViewIndex,function() use ($cacheViewName,$md5Hash) {
				return [['name'=>$cacheViewName,'hash'=>$md5Hash]];
			});
	}

	public function setSection($name, $buffer) {
		self::$_section[$name] = $buffer;
	}

	public function getSection($name) {
		return self::$_section[$name] ?? NULL;
	}

	protected static function compile($buffer) {
		$buffer .= "\r\n";
		
		$appendBuffer = "";
		
		$append = function($var) use (&$appendBuffer) {
			$appendBuffer .= $var;
		};
		
		$splitArg = function($str) {
			$args = [];
			
			$str = preg_replace_callback(['/\[(.*)\]/isU', '/(\s)([^,]*)\((.*)\)(\s)/isU'], function($var) use (&$args) {
				if (!empty($var[0])) {
					$key = '__arg_'.count($args);
					$args[$key] = $var[0];	
					return $key;
				}
			}, $str);
			
			$return = explode(',', $str);

			foreach($return as $key => $arg) {
				$arg = trim($arg);
				if (isset($args[$arg]))
					$return[$key] = $args[$arg];
			}

			return $return;
		};
		
		$convertSpec = [
			[
				'{',
				'}',
				'@',
				':',
				'$',
				'<?',
				'?>'
			],
			[
				'&lcub;',
				'&rcub;',
				'&commat;',
				'&colon;',
				'&dollar;',
				'&lt;&quest;',
				'&quest;&rt;'
			]
		];
		
		$buffer = preg_replace_callback('/\@nc(.*)@endnc/isU', function($var) use (&$convertSpec){
			return str_replace($convertSpec[0],$convertSpec[1],$var[1]);
		}, $buffer);
		
		$buffer = preg_replace_callback('/\{\{(.*)\}\}/isU', function($var){
			return "<?php echo htmlspecialchars(".$var[1]."); ?>";
		}, $buffer);
		
		$buffer = preg_replace_callback('/\{\!\!(.*)\!\!\}/isU', function($var){
			return "<?php echo ".$var[1]."; ?>";
		}, $buffer);
		

		$buffer = preg_replace_callback(['/\@(.*)(\(((?>[^()\n]|(?2))*)\))/isU', '/\@([^()]*)\s/isU'], function($var) use (&$append, &$splitArg) {
			
			if (count(self::$arrCompilerView)) {
				$name = $var[1] ?? '';
				$args = (isset($var[3]) && $var[3]) ? $splitArg($var[3]) : [];
				foreach(self::$arrCompilerView as $rule) {
					if ($name == $rule['name']) {
						if (count($args) <= (new ReflectionFunction($rule['return']))->getNumberOfParameters()) {
							$args[] = &$append;		
							return $rule['return'](...$args);
						}
					}
				}
			}
			
			return $var[0];
		}, $buffer);
		
		$buffer .= $appendBuffer ?? NULL;
		
		unset($convertSpec[0][5],$convertSpec[0][6],$convertSpec[1][5],$convertSpec[1][6]);
		
		$buffer = str_replace($convertSpec[1],$convertSpec[0],$buffer);
		
		return $buffer;
	}

	public static function declare($name, $return) {
		self::$arrCompilerView[] = [
			'name' => $name,
			'return' => $return
		];
	}

	public static function flush() {
		foreach(glob(self::dirCompiler.'*') as $file)
			@unlink($file);
		return file_put_contents(self::dirCompiler.'.index','[]');
	}
}