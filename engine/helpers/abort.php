<?php

function abort($code, $props = []) {
	return SME\Core\exceptions::abort($code, $props);
}