<?php

function response($data = NULL, $code = 200) {
	return response::make($data, $code);
}