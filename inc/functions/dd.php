<?php
function dd($data) {
	$replacer = function($data) {
		return str_replace("  ","#",str_replace("\n","<br>\r\n",print_r($data,true)));
	};
	echo $replacer($data);
	echo "<h3>OTHER:</h3>";
	echo "<h4>GET</h4>";
	echo $replacer($_GET);
	echo "<h4>POST</h4>";
	echo $replacer($_POST);
	die();
}