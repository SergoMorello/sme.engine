<?php

class handlerException extends exceptions {

	public function render($request, $exception) {
		
		return parent::render($request, $exception);
	}
}