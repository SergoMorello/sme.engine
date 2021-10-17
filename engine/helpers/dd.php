<?php
function dd($data) {
	$arrTree = function($data) use (&$arrTree) {
		$ret = [];
		
		if (is_array($data) || is_object($data)) {

			if (is_object($data) && $className = get_class($data))
				$ret[] = (object)[
					'type'=>'object',
					'key'=>$className,
					'value'=>$arrTree(['props'=>get_object_vars($data),'methods'=>get_class_methods($data)])
				];
			else
			foreach($data as $key=>$dt)
				$ret[] = (object)[
					'type'=>(is_array($data) ? 'array' : 'object'),
					'key'=>(is_array($data) ? $key : get_class($data)),
					'value'=>$arrTree($dt)
				];
		}
		
		if (is_string($data) || is_numeric($data))
			$ret[] = (object)[
				'type'=>(is_string($data) ? 'string' : 'numeric'),
				'value'=>$data
			];
		
		if (count($ret)==0)
			$ret[] = (object)[
				'type'=>'unknown',
				'value'=>print_r($data,true)
			];
		
		return $ret;
	};
	
	$fncTree = function($data) use (&$fncTree) {
		$ret = '';
		foreach($data as $d) {
			if (is_array($d->value)) {
				$ret .= '<div class="'.$d->type.'"><span class="type">'.$d->type.'</span><span class="key">'.($d->key ?? 0).':</span>';
				$ret .= $fncTree($d->value);
				$ret .= '</div>';
			}else
				$ret .= '<div class="'.$d->type.'">'.$d->value.'</div>';
		}
		return $ret;
	};
	
	if (app::$console) {
		die(print_r($data,true));
	}else
		die(view::error('dd', ['data'=>$fncTree($arrTree($data))]));
}