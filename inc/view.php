<?php
class view extends core {
	static $_sectionObj;
	
	private function genCache($view,$dirV) {
		//return 1;
		$cacheViewName = md5($dirV.$view);
		$cacheViewIndex = core::$dirCache.".index";
		$md5Hash = md5_file($dirV.$view.".php");
		$createIndex = function($cacheViewIndex,$cacheViewName,$md5Hash) {
			if (file_put_contents($cacheViewIndex,json_encode([['name'=>$cacheViewName,'hash'=>$md5Hash]])))
				return true;
		};
		if (file_exists($cacheViewIndex)) {
			$cacheIndex = json_decode(file_get_contents($cacheViewIndex));
			$checkIndex = function($cacheIndex,$cacheViewName) {
				foreach($cacheIndex as $key=>$index) {
					if ($index->name==$cacheViewName)
						return $key;
				}
			};
			$keyIndex = $checkIndex($cacheIndex,$cacheViewName);
			if ($keyIndex && $cacheIndex[$keyIndex]->hash!=$md5Hash) {
				$cacheIndex[$keyIndex]->hash = $md5Hash;
				file_put_contents($cacheViewIndex,json_encode($cacheIndex));
				return true;
			}
			return $createIndex($cacheViewIndex,$cacheViewName,$md5Hash);
		}else{
			return $createIndex($cacheViewIndex,$cacheViewName,$md5Hash);
		}
	}
	public function addView($view,$data=array(),$system=false) {
		$pathV = $system ? self::$dirVSys : self::$dirV;
		if (file_exists($pathV.$view.".php")) {
			if ($data)
				foreach($data as $key=>$dataIt)
					${$key} = $dataIt;
			$cacheViewName = md5($pathV.$view);
			$cacheViewPath = core::$dirCache.$cacheViewName;
			$cacheViewIndex = core::$dirCache.".index";
			$md5Hash = md5_file($pathV.$view.".php");
			
			if ($this->genCache($view,$pathV)) {
				$buffer = $this->compiller(file_get_contents($pathV.$view.'.php'));
				file_put_contents($cacheViewPath,$buffer);
			}
			ob_start();
			try {
				require_once($cacheViewPath);
			}catch (Error $e) {
				view::error($e->getMessage());
			}
			return ob_get_clean();
		}
	}
	function include($page,$data=array()) {
		$this->addView($page,$data);
	}
	static function error($message,$code=500) {
		header($_SERVER['SERVER_PROTOCOL']." ".$code);
		ob_clean();
		$view = new self;
		$view->sys[] = 'error';
		echo $view->addView('error',['message'=>$message,'code'=>$code],true);
		die();
	}
	function compiller($buffer) {
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
function View($page="",$data=array(),$code=200) {
	if (!$page)
		view::error("Name page, no use");
	header($_SERVER['SERVER_PROTOCOL']." ".$code);
	$view = new view;
	return $view->addView($page,$data);
}