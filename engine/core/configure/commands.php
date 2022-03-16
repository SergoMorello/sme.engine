<?php
namespace SME\Core\Configure;

use SME\Core\Request\Request;
use SME\Core\Env;
use SME\Core\Log;

// Console
\Console::command("serve",function() {
	$port = Request::route('port') ?? '8000';
	$host = Request::route('host') ?? '127.0.0.1';
	Log::info('Start dev server on: http://'.$host.':'.$port);
	exec('php -S '.$host.':'.$port.' -t '.app('path.public').' dev');
});

\Console::command("route:list",function() {
	Log::info('Route list:');
	$list = [];
	foreach(\Route::__list() as $el)
		$list[] = [
			$el['url'],
			(is_callable($el['callback']) ? 'fn' : $el['callback']->controller.'@'.$el['callback']->method),
			$el['method']
		];
	Log::table([
		'URL',
		'Controller',
		'Method'
	], $list);
});

\Console::command("cache:clear",function() {
	if (\SME\Modules\Cache::flush())
		Log::info('Cache cleared');
});

\Console::command("view:clear",function() {
	if (View::flush())
		Log::info('Views cleared');
});

\Console::command("config:{func}",function($func) {
	switch($func) {
		case 'cache':
			if (Env::__cache())
				Log::info('Config cached');
		break;
		case 'clear':
			if (Env::__cacheClear())
				Log::info('Config clear');
		break;
		default:
			Log::info('Example - config:cache');
	}
});

\Console::command("make:{func} {name?} {keys?}",function($func, $name) {
	Request::validate([
		'name' => [
			'required',
				[
					'regex',
					['/([a-zA-Z]{1,}[0-9]{0,})/i']
				]
			]
	]);
	
	$getFoldersFile = function($name, $returnPath) {
		$name = str_replace(['.', '\\\\'], '/', $name);
		$splitPath = explode('/', $name);
		$fileName = end($splitPath);
		
		$returnClass = '';
		if (count($splitPath) > 1) {
			array_pop($splitPath);
			foreach($splitPath as $dir) {
				if (empty($dir))
					continue;
				$path = $returnPath.$dir;
				$returnPath .= '/'.$dir;
				$returnClass .= '\\'.$dir;
				if (!is_dir($path))
					mkdir($returnPath);
			}
		}

		return (object)[
			'path' => $returnPath.'/',
			'class' => $returnClass,
			'file' => $fileName
		];
	};

	switch($func) {
		case 'controller':
			$gff = $getFoldersFile($name, CONTROLLER);
			if (is_null($gff))
				return;
			$path = $gff->path.$gff->file.'.php';
			$file = file_get_contents(ENGINE.'/make/controller.php');
			$file = str_replace(['__NAME__', '__CLASS__'], [$gff->file, $gff->class], $file);
			
			if (file_exists($path))
				return Log::info('Controller exists');
			if (file_put_contents($path, $file))
				Log::info('Controller created');
		break;
		case 'model':
			$gff = $getFoldersFile($name, MODEL);
			if (is_null($gff))
				return;
			$path = $gff->path.$gff->file.'.php';
			$file = file_get_contents(ENGINE.'/make/model.php');
			$file = str_replace(['__NAME__', '__CLASS__'], [$gff->file, $gff->class], $file);

			if (file_exists($path))
				return Log::info('Model exists');
			if (file_put_contents($path, $file))
				Log::info('Model created');
		break;
		case 'middleware':
			$gff = $getFoldersFile($name, MIDDLEWARE);
			if (is_null($gff))
				return;
			$path = $gff->path.$gff->file.'.php';
			$file = file_get_contents(ENGINE.'/make/middleware.php');
			$file = str_replace(['__NAME__', '__CLASS__'], [$gff->file, $gff->class], $file);
			
			if (file_exists($path))
				return Log::info('Middleware exists');
			if (file_put_contents($path, $file))
				Log::info('Middleware created');
		break;
		default:
			Log::info('Example - make:controller MainController');
	}
});