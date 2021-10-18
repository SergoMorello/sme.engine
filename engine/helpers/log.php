<?php

function logs($text) {
	$log = new log;
	return is_null($text) ? $log : $log->info($text);
}