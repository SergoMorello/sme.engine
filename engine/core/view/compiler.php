<?php
class compiler extends core {
	static $_section;

	protected static function genCache($view,$dirV) {
		if (!file_exists(core::dirCache))
					if (!mkdir(core::dirCache))
						die('cache dir, error create');
		if (!file_exists(core::dirCompiler))
					if (!mkdir(core::dirCompiler))
						die('cache dir, error create');
		
		if (config::get('APP_DEBUG'))
			return 1;
		
		$cacheViewName = md5($dirV.$view);
		$cacheViewIndex = core::dirCompiler.".index";
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

	public function setSection($name,$buffer) {
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
			return explode(',',$str);
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
		
		$buffer = preg_replace_callback(['/\@([a-z0-9]{1,})[\r\n|\n|\s]/isU','/\@([^()\n\@]{0,})(\(((?>[^()\n]|(?2))*)\))/isU'], function($var) use (&$append,&$splitArg) {
			$arg = (isset($var[3]) && $var[3]) ? $splitArg($var[3]) : [];
			
			if (count(self::$arrCompilerView)) {
				$argCustom = (isset($var[3]) && $var[3]) ? $splitArg($var[3]) : [];
				foreach(self::$arrCompilerView as $rule) {
					if ((isset($var[1]) && $var[1]==$rule['name'])) {
						if (count($argCustom)<=(new ReflectionFunction($rule['return']))->getNumberOfParameters()) {
							$argCustom[count($arg)] = &$append;
							return $rule['return'](...$argCustom);
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

	public static function declare($name,$return) {
		self::$arrCompilerView[] = ['name'=>$name,'return'=>$return];
	}

	public static function flush() {
		foreach(glob(core::dirCompiler.'*') as $file)
			@unlink($file);
		return file_put_contents(core::dirCompiler.'.index','[]');
	}
}