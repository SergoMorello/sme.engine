<?php
use SME\Core\App;
use SME\Core\View\View;

function dd($data) {

	$varDump = function($data = null) {
		ob_start();
		var_dump($data);
		return ob_get_clean();
	};

	$varDumpView = function($data = null) use (&$varDump) {
		$replaceDump = preg_replace([
			'/object\((.*) \((.*)\)/isU',
			'/\[(.*)\]=>\n  /isU',
			'/string\((.*)\) \"(.*)\"/isU',
			'/int\(([0-9]*)\)/isU',
			'/array\(([0-9]*)\)/isU',
			'/ {(.*)}$/is',
			'/( {((?>[^{}]|(?1))*)})/isU',
		],[
			'<span class="toggle"><span style="color: #4800FF"><b>object</b> (\\1</span> (\\2)</span>',
			'<span style="color: #404040"><b>\\1:</b> </span>',
			'<span style="color: #404040; font-size: 11px;">(\\1)</span> <span style="color: #FF6A00">"\\2"</span>',
			'<span style="color: #4800FF">\\1</span>',
			'<span class="toggle"><span style="color: #FF6A00">array:<span style="color: #404040">\\1</span></span></span>',
			'<span class="child_block">\\1</span>',
			'<span class="child_block">\\2</span>',
		],$varDump($data));
		$resVarDump = '<pre>'.$replaceDump.'</pre>';

		return $resVarDump;
	};
	
	if (App::isConsole())
		App::__return($varDump($data));
	else
		App::__return(View::error('dd', [
			'data' => $varDumpView($data)
		]));
}