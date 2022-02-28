<?php
namespace SME\Core;

use SME\Core\Route\console;
use SME\Core\View\compiler;
use SME\Core\Request\request;

if (app::isConsole()) {
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
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Init
env::init();

middleware::init();

// Config

config::set('app', app::include('config.app'));

config::set('database', app::include('config.database'));

config::set('storage', app::include('config.storage'));


middleware::declare('api', function($request, $next){
	
	return $next($request);
});

if (config('app.compressorEnabled'))
	\route::get('/'.config("app.compressorName").'/{hash}/{name}', 'compressor@get')->name('compressor-get');

// Compiler

// PHP
compiler::declare('php',function() {
	return "<?php";
});

// END PHP
compiler::declare('endphp',function() {
	return "?>";
});

// IF
compiler::declare('if',function($arg) {
	return "<?php if(".$arg.") { ?>";
});

// END IF
compiler::declare('endif',function() {
	return "<?php } ?>";
});

// FOR
compiler::declare('for',function($arg) {
	return "<?php for(".$arg.") { ?>";
});

// END FOR
compiler::declare('endfor',function() {
	return "<?php } ?>";
});

// FOREACH
compiler::declare('foreach',function($arg) {
	return "<?php foreach(".$arg.") { ?>";
});

// END FOREACH
compiler::declare('endforeach',function() {
	return "<?php } ?>";
});

// ELSE
compiler::declare('else',function() {
	return "<?php }else{ ?>";
});

// SECTION SINGLE
compiler::declare('sectiond',function($arg) {
	return "<?php ob_start(function(\$b){\$this->setSection(".$arg.",\$b);}); ?>";
});

// SECTION
compiler::declare('section',function($arg1, $arg2, $append = null) {
	if (is_null($append))
		return "<?php ob_start(function(\$b){\$this->setSection(".$arg1.",\$b);}); ?>";	
	else{
		return "<?php \$this->setSection(".$arg1.",".$arg2."); ?>";
	}
		
});

// END SECTION
compiler::declare('endsection',function() {
	return "<?php ob_end_clean(); ?>";
});

// YIELD
compiler::declare('yield',function($arg) {
	return "<?php echo \$this->getSection(".$arg."); ?>";
});

// EXTENDS
compiler::declare('extends',function($arg, $append) {
	$varSection = str_replace(['\'','\"'],'',$arg);
	
	$append("<?php ob_end_clean(); echo \$this->addView(".$arg.", [], \$__system); echo \$this->getSection('__view.".$varSection."'); ?>");
	
	return "<?php ob_start(function(\$b){self::\$_section['__view.".$varSection."']=\$b;}); ?>";
});

// INCLUDE
compiler::declare('include',function($arg1, $arg2) {
	$arg2 = is_callable($arg2) ? '[]' : $arg2;
	
	return "<?php echo \$this->addView(".$arg1.", ".$arg2.", \$__system); ?>";
});


// Exceptions

if (app::isConsole()) {
			
	// 401
	exceptions::declare(401,function(){
		return response('Not found');
	});
	
	// 404
	exceptions::declare(404,function(){
		return response('Not found');
	});
	
	// 405
	exceptions::declare(405,function(){
		return response('Method not allowed');
	});
	
	// 500
	exceptions::declare(500,function(){
		return response('Internal Server Error');
	});
	
	exceptions::declare('validate',function($errors){
		$list = [];
		foreach($errors as $error)
			$list[] = 'field '.$error['name'].' must be '.implode(' | ', $error['access']);
		return response(implode("\r\n",$list));
	});
	
	exceptions::declare('exception',function($error){
		
		return response($error->getMessage()."
			\r\non line: ".$error->getLine().' in '.$error->getFile()
		);
	});
	
	exceptions::declare('httpError',function($e){
		return response($e['message']."
		\r\n".implode("\r\n",$e['lines'])
		);
	});
	
	exceptions::declare('consoleError',function($e){
		$routes = [];
		foreach($e['routes'] as $route)
			$routes[] = $route['url'];
		return response($e['message']."
		\r\n".implode("\r\n",$routes)
		);
	});
	
}else{
	// 401
	exceptions::declare(401,function(){
		return view::error(
			'error',
			['message'=>'Not found'],
			401
		);
	});
	
	// 404
	exceptions::declare(404,function($e){
		return view::error(
			'error',
			['message'=>'Not found'],
			404
		);
	});
	
	// 405
	exceptions::declare(405,function(){
		return view::error(
			'error',
			['message'=>'Method not allowed'],
			405
		);
	});
	
	// 500
	exceptions::declare(500,function(){
		return view::error(
			'error',
			['message'=>'Internal Server Error'],
			500
		);
	});
	
	exceptions::declare('validate',function($errors){
		$list = [];
		foreach($errors as $error)
			$list[$error['name']] = 'field '.$error['name'].' must be '.implode(' | ', $error['access']);
		return redirect()->back()->withErrors($list);
	});
	
	exceptions::declare('exception',function($error, $short=false){
		
		if (config::get('app.debug') && $error->getCode()==0 && !$short) {
			$sourceLines = function($file) {
				return explode(PHP_EOL,file_get_contents($file));
			};
			
			return view::error('error',[
				'message'=>$error->getMessage().' on line: '.$error->getLine().' in '.$error->getFile(),
				'errorLine'=>$error->getLine(),
				'sourceLines'=>$sourceLines($error->getFile())
			]);
		}else
			return view::error('error',['message'=>$error->getMessage()]);
	});
	
	exceptions::declare('httpError',function($e){
		return view::error('error',[
			'message'=>$e['message'],
			'errorLine'=>0,
			'sourceLines'=>$e['lines']
		]);
	});
	
	exceptions::declare('error',function($e){
		return view::error('error',[
			'message'=>$e['message']
		]);
	});
}

if (app::isConsole()) {
	
	// Console
	\console::command("serve",function() {
		$port = request::route('port') ?? '8000';
		$host = request::route('host') ?? '127.0.0.1';
		log::info('Start dev server on: http://'.$host.':'.$port);
		exec('php -S '.$host.':'.$port.' -t public dev');
	});
	
	\console::command("route:list",function() {
		log::info('Route list:');
		$list = [];
		foreach(\route::__list() as $el)
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
	
	\console::command("cache:clear",function() {
		if (cache::flush())
			log::info('Cache cleared');
	});

	\console::command("view:clear",function() {
		if (view::flush())
			log::info('Views cleared');
	});

	\console::command("config:{func}",function($func) {
		switch($func) {
			case 'cache':
				if (env::__cache())
					log::info('Config cached');
			break;
			case 'clear':
				if (env::__cacheClear())
					log::info('Config clear');
			break;
			default:
				log::info('Example - config:cache');
		}
	});

	\console::command("make:{func} {name?}",function($func, $name) {

		request::validate([
			'name' => 'required|regex:/([a-zA-Z]{1,}[0-9]{0,})/i'
		]);
		
		switch($func) {
			case 'controller':
				$path = CONTROLLER.$name.'.php';
				$file = file_get_contents(ENGINE.'/make/controller.php');
				$file = str_replace('__NAME__', $name, $file);
				if (file_exists($path))
					return log::info('Controller exists');
				if (file_put_contents($path, $file))
					log::info('Controller created');
			break;
			case 'model':
				$path = MODEL.$name.'.php';
				$file = file_get_contents(ENGINE.'/make/model.php');
				$file = str_replace('__NAME__', $name, $file);
				if (file_exists($path))
					return log::info('Model exists');
				if (file_put_contents($path, $file))
					log::info('Model created');
			break;
			case 'middleware':
				$path = MIDDLEWARE.$name.'.php';
				$file = file_get_contents(ENGINE.'/make/middleware.php');
				$file = str_replace('__NAME__', $name, $file);
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