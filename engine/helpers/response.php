<?php

function response($data = NULL, $code = 200) {
	return SME\Core\Response\Response::make($data, $code);
}