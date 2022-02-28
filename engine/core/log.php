<?php
namespace SME\Core;

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
	
	private static function inputOS($input) {
		return PHP_OS=='WINNT' ? iconv('UTF-8','CP866',$input) : $input;
	}

	private static function cout($text) {
		$input = self::input($text);
		if (!App::isConsole())
			return;
		fwrite(STDOUT, self::inputOS($input));
	}
	
	private static function cerr($text) {
		$input = self::input($text);
		if (!App::isConsole())
			return;
		fwrite(STDERR, self::inputOS($input));
	}
	
	private static function updateLog($text) {
		if (!config('app.logEnabled'))
			return;
		$file = 'system.log';
		$fileExists = file_exists(LOGS.$file);
		$date = date("Y-m-d H:i:s");
		$log = $date."\tNew file".PHP_EOL;
		if ($fileExists && filesize(LOGS.$file)>=config('app.maxLogSize')) {
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

	public static function table($heads, $rows) {

		$addNp = function($str, $num) {
			$np = ' ';
			for($i = 0; $i <= $num / 2; $i++)
				$np .= ' ';
			return $np.$str.$np;
		};

		$maxWidth = [];
		$textHead = '';
		
		foreach($rows as $row) {
			foreach($heads as $key => $head) {
				$rowLen = strlen($row[$key]);
				if ($rowLen >= ($maxWidth[$key] ?? 0))
					$maxWidth[$key] = $rowLen;
			}
		}

		foreach($heads as $key => $head) {
			$headLen = strlen($head);
			$headLenDelta = ($maxWidth[$key] > $headLen) ? $maxWidth[$key] - $headLen : 1;
				
			$textHead .= $addNp($head, $headLenDelta);
			if ($headLen > ($maxWidth[$key] ?? 0))
				$maxWidth[$key] = $headLen;
		}
		self::info($textHead);

		foreach($rows as $row) {
			$textRows = '';
			foreach($heads as $key => $head) {
				$rowLen = strlen($row[$key]);
				$rowLenDelta = ($maxWidth[$key] > $rowLen) ? $maxWidth[$key] - $rowLen : 1;
				$textRows .= $addNp($row[$key], $rowLenDelta);
			}
			self::info($textRows);
		}
	}
}