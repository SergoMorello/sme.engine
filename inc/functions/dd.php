<?php
function dd($data) {
	$replacer = function($data) {
		return str_replace("  ","#",str_replace("\n","<br>\r\n",print_r($data,true)));
	};
	echo "<div style='color:#353535;padding: 10px;font-size:15px;border-bottom:1px dotted #212121;'>".$replacer($data)."</div>";
	echo "<div style='color:#727272;font-size:12px;'>";
	echo "<h3 style='color:#545454;'>OTHER:</h3>";
	echo "<h4 style='color:#545454;'>GET</h4>";
	echo $replacer($_GET);
	echo "<h4 style='color:#545454;'>POST</h4>";
	echo $replacer($_POST);
	echo "</div>";
	die();
}