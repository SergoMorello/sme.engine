<?php
function dd($data) {
	$arrTree = function($data) use (&$arrTree) {
		$ret = [];
		
		if (is_array($data) || is_object($data)) {

			if (is_object($data) && $className = get_class($data)) {
				$vars = get_object_vars($data);
				$methods = get_class_methods($data);
				$ret[] = (object)[
					'type'=>'object',
					'key'=>$className,
					'inType'=>'',
					'value'=>$arrTree(['props'=>$vars,'methods'=>$methods])
				];
			}else
			foreach($data as $key=>$dt) {
				$arr = $arrTree($dt);
				$ret[] = (object)[
					'type'=>(is_array($data) ? 'array' : 'object'),
					'key'=>(is_array($data) ? $key : get_class($data)),
					'inType'=>$arr[0]->type ?? '',
					'value'=>$arr
				];
			}
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
		foreach($data as $key=>$d) {
			if (is_array($d->value)) {
				$keyShow = md5($key.serialize($d));
				$string = ($d->inType=='string' || $d->inType=='numeric');
				$ret .= '<div class="'.$d->type.($string ? ' oneLine' : null).'">';
				$ret .= !$string ? '<input type="checkbox" class="showArr" id="'.$keyShow.'">' : null;
				$ret .= '<label class="key" title="'.$d->type.'" '.(!$string ? 'for="'.$keyShow.'"' : null).'>'.($d->key ?? 0).':</label>';
				$ret .= '<div class="value">'.$fncTree($d->value).'</div>';
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