<?php
class view extends viewCore {	
	public function addView($view,$data=array(),$system=false) {
		$view = str_replace(".","/",$view);
		$pathV = $system ? self::dirVSys : self::dirV;
		if (file_exists($pathV.$view.".php")) {
			if (count($data))
				foreach($data as $key=>$dataIt)
					${$key} = $dataIt;
			$cacheViewName = md5($pathV.$view);
			$cacheViewPath = core::dirCache.$cacheViewName;
			$cacheViewIndex = core::dirCache.".index";
			$md5Hash = md5_file($pathV.$view.".php");
			
			if ($this->genCache($view,$pathV)) {
				$buffer = $this->compiller(file_get_contents($pathV.$view.'.php'));
				file_put_contents($cacheViewPath,$buffer);
			}
			ob_start();
			try {
				require_once($cacheViewPath);
			}catch (Error $e) {
				view::error('error',['message'=>$e->getMessage().' on line: '.$e->getLine()]);
			}catch (Exception $ex) {
				view::error('error',['message'=>$ex->getMessage()]);
			}
			return ob_get_clean();
		}else
			abort(404);
	}
	function include($page,$data=array()) {
		$this->addView($page,$data);
	}
	static function error($page,$props=[],$code=500) {
		header($_SERVER['SERVER_PROTOCOL']." ".$code);
		ob_clean();
		$view = new self;
		$props['code'] = $code;
		echo $view->addView($page,$props,true);
		die();
	}
}
function View($page="",$data=array(),$code=200) {
	if (!$page)
		view::error("Name page, no use");
	header($_SERVER['SERVER_PROTOCOL']." ".$code);
	$view = new view;
	return $view->addView($page,$data);
}