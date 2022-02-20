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
		
		$buffer .= PHP_EOL;
		
		$appendBuffer = "";
		
		$append = function($var) use (&$appendBuffer) {
			$appendBuffer .= $var;
		};
		
		$splitArg = function($str) {
			$return = [];
			$args = [];
			
			$str = preg_replace_callback(['/\[(.*)\]/isU', '/([^,\s]*)(\(([^()]|(?2))*\))([\s]*)\{(.*)\}/isU', '/([^,\s]*)\((.*)\)/isU'], function($var) use (&$args) {
				if (!empty($var[0])) {
					$key = '__arg_'.count($args);
					$args[$key] = $var[0];	
					return $key;
				}
			}, $str);

				$return = explode(',', $str);
				foreach($return as $key => $arg) {
					$arg = trim($arg);
					foreach($args as $keyArg => $valueArg) {	
						$return[$key] = str_replace($keyArg, $valueArg, $return[$key]);
					}
				}
				
			return $return;
		};
		
		$buffer = preg_replace_callback('/\@html(.*)@endhtml/isU', function($var) use (&$convertSpec){
			return self::convertSpec($var[1])->encode();
		}, $buffer);
		
		$buffer = preg_replace_callback('/\{\{(.*)\}\}/isU', function($var){
			return "<?php echo htmlspecialchars(".$var[1]."); ?>";
		}, $buffer);
		
		$buffer = preg_replace_callback('/\{\!\!(.*)\!\!\}/isU', function($var){
			return "<?php echo ".$var[1]."; ?>";
		}, $buffer);
		

		$buffer = preg_replace_callback(['/\@([a-z0-9]{1,})[\r\n|\n|\s]/isU','/\@([^()\n\@]{0,})(\(((?>[^()\n]|(?2))*)\))/isU'], function($var) use (&$append, &$splitArg) {
			
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

		$buffer = self::convertSpec($buffer)->decode();
		
		return $buffer;
	}

	private static function convertSpec($buffer) {
		return new class($buffer){
			private $buffer, $spec;

			public function __construct($buffer) {
				$this->buffer = $buffer;
				$this->spec = [
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
						'&lt;?',
						'?&gt;'
					]
				];
			}

			public function encode() {
				return str_replace($this->spec[0], $this->spec[1], $this->buffer);
			}

			public function decode() {
				unset($this->spec[0][5],$this->spec[0][6],$this->spec[1][5],$this->spec[1][6]);
		
				return str_replace($this->spec[1], $this->spec[0], $this->buffer);
			}
		};
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