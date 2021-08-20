<?php
class viewCore extends core {
	static $_sectionObj;
	protected function genCache($view,$dirV) {
		//return 1;
		$cacheViewName = md5($dirV.$view);
		$cacheViewIndex = core::$dirCache.".index";
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
	protected function compiller($buffer) {
		self::$_sectionObj = (object)[];
		$buffer = preg_replace_callback("/\{\{(.*)\}\}/", function($var){
			return "<?php echo ".$var[1]."; ?>";
		}, $buffer);
		
		$buffer = preg_replace_callback("/\@([a-z0-9]{1,})(\((\'|\"|)(.*)\\3\)|\(\)|)\s(.*)/isU", function($var) {
			if ($var[1]=="php")
				return "<?php ".$var[5];
			if ($var[1]=="for" || $var[1]=="foreach" || $var[1]=="if")
				return "<?php ".$var[1]."(".$var[4].") { ?> ".$var[5];
			if ($var[1]=="section") {
				++self::$_sectionObj->int;
				self::$_sectionObj->start[$var[4]] = self::$_sectionObj->int;
				return "<?php ob_start(); ".$var[5]." ?>";
			}
			if ($var[1]=="getSection")
				return "<?php echo self::\$_sectionObj->end[self::\$_sectionObj->start['".$var[4]."']]; ?>";
				
			if ($var[1]=="extends") {
				return "<?php echo \$this->addView('".$var[4]."'); ?>";
			}
			return $var[0];
		}, $buffer);
		
		$buffer = preg_replace_callback("/\@end([a-z0-9]{1,})/is", function($var) {
			if ($var[1]=="php")
				return "?>";
			if ($var[1]=="for" || $var[1]=="foreach" || $var[1]=="if")
				return "<?php } ?>";
			if ($var[1]=="section") {
				--self::$_sectionObj->int;
				return "<?php self::\$_sectionObj->end[".(self::$_sectionObj->int+1)."] = ob_get_clean(); ?>";
			}
			return $var[0];
		}, $buffer);
		return $buffer;
	}
}