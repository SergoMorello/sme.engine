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
			
			$connect = function($__file, $__data, $__system) {
				if (count($__data)>0)
					extract($__data);
				ob_start();
				require_once($__file);
				return ob_get_clean();
			};
			
			try {
				
				return $connect($cacheViewPath, $data, $system);
				
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