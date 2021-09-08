<?php
session_name('smeSession');
session_start();

define('ROOT',realpath(__DIR__ .'/..').'/');
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
require_once(CORE.'middleware.php');
require_once(CORE.'cache.php');
require_once(CORE.'storage.php');

class app extends core {
	private $appService;
	public function __construct() {
		header('Content-Type: text/html; charset=utf-8');
			
		config::init();
		
		$this->defaultCompiller();
		
		$this->defaultErrors();
		
		self::initFile('appService');
		
		$this->defaultService();
		
		$this->defaultMiddleware();
			
		self::initFile('route');
		
		core::connectDB();
		
		core::addControllers();
		
		$this->run();
		
	}
	public function __destruct() {
		
		core::disconnectDB();
		
	}
	public static function initFile($name) {
		try {
			
			require_once(ROOT.$name.'.php');
			
		} catch (Error $e) {
			
			middleware::check('viewError',$e);
			
		} catch (Exception $e) {
			
			middleware::check('viewError',$e);
			
		} catch (ErrorException $e) {
			
			middleware::check('viewError',$e);
			
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
			
			$append("<?php ob_end_clean(); echo \$this->addView(".$arg.", \$__data, \$__system); echo \$this->getSection('__view.".$varSection."'); ?>");
			
			return "<?php ob_start(function(\$b){self::\$_section['__view.".$varSection."']=\$b;}); ?>";
		});
	}
	private function defaultErrors() {
		// 404
		abort::declare(404,function(){
			return view::error(
				'error',
				['message'=>'Not found'],
				404
			);
		});
		
		// 405
		abort::declare(405,function(){
			return view::error(
				'error',
				['message'=>'Method not allowed'],
				405
			);
		});
		
		// 500
		abort::declare(500,function(){
			return view::error(
				'error',
				['message'=>'Internal Server Error'],
				500
			);
		});
	}
	private function defaultMiddleware() {
		
		middleware::declare('validate',function($errors){
			$list = [];
			foreach($errors as $error)
				$list[] = $error['var'].' need '.$error['access'];
			die(view::error('error',[
				'message'=>'Error fields:',
				'errorLine'=>0,
				'sourceLines'=>$list
			]));
		});
		
		middleware::declare('viewError',function($error){
			if (app()->config->APP_DEBUG) {
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
	}
	private function defaultService() {
		try {
			
			$this->appService = new appService;
			
			if (method_exists($this->appService,'register'))
				$this->appService->register();
			
		} catch (ParseError $e) {
			
			middleware::check('viewError',$e);
			
		} catch (Error $e) {
			
			middleware::check('viewError',$e);
			
		} catch (Exception $e) {
			
			middleware::check('viewError',$e);
			
		} catch (ErrorException $e) {
			
			middleware::check('viewError',$e);
			
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
			
		} catch (Error $e) {
			
			view::error('error',['message'=>$e->getMessage()]);
			
		} catch (Exception $e) {
			
			view::error('error',['message'=>$e->getMessage()]);
			
		} catch (ErrorException $e) {
			
			view::error('error',['message'=>$e->getMessage()]);
			
		}
	}
}