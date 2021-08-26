<?php
class viewCore extends core {
	static $_sectionObj,$_section;
	protected function genCache($view,$dirV) {
		return 1;
		$cacheViewName = md5($dirV.$view);
		$cacheViewIndex = core::dirCache.".index";
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
		return self::$_section[$name];
	}
	protected function compiller($buffer) {
		$appendBuffer = "";
		
		$append = function($var) use (&$appendBuffer) {
			$appendBuffer .= $var;
		};
		
		$splitArg = function($str) {
			return explode(',',$str);
		};
		
		$buffer = preg_replace_callback("/\{\{(.*)\}\}/", function($var){
			return "<?php echo ".$var[1]."; ?>";
		}, $buffer);
		
		$buffer = preg_replace_callback("/\@([a-z0-9]{1,})\((.*)\)|\@([a-z0-9]{1,})(?=\b|[\s-])/isU", function($var) use (&$append,&$splitArg) {
			$arg = $var[2] ? $splitArg($var[2]) : NULL;

			if (count(self::$arrCompillerView)) {
				$argCustom = $arg;
				foreach(self::$arrCompillerView as $rule) {
					if ((isset($var[1]) && $var[1]==$rule['name']) || (isset($var[3]) && $var[3]==$rule['name'])) {
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
}