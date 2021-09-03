<?php
session_name('smeSession');
session_start();

define('ROOT',$_SERVER['DOCUMENT_ROOT'].'/');
define('MIDDLEWARE',$_SERVER['DOCUMENT_ROOT'].'/middleware/');
define('STORAGE',$_SERVER['DOCUMENT_ROOT'].'/storage/');
define('CORE',$_SERVER['DOCUMENT_ROOT'].'/core/');
define('FUNC',$_SERVER['DOCUMENT_ROOT'].'/core/functions/');


require_once(CORE.'database.php');
require_once(CORE.'core.php');
require_once(CORE.'config.php');
require_once(CORE.'model.php');
require_once(CORE.'controller.php');
require_once(CORE.'compiller.php');
require_once(CORE.'view.php');
require_once(CORE.'route.php');
require_once(CORE.'http.php');
require_once(CORE.'functions.php');
require_once(CORE.'middleware.php');
require_once(CORE.'cache.php');
require_once(CORE.'storage.php');

class app extends route {
	private $appService;
	function __construct() {
		header('Content-Type: text/html; charset=utf-8');
			
		config::init();
		
		$this->defaultCompiller();
		
		$this->defaultErrors();
		
		self::initFile('appService');
		
		$this->defaultService();
		
		$this->defaultMiddleware();
			
		self::initFile('route');
		
		$this->connectDB();
		
		$this->addControllers();
		
		$this->run();
		
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
		compiller::declare('php',function(){
			return "<?php ";
		});
		
		// END PHP
		compiller::declare('endphp',function(){
			return " ?>";
		});
		
		// IF
		compiller::declare('if',function($arg){
			return "<?php if(".$arg.") { ?>";
		});
		
		// END IF
		compiller::declare('endif',function(){
			return "<?php } ?>";
		});
		
		// FOR
		compiller::declare('for',function($arg){
			return "<?php for(".$arg.") { ?>";
		});
		
		// END FOR
		compiller::declare('endfor',function(){
			return "<?php } ?>";
		});
		
		// FOREACH
		compiller::declare('foreach',function($arg){
			return "<?php foreach(".$arg.") { ?>";
		});
		
		// END FOREACH
		compiller::declare('endforeach',function(){
			return "<?php } ?>";
		});
		
		// SECTION SINGLE
		compiller::declare('section',function($arg){
			return "<?php ob_start(function(\$b){\$this->setSection(".$arg.",\$b);}); ?>";
		});
		
		// SECTION
		compiller::declare('section',function($arg1,$arg2){
			return "<?php \$this->setSection(".$arg1.",".$arg2."); ?>";
		});
		
		// END SECTION
		compiller::declare('endsection',function(){
			return "<?php ob_end_clean(); ?>";
		});
		
		// YIELD
		compiller::declare('yield',function($arg){
			return "<?php echo \$this->getSection(".$arg."); ?>";
		});
		
		// EXTENDS
		compiller::declare('extends',function($arg,$append){
			$varSection = str_replace(['\'','\"'],'',$arg);
			$append("<?php ob_end_clean(); echo \$this->addView(".$arg."); echo \$this->getSection('__view.".$varSection."'); ?>");
			return "<?php ob_start(function(\$b){self::\$_section['__view.".$varSection."']=\$b;}); ?>";
		});
	}
	private function defaultErrors() {
		// 404
		abort::declare(404,function(){
			return view::error('error',['message'=>'Not found'],404);
		});
		
		// 405
		abort::declare(405,function(){
			return view::error('error',['message'=>'Method not allowed'],405);
		});
		
		// 500
		abort::declare(500,function(){
			return view::error('error',['message'=>'Internal Server Error'],500);
		});
	}
	private function defaultMiddleware() {
		
		middleware::declare('validate',function($errors){
			$list = [];
			foreach($errors as $error)
				$list[] = $error['var'].' need '.$error['access'];
			die(view::error('error',['message'=>'Error fields:','errorLine'=>0,'sourceLines'=>$list]));
		});
		
		middleware::declare('viewError',function($error){
			$sourceLines = function($file) {
				return explode(PHP_EOL,file_get_contents($file));
			};
			die(view::error('error',['message'=>$error->getMessage().' on line: '.$error->getLine().' in '.$error->getFile(),'errorLine'=>$error->getLine(),'sourceLines'=>$sourceLines($error->getFile())]));
		});
	}
	private function defaultService() {
		try {
		$this->appService = new appService;
		if (method_exists($this->appService,'register'))
			$this->appService->register();
		}catch (ParseError $e) {
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
		$route = $this->getRoute();
		
		if (!$route)
			abort(404);
		
		if (!$this->checkMethod($route['method']))
			abort(405);
		
		if (middleware::check($route['middleware'] ?? NULL))
			return;
		
		$return = function($result) {
			echo (is_array($result) || is_object($result)) ? response::json($result) : $result;
		};
		
		try {
			if (is_callable($route['callback']) && $route['callback'] instanceof Closure)
				$return(call_user_func_array($route['callback'],array_values($route['props'] ?? [])));
			elseif (is_array($route['callback'])) {
				list($controllerName,$methodName) = $route['callback'];
				$controller = new $controllerName();
				if (method_exists($this->appService,'boot'))
					$this->appService->boot($this);
				$return($controller->$methodName(...array_values($route['props'] ?? [])));
			}
		} catch (Error $e) {
			dd($e);
			view::error('error',['message'=>$e->getMessage()]);
		} catch (Exception $e) {
			view::error('error',['message'=>$e->getMessage()]);
		} catch (ErrorException $e) {
			view::error('error',['message'=>$e->getMessage()]);
		}
	}
}