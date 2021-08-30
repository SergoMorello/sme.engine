<?php
session_name('smeSession');
session_start();

define('ROOT',$_SERVER['DOCUMENT_ROOT'].'/');
define('MIDDLEWARE',$_SERVER['DOCUMENT_ROOT'].'/middleware/');
define('CORE',$_SERVER['DOCUMENT_ROOT'].'/core/');
define('FUNC',$_SERVER['DOCUMENT_ROOT'].'/core/functions/');

require_once(CORE.'database.php');
require_once(CORE.'core.php');
require_once(CORE.'model.php');
require_once(CORE.'controller.php');
require_once(CORE.'compiller.php');
require_once(CORE.'view.php');
require_once(CORE.'route.php');
require_once(CORE.'functions.php');
require_once(ROOT.'route.php');
require_once(CORE.'middleware.php');
require_once(ROOT.'appService.php');

class app extends route {
	private $appService;
	function __construct() {
		header('Content-Type: text/html; charset=utf-8');
		
		$this->defaultConfig();
		
		$this->defaultCompiller();
		
		$this->defaultErrors();
		
		$this->defaultService();
		
		$this->defaultMiddleware();
		
		$this->connectDB();
		
		$this->addControllers();
		
		$this->run();
	}
	private function defaultConfig() {
		$this->config('APP_NAME','SME Engine');
		
		$this->config('APP_DEBUG','true');
		
		$this->config('DB_ENABLED','false');
		
		$this->config('DB_TYPE','mysql');
		
		$this->config('DB_HOST','127.0.0.1');
		
		$this->config('DB_USER','');
		
		$this->config('DB_PASS','');
		
		$this->config('DB_NAME','');
		
		$this->config();
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
		self::declareError('error',404,['message'=>'Not found']);
		
		// 405
		self::declareError('error',405,['message'=>'Method not allowed']);
		
		// 500
		self::declareError('error',500,['message'=>'Internal Server Error']);
	}
	private function defaultMiddleware() {
		middleware::declare('validate',function(){
			die();
		});
	}
	private function defaultService() {
		$this->appService = new appService;
		if (method_exists($this->appService,'register'))
			$this->appService->register();
	}
	private function run() {
		$route = $this->getRoute();
		
		if (!$route)
			abort(404);
		
		if (!$this->checkMethod($route['method']))
			abort(405);
		
		if (middleware::check($route['middleware']))
			return;
		
		if (is_callable($route['callback']) && $route['callback'] instanceof Closure)
			echo call_user_func_array($route['callback'],array_values($route['props'] ?? []));	
		elseif (is_array($route['callback'])) {
			list($controllerName,$methodName) = $route['callback'];
			if (file_exists(core::dirC.$controllerName.".php")) {
				if (class_exists($controllerName)) {
					$controller = new $controllerName();
					if (method_exists($this->appService,'boot'))
						$this->appService->boot($this);
					if (method_exists($controller,$methodName))
						echo $controller->$methodName(...array_values($route['props'] ?? []));
					else
						view::error('error',['message'=>"Method \"".$methodName."\" not found"]);
				}else
					view::error('error',['message'=>"Class \"".$controllerName."\" not found"]);
			}
		}
	}
}