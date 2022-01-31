<?php
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
	
	if (app::isConsole())
		app::__return(var_dump($data));
	else
		app::__return(view::error('dd', ['data'=>$varDumpView($data)]));
}