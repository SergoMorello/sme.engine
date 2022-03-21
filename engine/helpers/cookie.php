<?php

use SME\Core\Response\Objects\Cookie;

function cookie($name, $value, $minutes = 0) {
	return (new Cookie)($name, $value, $minutes);
}