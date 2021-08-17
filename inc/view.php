<?php
class view extends core {
	private $controller;
	static $staticController;
	function setController($controller) {
		self::$staticController = $controller;
	}
	private function controller() {
		return self::$staticController;
	}
	public function addView($view,$data=array()) {
		if (file_exists(self::$dirV.$view.".php")) {
			if ($data)
				foreach($data as $key=>$dataIt)
					${$key} = $dataIt;
			$this->controller = $this->controller();
			ob_start();
			require_once(self::$dirV.$view.'.php');
			return ob_get_clean();
		}else
			view::error("View \"".$view."\" not found");
	}
	function include($page,$data=array()) {
		$this->addView($page,$data);
	}
	static function error($message,$code=500) {
		header($_SERVER['SERVER_PROTOCOL']." ".$code);
		echo $message;
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