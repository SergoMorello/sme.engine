<?php
use SME\Core\Lang;

function trans($string, $params = []) {
	return SME\Core\Lang::get($string, $params);
}