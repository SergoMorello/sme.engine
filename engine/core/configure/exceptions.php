<?php
namespace SME\Core\Configure;

use SME\Core\App;
use SME\Core\Config;
use SME\Core\Log;
use SME\Core\Exception;
use SME\Core\View\View;

// Exceptions

// Default
Exception::make(null, function($exception){
	if (App::isConsole())
		return log::error($exception->getMessage()."
				\r\non line: ".$exception->getLine().' in '.$exception->getFile()
			);
	if ($exception->getCode()==1)
		return View::error('error', [
			'message' => $exception->getMessage()
		]);
	if (Config::get('app.debug')) {
		$sourceLines = function($file) {
			return explode(PHP_EOL,file_get_contents($file));
		};
		
		return View::error('error',[
			'message' => $exception->getMessage().' on line: '.$exception->getLine().' in '.$exception->getFile(),
			'errorLine' => $exception->getLine(),
			'sourceLines' => $sourceLines($exception->getFile())
		]);
	}else
		return View::error('error',['message' => '']);
});

// Validate
Exception::make(\SME\Exceptions\Validate::class, function($exception){ 
	$list = [];
	foreach($exception->getErrors() as $parentError) {
		foreach($parentError as $error)
			$list[] = trans('validate.'.$error['method'], ['field' => $error['field'], 'params' => implode(',', $error['params'])]);
	}
	if (App::isConsole())
		return Log::error(implode("\r\n",$list));
	return redirect()->back()->withErrors($exception->getMessageErrors());
});

// Http
Exception::make(\SME\Exceptions\Http::class, function($exception){
	switch($exception->getHttpCode()){
		case 401:
			if (App::isConsole())
				return log::error('Not access');
			return View::error(
				'error',
				['message' => 'Not access'],
				401
			);
		break;
		case 404:
			if (App::isConsole())
				return log::error('Not found');
			return View::error(
				'error',
				['message' => 'Not found'],
				404
			);
		break;
		case 405:
			if (App::isConsole())
				return log::error('Method not allowed');
			return View::error(
				'error',
				['message' => 'Method not allowed'],
				405
			);
		break;
		case 500:
			if (App::isConsole())
				return log::error('Internal Server Error');
			return View::error(
				'error',
				['message' => 'Internal Server Error'],
				500
			);
		break;
		default:
			if (App::isConsole())
				return log::error('Unkown error');
			return View::error(
				'error',
				['message' => 'Unkown error'],
				500
			);
	}
	
});

Exception::make(\SME\Exceptions\HttpClient::class, function($exception){
	$e = $exception->getMessage();
	if (App::isConsole())
		return Log::error($e['message']."
			\r\n".implode("\r\n",$e['lines'])
			);
	return View::error('error',[
		'message' => $e['message'],
		'errorLine' => 0,
		'sourceLines' => $e['lines']
	]);
});

Exception::make(\SME\Exceptions\Console::class, function($exception){
	$e = $exception->getErrors();
	$routes = [];
	foreach($e['routes'] as $route)
		$routes[] = $route['url'];
	return Log::info($e['message']."
	\r\n".implode("\r\n",$routes)
	);
});

Exception::make(\SME\Exceptions\Database::class, function($exception){
	if (App::isConsole())
		return $exception->getMessage();
	else
		return View::error('error',[
			'message' => $exception->getMessage()
		]);
});