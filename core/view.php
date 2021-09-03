<?php
class view extends compiller {	
	public function addView($view,$data=array(),$system=false) {
		$view = str_replace(".","/",$view);
		$pathV = $system ? self::dirVSys : self::dirV;
		
		if (file_exists($pathV.$view.".php")) {
			if (count($data))
				foreach($data as $key=>$dataIt)
					${$key} = $dataIt;
			$cacheViewName = md5($pathV.$view);
			$cacheViewPath = core::dirCompiller.$cacheViewName;
			$cacheViewIndex = core::dirCompiller.".index";
			$md5Hash = md5_file($pathV.$view.".php");
			
			if ($this->genCache($view,$pathV)) {
				$buffer = $this->compile(file_get_contents($pathV.$view.'.php'));
				file_put_contents($cacheViewPath,$buffer);
			}
			ob_start();
			try {
				require_once($cacheViewPath);
			}catch (ParseError $p) {
				middleware::check('viewError',$p);
			}catch (Error $e) {
				middleware::check('viewError',$e);
			}catch (Exception $ex) {
				middleware::check('viewError',$ex);
			}catch (ErrorException $ex) {
				middleware::check('viewError',$ex);
			}
			return ob_get_clean();
		}else
			abort(404);
	}
	function include($page,$data=array()) {
		$this->addView($page,$data);
	}
	public static function error($page,$props=[],$code=500) {
		header($_SERVER['SERVER_PROTOCOL']." ".$code);
		ob_clean();
		$view = new self;
		$props['code'] = $code;
		die($view->addView($page,$props,true));
	}
}
function View($page="",$data=array(),$code=200) {
	if (!$page)
		view::error("Name page, no use");
	header($_SERVER['SERVER_PROTOCOL']." ".$code);
	$view = new view;
	return $view->addView($page,$data);
}