<?php
class view extends compiler {	

	public function addView($view, $data=array(), $system=false) {
		$view = str_replace(".","/",$view);
		$pathV = $system ? self::dirVSys : self::dirV;
		
		if (file_exists($pathV.$view.".php")) {
			
			$cacheViewPath = core::dirCompiler.md5($pathV.$view);
			
			if (compiler::genCache($view,$pathV))
				file_put_contents(
					$cacheViewPath,
					compiler::compile(file_get_contents($pathV.$view.'.php'))
				);
			
			$errors = function() {
				return (new class{
					
					private $errors;
					
					public function __construct() {
						$this->errors = session('__withErrors');
					}
					
					public function any() {
						return count($this->errors) ? true : false;
					}
					
					public function all() {
						return $this->errors;
					}
				});
			};
			
			$connect = function($__file, $__data, $__system, $__errors) {
				$errors = $__errors();
				
				if (count($__data)>0)
					extract($__data);
				
				ob_start();
				
				require_once($__file);
				
				session()->delete([
					'__oldInputs',
					'__withErrors'
				]);
				
				return ob_get_clean();
			};
			
			try {
				
				return $connect($cacheViewPath, $data, $system, $errors);
				
			}catch (ParseError $e) {
				
				middleware::check('viewError',$e);
				
			}catch (Error $e) {
				
				middleware::check('viewError',$e);
				
			}catch (Exception $e) {
				
				middleware::check('viewError',$e);
				
			}catch (ErrorException $e) {
				
				middleware::check('viewError',$e);
				
			}
		}else
			view::error('error',['message'=>'View \''.$view.'\' not found']);
	}
	
	public static function error($page,$props=[],$code=500) {
		header($_SERVER['SERVER_PROTOCOL']." ".$code);
		while(ob_list_handlers())
			ob_end_clean();
		$view = new self;
		$props['code'] = $code;
		die($view->addView($page,$props,true));
	}
}

function View($page="",$data=array(),$code=200) {
	if (!$page)
		view::error('error',['message'=>'Name page, no use']);
	header($_SERVER['SERVER_PROTOCOL']." ".$code);
	$view = new view;
	return $view->addView($page,$data);
}