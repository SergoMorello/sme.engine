<?php
class compiller extends core {
	static $_section;
	protected function genCache($view,$dirV) {
		if (!file_exists(core::dirCache))
					if (!mkdir(core::dirCache))
						die('cache dir, error create');
		if (!file_exists(core::dirCompiller))
					if (!mkdir(core::dirCompiller))
						die('cache dir, error create');
		
		if (app()->config->APP_DEBUG)
			return 1;
		
		$cacheViewName = md5($dirV.$view);
		$cacheViewIndex = core::dirCompiller.".index";
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
	protected function compile($buffer) {
		$buffer .= "\r\n";
		
		$appendBuffer = "";
		
		$append = function($var) use (&$appendBuffer) {
			$appendBuffer .= $var;
		};
		
		$splitArg = function($str) {
			return explode(',',$str);
		};
		
		$buffer = preg_replace_callback('/\{\{(.*)\}\}/isU', function($var){
			return "<?php echo htmlspecialchars(".$var[1]."); ?>";
		}, $buffer);
		
		$buffer = preg_replace_callback('/\{\!\!(.*)\!\!\}/isU', function($var){
			return "<?php echo ".$var[1]."; ?>";
		}, $buffer);
		
		$buffer = preg_replace_callback(['/\@([a-z0-9]{1,})[\r\n|\n|\s]/isU','/\@([^()\n\@]{0,})(\(((?>[^()\n]|(?2))*)\))/isU'], function($var) use (&$append,&$splitArg) {
			$arg = (isset($var[3]) && $var[3]) ? $splitArg($var[3]) : NULL;
			
			if (count(self::$arrCompillerView)) {
				$argCustom = $arg;
				foreach(self::$arrCompillerView as $rule) {
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
		
		return $buffer;
	}
	public static function declare($name,$return) {
		self::$arrCompillerView[] = ['name'=>$name,'return'=>$return];
	}
}