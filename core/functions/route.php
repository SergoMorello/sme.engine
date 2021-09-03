<?php
function route($name=NULL,$props=[]) {
	if (is_null($name))
		return (new class{
			public function current() {
				return core::url();
			}
		});
	$replaceVars = function($url,$vars=[]) {
		$numVars = count($vars);
		$i = 0;
		$url = preg_replace_callback('/\{([0-9A-Za-z?]{0,})\}/isU',function($var) use (&$vars,&$i) {
			++$i;
			return $vars[$i-1];
		},$url);
		$url .= $numVars>$i ? "?".(function() use (&$vars,&$i) {
			$ret = "";
			foreach($vars as $key=>$value) {
				if (($key+1)<=$i)
					continue;
				$ret .= $value;
				$ret .= (end($vars)==$value) ? NULL : "&";
			}
			return $ret;
		})() : NULL;
		return $url;
	};
	$searchRoute = function($name) {
		foreach(route::$routes as $page)
			if (isset($page['name']) && $page['name']==$name)
				return (object)$page;
	};
	return $replaceVars($searchRoute($name)->url,$props);
}