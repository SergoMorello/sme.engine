<?php
function dd($data) {
	echo str_replace("  ","#",str_replace("\n","<br>",print_r($data,true)));
	die();
}