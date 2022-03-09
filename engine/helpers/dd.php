<?php
use SME\Core\App;
use SME\Core\View\view;
function dd($data) {

	$varDump = function($data = null) {
		ob_start();
		var_dump($data);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	};

	$varDumpView = function($data = null) use (&$varDump) {
		$resVarDump = '<pre>'.$varDump($data).'</pre>';

		return $resVarDump;
	};
	
	if (App::isConsole())
		App::__return(var_dump($data));
	else
		App::__return(View::error('dd', ['data'=>$varDumpView($data)]));
}