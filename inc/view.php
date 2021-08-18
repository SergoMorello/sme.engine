<?php
class view extends core {
	private $controller;
	static $staticController,$_sectionObj;
	
	function setController($controller) {
		self::$staticController = $controller;
	}
	private function controller() {
		return self::$staticController;
	}
	private function genCache($view) {
		$cacheViewName = md5(self::$dirV.$view);
		$cacheViewIndex = core::$dirCache.".index";
		$md5Hash = md5_file(self::$dirV.$view.".php");
		if (file_exists($cacheViewIndex)) {
			$cacheIndex = json_decode(file_get_contents($cacheViewIndex));
			$checkIndex = function($cacheIndex,$cacheViewName) {
				foreach($cacheIndex as $key=>$index) {
					if ($index->name==$cacheViewName)
						return $key;
				}
			};
			$keyIndex = $checkIndex($cacheIndex,$cacheViewName);
			if ($cacheIndex[$keyIndex]->hash!=$md5Hash) {
				$cacheIndex[$keyIndex]->hash = $md5Hash;
				file_put_contents($cacheViewIndex,json_encode($cacheIndex));
				return true;
			}
		}else{
			if (file_put_contents($cacheViewIndex,json_encode([['name'=>$cacheViewName,'hash'=>$md5Hash]])))
				return true;
		}
	}
	public function addView($view,$data=array()) {
		if (file_exists(self::$dirV.$view.".php")) {
			if ($data)
				foreach($data as $key=>$dataIt)
					${$key} = $dataIt;
			$this->controller = $this->controller();
			$cacheViewName = md5(self::$dirV.$view);
			$cacheViewPath = core::$dirCache.$cacheViewName;
			$cacheViewIndex = core::$dirCache.".index";
			$md5Hash = md5_file(self::$dirV.$view.".php");
			
			
			if ($this->genCache($view)) {
				$buffer = $this->compiller(file_get_contents(self::$dirV.$view.'.php'));
				file_put_contents($cacheViewPath,$buffer);
			}
			ob_start();
			try {
				require_once($cacheViewPath);
			}catch (Error $e) {
				view::error($e->getMessage());
			}
			return ob_get_clean();
		}else
			view::error("View \"".$view."\" not found");
	}
	function include($page,$data=array()) {
		$this->addView($page,$data);
	}
	static function error($message,$code=500) {
		header($_SERVER['SERVER_PROTOCOL']." ".$code);
		ob_clean();
		echo $message;
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