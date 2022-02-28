<?php

function env($name, $default = '') {
	return SME\Core\Env::get($name, $default);
}