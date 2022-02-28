<?php

function abort($code, $props = []) {
	return SME\Core\Exception::abort($code, $props);
}