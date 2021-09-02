<?php
function dd($data) {
	$replacer = function($data) {
		return str_replace("  ","#",str_replace("\n","<br>\r\n",print_r($data,true)));
	};
	header('Content-Type: text/html');
	$ret = "<div style='color:#353535;padding: 10px;font-size:15px;border-bottom:1px dotted #212121;'>".$replacer($data)."</div>";
	$ret .= "<div style='color:#727272;font-size:12px;'>";
	$ret .= "<h3 style='color:#545454;'>OTHER:</h3>";
	$ret .= "<h4 style='color:#545454;'>GET</h4>";
	$ret .= $replacer($_GET);
	$ret .= "<h4 style='color:#545454;'>POST</h4>";
	$ret .= $replacer($_POST);
	$ret .= "</div>";
	die($ret);
}