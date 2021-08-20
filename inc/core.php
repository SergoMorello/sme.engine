<?php
abstract class core {
	static $pagesArr=array(),$pageSelect,$props,$dblink,$dirM,$dirV,$dirC,$dirCache,$dirVSys;
	function __construct() {
		self::$dirM = "m/";
		self::$dirV = "v/";
		self::$dirC = "c/";
		self::$dirCache = ".cache/";
		self::$dirVSys = "inc/v/";
	}
	function connectDB() {
		self::$dblink = new database();
		self::$dblink->connect();
	}
	function disconnectDB() {
		self::$dblink->disconnect();
	}
	public function getUrl() {
		$path = request()->data()->route;
		return $path=="" ? "/" : "/".$path;
	}
	function getPages() {
		return self::$pagesArr;
	}
	public static function guardData($data) {
		if (is_array($data) OR $isObj=is_object($data)) {
			$ret = array();
			foreach($data as $key=>$val)
				$ret[$key] = self::guardData($val);
			return $isObj ? (object)$ret : $ret;
		}
		return htmlspecialchars(addslashes($data));
	}
	function addControllers() { 
		foreach($this->getPages() as $page)
			if (is_array($page['callback']))
				if (file_exists(self::$dirC.$page['callback'][0].".php"))
					require_once(self::$dirC.$page['callback'][0].'.php');
	}
	protected function checkMethod($method) {
		return strtolower($method)==strtolower($_SERVER['REQUEST_METHOD']) ? true : false;
	}
	function getPage() {
		$pages = $this->getPages();
		if ($pages)
			foreach($pages as $page) {
				$arrUrlItems = explode("/",$this->getUrl());
				if (preg_match_all("/\{(.*)\}/U", $page['url'], $match)) {
					$arrPageItems = explode("/",$page['url']);
					$numSec = count($arrUrlItems);
					$trueSec = 0;
					$dataSec = array();
					foreach($arrUrlItems as $key=>$urlItems) {
						if ($urlItems==$arrPageItems[$key])
							++$trueSec;
						foreach($match[1] as $var) {
							if ("{".$var."}"==$arrPageItems[$key]) {
								++$trueSec;
								$dataSec[$var] = $urlItems;
							}
						}
					}
					if ($numSec==$trueSec) {
						self::$props = $dataSec;
						$page['props'] = $dataSec;
						return self::$pageSelect = $page;
					}
					
				}
				if ($this->getUrl()==$page['url'])
					return self::$pageSelect = $page;
			}
		return array();
	}
}