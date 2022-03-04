<?php
namespace SME\Core\View;

use SME\Core\Response\Response;
use SME\Core\Exception;

class View extends Compiler {	
	
	const dirVSys = ENGINE.'view/';

	private static $shareVars = [];

	public function __destruct() {
		session()->delete([
			'__oldInputs',
			'__withErrors'
		]);
	}

	public static function share($name, $value) {
		self::$shareVars[$name] = $value;
	}

	private function addView($view, $data = array(), $system = false) {

		$view = str_replace(".","/",$view);
		$pathV = $system ? self::dirVSys : self::dirV;
		
		if ($isPHP = file_exists($pathV.$view.".php") || $isHTML = file_exists($pathV.$view.".html")) {
			if (isset($isHTML))
				return file_get_contents($pathV.$view.'.html');

			foreach(self::$shareVars as $nameVar => $valueVar)
				$data[$nameVar] = $valueVar;

			$cacheViewPath = self::dirCompiler.md5($pathV.$view);
			
			if (Compiler::genCache($view,$pathV))
				file_put_contents(
					$cacheViewPath,
					Compiler::compile(file_get_contents($pathV.$view.'.php'))
				);
			
			$errors = function() {
				return (new class{
					private static $fields = [];

					public function __construct() {
						if ($errors = session('__withErrors')) {
							foreach($errors as $error => $access)
								$this->{$error} = $access;
						}
					}

					private function errors() {
						return get_object_vars($this);
					}
					
					public function has($name) {
						return empty($this->first($name)) ? false : true;
					}

					public function count() {
						return count($this->errors());
					}

					public function first($name) {
						if (!count($this->errors()))
							return '';
						if (isset(self::$fields[$name]))
							return self::$fields[$name];
						foreach($this->errors() as $error) {
							if ($error['field'] == $name)
								return self::$fields[$name] = $error['message'];
						}
						return '';
					}

					public function any() {
						return count($this->errors()) ? true : false;
					}

					public function all() {
						return $this->errors();
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
				
			} catch (\Throwable $e) {
				Exception::throw($e);
			}
		}else
			throw new \Exception('View \''.$view.'\' not found',1);
	}
	
	public static function show($page, $data = []) {
		return Response::make((new self)->addView($page, $data))->code(200);
	}
	
	public static function error($page, $props = [], $code = 500) {
		while(ob_list_handlers())
			ob_end_clean();
		$props['code'] = $code;
		return Response::make((new self)->addView($page, $props, true))->code($code);
	}
}

