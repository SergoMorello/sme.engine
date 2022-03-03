<?php
namespace App\Exceptions;

use SME\Core\Exception;

class handlerException extends Exception {

	public function render($request, $exception) {
		
		return parent::render($request, $exception);
	}
}