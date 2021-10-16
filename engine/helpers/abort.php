<?php

function abort($code, $props=[]) {
	return exceptions::abort($code, $props);
}