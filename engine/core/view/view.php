<?php
class view extends compiler {	

	public function __destruct() {
		session()->delete([
			'__oldInputs',
			'__withErrors'
		]);
	}

	private function addView($view, $data=array(), $system=false) {
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
					
					public function has($name) {
						return count($this->errors) ? array_key_exists($name, $this->errors) : false;
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
				
				return ob_get_clean();
			};
			
			try {
				
				return $connect($cacheViewPath, $data, $system, $errors);
				
			}catch (ParseError $e) {
				
				exceptions::throw('exception',$e);
				
			}catch (Error $e) {
				
				exceptions::throw('exception',$e);
				
			}catch (Exception $e) {
				
				exceptions::throw('exception',$e);
				
			}catch (ErrorException $e) {
				
				exceptions::throw('exception',$e);
				
			}
		}else
			throw new Exception('View \''.$view.'\' not found',1);
	}
	
	public static function show($page, $data=[]) {
		response::сode(200);
		return (new self)->addView($page,$data);
	}
	
	public static function error($page,$props=[],$code=500) {
		response::сode($code);
		while(ob_list_handlers())
			ob_end_clean();
		$props['code'] = $code;
		return (new self)->addView($page,$props,true);
	}
}

