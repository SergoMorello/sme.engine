<?php
namespace SME\Core\View;

use SME\Core\Response\Response;
use SME\Core\Exception;

class View extends Compiler {	
	
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

	private static function getViewPath($view, $system = false) {
		$view = str_replace(".", "/", $view);
		$path = $system ? SVIEW : VIEW;
		
		if ($isPHP = is_file($path.$view.".php") || $isHTML = is_file($path.$view.".html")) {
			$ext = '';
			if (isset($isHTML))
				$ext = 'html';
			if (isset($isPHP))
				$ext = 'php';
			return (object)[
				'dir' => $path,
				'view' => $view,
				'file' => $view.'.'.$ext,
				'full' => $path.$view.'.'.$ext,
				'ext' => $ext
			];
		}
		return '';
	}

	private static function getView($view, $system = false) {
		if ($result = self::getViewPath($view, $system)) {
			return (object)[
				'path' => $result,
				'data' => file_get_contents($result->full)
			];
		}
	}

	private function addView($view, $data = array(), $system = false) {

		if ($result = self::getView($view, $system)) {
			if ($result->path->ext == 'html')
				return $result->data;

			foreach(self::$shareVars as $nameVar => $valueVar)
				$data[$nameVar] = $valueVar;

			$cacheViewPath = self::dirCompiler.md5($result->path->dir . $result->path->view);
			
			if (Compiler::genCache($result->path->view, $result->path->dir))
				file_put_contents(
					$cacheViewPath,
					Compiler::compile(file_get_contents($result->path->full))
				);

			
			$connect = function($__file, $__data, $__system) {
				$errors = new Errors;
				
				if (count($__data)>0)
					extract($__data);
						
				ob_start();
				
				require_once($__file);
				
				return ob_get_clean();
			};
			
			try {
				
				return $connect($cacheViewPath, $data, $system);
				
			} catch (\Throwable $e) {
				Exception::throw($e);
			}
		}else
			throw new Exception('View \''.$view.'\' not found', 1);
	}
	
	public static function make($view, $data = []) {
		return Response::make((new self)->addView($view, $data))->code(200);
	}
	
	public static function exists($view) {
		return !empty(self::getViewPath($view));
	}

	public static function error($page, $props = [], $code = 500) {
		while(ob_list_handlers())
			ob_end_clean();
		$props['code'] = $code;
		return Response::make((new self)->addView($page, $props, true))->code($code);
	}
}

