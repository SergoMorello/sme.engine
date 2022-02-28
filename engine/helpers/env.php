<?php

function env($name, $default = '') {
	return SME\Core\env::get($name, $default);
}