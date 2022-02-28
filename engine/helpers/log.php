<?php

function logs($text) {
	$log = new SME\Core\log;
	return is_null($text) ? $log : $log->info($text);
}