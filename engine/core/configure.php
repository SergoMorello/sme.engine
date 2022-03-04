<?php
namespace SME\Core;

use SME\Core\Route\console;
use SME\Core\View\compiler;
use SME\Core\View\view;
use SME\Core\Request\request;

if (App::isConsole()) {
	ini_set('default_charset','UTF-8');
}else{
	if (!file_exists(TEMP))
		mkdir(TEMP);
	session_save_path(TEMP);
	session_name('smeSession');
	session_start();
	header('Content-Type: text/html; charset=utf-8');
}

set_error_handler(function($errno, $errstr, $errfile, $errline) {
	if (0 === error_reporting())
		return false;
	throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Init
App::singleton('path.public', function(){
	return base_path('public');
});

Env::init();

Middleware::init();

// Config

Config::set('app', App::include('config.app'));

Config::set('database', App::include('config.database'));

Config::set('storage', App::include('config.storage'));


Middleware::declare('api', function($request, $next){
	
	return $next($request);
});

if (config('app.compressorEnabled'))
	\Route::get('/'.config("app.compressorName").'/{hash}/{name}', 'SME\\Modules\\compressor@get')->name('compressor-get');

// Compiler

// PHP
Compiler::declare('php',function() {
	return "<?php";
});

// END PHP
Compiler::declare('endphp',function() {
	return "?>";
});

// IF
Compiler::declare('if',function($arg) {
	return "<?php if(".$arg.") { ?>";
});

// END IF
Compiler::declare('endif',function() {
	return "<?php } ?>";
});

// FOR
Compiler::declare('for',function($arg) {
	return "<?php for(".$arg.") { ?>";
});

// END FOR
Compiler::declare('endfor',function() {
	return "<?php } ?>";
});

// FOREACH
Compiler::declare('foreach',function($arg) {
	return "<?php foreach(".$arg.") { ?>";
});

// END FOREACH
Compiler::declare('endforeach',function() {
	return "<?php } ?>";
});

// ELSE
Compiler::declare('else',function() {
	return "<?php }else{ ?>";
});

// SECTION SINGLE
Compiler::declare('sectiond',function($arg) {
	return "<?php ob_start(function(\$b){\$this->setSection(".$arg.",\$b);}); ?>";
});

// SECTION
Compiler::declare('section',function($arg1, $arg2, $append = null) {
	if (is_null($append))
		return "<?php ob_start(function(\$b){\$this->setSection(".$arg1.",\$b);}); ?>";	
	else{
		return "<?php \$this->setSection(".$arg1.",".$arg2."); ?>";
	}
		
});

// END SECTION
Compiler::declare('endsection',function() {
	return "<?php ob_end_clean(); ?>";
});

// YIELD
Compiler::declare('yield',function($arg) {
	return "<?php echo \$this->getSection(".$arg."); ?>";
});

// EXTENDS
Compiler::declare('extends',function($arg, $append) {
	$varSection = str_replace(['\'','\"'],'',$arg);
	
	$append("<?php ob_end_clean(); echo \$this->addView(".$arg.", [], \$__system); echo \$this->getSection('__view.".$varSection."'); ?>");
	
	return "<?php ob_start(function(\$b){self::\$_section['__view.".$varSection."']=\$b;}); ?>";
});

// INCLUDE
Compiler::declare('include',function($arg1, $arg2) {
	$arg2 = is_callable($arg2) ? '[]' : $arg2;
	
	return "<?php echo \$this->addView(".$arg1.", ".$arg2.", \$__system); ?>";
});

// LANG
Compiler::declare('lang',function($arg1, $arg2) {
	$arg2 = is_callable($arg2) ? '[]' : $arg2;
	return "<?php e(trans(".$arg1.", ".$arg2.")); ?>";
});


// Exceptions

if (App::isConsole()) {
			
	// 401
	Exception::declare(401,function(){
		return response('Not found');
	});
	
	// 404
	Exception::declare(404,function(){
		return response('Not found');
	});
	
	// 405
	Exception::declare(405,function(){
		return response('Method not allowed');
	});
	
	// 500
	Exception::declare(500,function(){
		return response('Internal Server Error');
	});

	Exception::make(\SME\Core\Exceptions\Validate::class, function($exception){ 
		$list = [];
		foreach($exception->getErrors() as $parentError) {
			foreach($parentError as $error)
				$list[] = trans('validate.'.$error['method'], ['field' => $error['field'], 'params' => implode(',', $error['params'])]);
		}
		
		return Log::error(implode("\r\n",$list));
	});

	Exception::make(null, function($exception){
		return log::error($exception->getMessage()."
			\r\non line: ".$exception->getLine().' in '.$exception->getFile()
		);
	});
	
	Exception::declare('httpError',function($e){
		return response($e['message']."
		\r\n".implode("\r\n",$e['lines'])
		);
	});
	
	Exception::declare('consoleError',function($e){
		$routes = [];
		foreach($e['routes'] as $route)
			$routes[] = $route['url'];
		return response($e['message']."
		\r\n".implode("\r\n",$routes)
		);
	});
	
}else{
	
	Exception::make(null, function($exception){
		if (Config::get('app.debug') && $exception->getCode()==0) {
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

	Exception::make(\SME\Core\Exceptions\Validate::class, function($exception){ 
		$list = [];
		foreach($exception->getErrors() as $parentError) {
			foreach($parentError as $error)
				$list[] = trans('validate.'.$error['method'], ['field' => $error['field'], 'params' => implode(',', $error['params'])]);
		}
		
		return redirect()->back()->withErrors($list);
	});

	Exception::make(\SME\Core\Exceptions\Http::class, function($exception){
		switch($exception->getHttpCode()){
			case 401:
				return View::error(
					'error',
					['message' => 'Not access'],
					401
				);
			break;
			case 404:
				return View::error(
					'error',
					['message' => 'Not found'],
					404
				);
			break;
			case 405:
				return View::error(
					'error',
					['message' => 'Method not allowed'],
					405
				);
			break;
			case 500:
				return View::error(
					'error',
					['message' => 'Internal Server Error'],
					500
				);
			break;
			default:
				return View::error(
					'error',
					['message' => 'Unkown error'],
					500
				);
		}
		
	});
	
	Exception::declare('httpError',function($e){
		return View::error('error',[
			'message'=>$e['message'],
			'errorLine'=>0,
			'sourceLines'=>$e['lines']
		]);
	});
	
	Exception::declare('error',function($e){
		return View::error('error',[
			'message'=>$e['message']
		]);
	});
}

if (App::isConsole()) {
	
	// Console
	\Console::command("serve",function() {
		$port = Request::route('port') ?? '8000';
		$host = Request::route('host') ?? '127.0.0.1';
		log::info('Start dev server on: http://'.$host.':'.$port);
		exec('php -S '.$host.':'.$port.' -t '.app('path.public').' dev');
	});
	
	\Console::command("route:list",function() {
		log::info('Route list:');
		$list = [];
		foreach(\Route::__list() as $el)
			$list[] = [
				$el['url'],
				(is_callable($el['callback']) ? 'fn' : $el['callback']->controller.'@'.$el['callback']->method),
				$el['method']
			];
		log::table([
			'URL',
			'Controller',
			'Method'
		], $list);
	});
	
	\Console::command("cache:clear",function() {
		if (Cache::flush())
			log::info('Cache cleared');
	});

	\Console::command("view:clear",function() {
		if (View::flush())
			log::info('Views cleared');
	});

	\Console::command("config:{func}",function($func) {
		switch($func) {
			case 'cache':
				if (Env::__cache())
					log::info('Config cached');
			break;
			case 'clear':
				if (Env::__cacheClear())
					log::info('Config clear');
			break;
			default:
				log::info('Example - config:cache');
		}
	});

	\Console::command("make:{func} {name?}",function($func, $name) {

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
			$name = str_replace('.', '/', $name);
			$splitPath = explode('/', $name);
			$fileName = end($splitPath);
			
			if (count($splitPath) > 1)
				array_pop($splitPath);
			$returnClass = '';
			foreach($splitPath as $dir) {
				$path = $returnPath.$dir;
				$returnPath .= '/'.$dir;
				$returnClass .= '\\'.$dir;
				if (!is_dir($path))
					mkdir($returnPath);
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
					return log::info('Controller exists');
				if (file_put_contents($path, $file))
					log::info('Controller created');
			break;
			case 'model':
				$gff = $getFoldersFile($name, MODEL);
				if (is_null($gff))
					return;
				$path = $gff->path.$gff->file.'.php';
				$file = file_get_contents(ENGINE.'/make/model.php');
				$file = str_replace(['__NAME__', '__CLASS__'], [$gff->file, $gff->class], $file);

				if (file_exists($path))
					return log::info('Model exists');
				if (file_put_contents($path, $file))
					log::info('Model created');
			break;
			case 'middleware':
				$gff = $getFoldersFile($name, MIDDLEWARE);
				if (is_null($gff))
					return;
				$path = $gff->path.$gff->file.'.php';
				$file = file_get_contents(ENGINE.'/make/middleware.php');
				$file = str_replace(['__NAME__', '__CLASS__'], [$gff->file, $gff->class], $file);
				
				if (file_exists($path))
					return log::info('Middleware exists');
				if (file_put_contents($path, $file))
					log::info('Middleware created');
			break;
			default:
				log::info('Example - make:controller MainController');
		}
	});
}