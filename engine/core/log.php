<?php

class log extends core {
	private static $props=[];
	
	private static function input($text) {
		if (isset(self::$props['thisLine']) && self::$props['thisLine'])
			$text .= "\r";
		else
		if ((isset(self::$props['newLine']) && !self::$props['newLine']) || !isset(self::$props['newLine'])) {
			$text .= PHP_EOL;
			self::updateLog($text);
		}
		
		return $text;
	}
	
	private static function cout($text) {
		$input = self::input($text);
		if (!app::isConsole())
			return;
		fwrite(STDOUT,iconv('UTF-8','IBM866',$input));
	}
	
	private static function cerr($text) {
		$input = self::input($text);
		if (!app::isConsole())
			return;
		fwrite(STDERR,iconv('UTF-8','IBM866',$input));
	}
	
	private static function updateLog($text) {
		if (!config('LOG_ENABLED'))
			return;
		$file = 'system.log';
		$fileExists = file_exists(LOGS.$file);
		$date = date("Y-m-d H:i:s");
		$log = $date."\tNew file".PHP_EOL;
		if ($fileExists && filesize(LOGS.$file)>=config('MAX_LOG_SIZE')) {
			rename(LOGS.$file, LOGS. str_replace([' ',':'],['_','-'],$date).'_'.$file);
			$fileExists = false;
			
		}else
			$log = $fileExists ? file_get_contents(LOGS.$file) : '';
		$text = $date."\t".$text;
		file_put_contents(LOGS.$file,$log.$text);
	}
	
	public static function newLine($value) {
		if (is_bool($value))
			self::$props['newLine'] = $value;
		return new self;
	}
	
	public static function thisLine($value) {
		self::$props['newLine'] = false;
		if (is_bool($value))
			self::$props['thisLine'] = $value;
		return new self;
	}
	
	public static function info($text) {
		self::cout($text);
		self::$props = [];
	}
	
	public static function error($text) {
		self::cerr($text);
		self::$props = [];
	}
}