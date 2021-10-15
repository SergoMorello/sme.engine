<?php
session_name('smeSession');
session_start();

define('ROOT',realpath(__DIR__ .'/..').'/');
define('EXCEPTIONS',ROOT.'/exceptions/');
define('MIDDLEWARE',ROOT.'/middleware/');
define('STORAGE',ROOT.'/storage/');
define('CORE',ROOT.'/core/');
define('FUNC',ROOT.'/core/functions/');


require_once(CORE.'database.php');
require_once(CORE.'core.php');
require_once(CORE.'config.php');
require_once(CORE.'model.php');
require_once(CORE.'controller.php');
require_once(CORE.'compiler.php');
require_once(CORE.'view.php');
require_once(CORE.'route.php');
require_once(CORE.'http.php');
require_once(CORE.'functions.php');
require_once(CORE.'exceptions.php');
require_once(CORE.'middleware.php');
require_once(CORE.'cache.php');
require_once(CORE.'storage.php');

class app extends core {
	
	private $appService;
	
	static $console, $classes = [];
	
	public function __construct($console=false) {
		header('Content-Type: text/html; charset=utf-8');
		
		set_error_handler(function($errno, $errstr, $errfile, $errline) {
			if (0 === error_reporting())
				return false;
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		});
		
		self::$console = $console;
		
		config::init();
		
		$this->defaultCompiller();
		
		$this->defaultErrors();
		
		self::include('appService');
		
		$this->defaultService();
		
		$this->defaultMiddleware();
		
		new request;
			
		self::include('route');
		
		core::connectDB();
		
		core::addControllers();
		
		$this->bootService();
		
		$this->run();
		
	}
	public function __destruct() {
		
		core::disconnectDB();
		
	}
	
	public static function singleton($name, $callback) {
		self::$classes[] = [
			'name'=>$name,
			'obj'=>$callback()
		];
	}
	
	public static function include($name) {
		$name = str_replace('.','/',$name);
		try {
			
			require_once(ROOT.$name.'.php');
			
		} catch (ParseError $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (Error $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (Exception $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (ErrorException $e) {
			
			exceptions::throw('exception',$e);
			
		}
	}
	private function defaultCompiller() {
		// PHP
		compiler::declare('php',function() {
			return "<?php ";
		});
		
		// END PHP
		compiler::declare('endphp',function() {
			return " ?>";
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
		
		// SECTION SINGLE
		compiler::declare('section',function($arg) {
			return "<?php ob_start(function(\$b){\$this->setSection(".$arg.",\$b);}); ?>";
		});
		
		// SECTION
		compiler::declare('section',function($arg1, $arg2) {
			return "<?php \$this->setSection(".$arg1.",".$arg2."); ?>";
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
	}
	private function defaultErrors() {
		
		if (app::$console) {
			
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
			exceptions::declare(404,function(){
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
		}
	}
	private function defaultMiddleware() {
		
		if (app::$console) {
			
			exceptions::declare('validate',function($errors){
				$list = [];
				foreach($errors as $error)
					$list[] = 'field '.$error['name'].' must be '.$error['access'];
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
			exceptions::declare('validate',function($errors){
				$list = [];
				foreach($errors as $error)
					$list[] = 'field '.$error['name'].' must be '.$error['access'];
				die(redirect()->back()->withErrors($list));
			});
			
			exceptions::declare('exception',function($error, $short=false){
				
				if (config::get('APP_DEBUG') && $error->getCode()==0 && !$short) {
					$sourceLines = function($file) {
						return explode(PHP_EOL,file_get_contents($file));
					};
					
					die(view::error('error',[
						'message'=>$error->getMessage().' on line: '.$error->getLine().' in '.$error->getFile(),
						'errorLine'=>$error->getLine(),
						'sourceLines'=>$sourceLines($error->getFile())
					]));
				}else
					die(view::error('error',['message'=>$error->getMessage()]));
			});
			
			exceptions::declare('httpError',function($e){
				die(view::error('error',[
					'message'=>$e['message'],
					'errorLine'=>0,
					'sourceLines'=>$e['lines']
				]));
			});
			
			exceptions::declare('error',function($e){
				return view::error('error',[
					'message'=>$e['message']
				]);
			});
		}
	}
	private function defaultService() {
		try {
			
			$this->appService = new appService;
			
			if (method_exists($this->appService,'register'))
				$this->appService->register();
			
		} catch (ParseError $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (Error $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (Exception $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (ErrorException $e) {
			
			exceptions::throw('exception',$e);
			
		}
	}
	private function bootService() {
		try {
			
			if (method_exists($this->appService,'boot'))
				$this->appService->boot();
			
		} catch (ParseError $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (Error $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (Exception $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (ErrorException $e) {
			
			exceptions::throw('exception',$e);
			
		}
	}
	private function run() {
		$route = route::getRoute();
		
		if (!$route)
			abort(404);
		
		if (!$this->checkMethod($route['method']))
			abort(405);
		
		if (middleware::check($route['middleware'] ?? null))
			return;
		
		$return = function($result) {
			echo (is_array($result) || is_object($result)) ? response::json($result) : $result;
		};
		
		try {
			
			$callback = is_callable($route['callback']) ? $route['callback'] : [new $route['callback']->controller,$route['callback']->method];
			
			$return(call_user_func_array(
				$callback,
				array_values($route['props'] ?? [])
			));
			
		} catch (ParseError $e) {
			
			exceptions::throw('exception',$e);
		
		} catch (Error $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (Exception $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (ErrorException $e) {
			
			exceptions::throw('exception',$e);
			
		}
	}
}