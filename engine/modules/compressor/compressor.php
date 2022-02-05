<?php

class compressor extends core {
	const nameCache = '_compressorData';

	public static function make($files, $name = 'scripts.js', $type = null) {
		if (!is_array($files)) {
			throw new Exception('compressor make, files is not array!');
		}

		$cache = cache();
		$hash = '';

		if (config('app.debug'))
			$cache->forget(self::nameCache.$name);

		if ($cache->has(self::nameCache.$name)) {
			$data = $cache->get(self::nameCache.$name);
			$hash = $data['hash'];
		}else{
			$paths = [];
			$str = '';
			foreach($files as $file) {
				if (file_exists(PUBLIC_DIR.$file) && !empty($file)) {
					$str .= file_get_contents(PUBLIC_DIR.$file);
					$paths[] = pathinfo($file, PATHINFO_DIRNAME);
				}
			}

			$str = self::compress($str);

			$hash = md5($str);

			$type = is_null($type) ? pathinfo($name, PATHINFO_EXTENSION)=='js' ? 'text/javascript' : 'text/css' : $type;

			$cache->put(self::nameCache.$hash, [
				'paths' => $paths
			], 600);

			$cache->put(self::nameCache.$name, [
				'hash' => $hash,
				'str' => $str,
				'size' => strlen($str),
				'type' => $type
			], 600);
		}

		return route('compressor-get', [$hash, $name]);
	}

	private static function compress($str) {
		$str = preg_replace("/\t\/\/(.*)/i", " ", $str);
		$str = preg_replace("/\n\/\/(.*)/i", " ", $str);
		$str = preg_replace("/\r\n\/\/(.*)/i", " ", $str);
		$str = preg_replace("/ \/\/(.*)\n/i", " ", $str);
		$str = str_replace("\r\n"," ",$str);
		$str = str_replace("\n"," ",$str);
		$str = str_replace("\t"," ",$str);

		return $str;
	}

	private function getAsset($hash, $name) {
		$cache = cache();
		if ($cache->has(self::nameCache.$hash)) {
			$paths = $cache->get(self::nameCache.$hash);
			foreach($paths['paths'] as $path) {
				$fullPath = PUBLIC_DIR.$path.'/'.$name;
				if (file_exists($fullPath)) {
					return file_get_contents($fullPath);
				}
			}
		}
	}

	public function get(request $req) {
		$hash = $req->route('hash');
		$name = $req->route('name');

		$cache = cache();
		
		if ($cache->has(self::nameCache.$name)) {
			$cache = cache()->get(self::nameCache.$name);
			return response($cache['str'] ?? '')
			->header('Content-type', $cache['type'] ?? 'text/javascript')
			->header('Content-Length', $cache['size'] ?? 0);
		}else{
			if ($assetData = $this->getAsset($hash, $name)) {
				return $assetData;
			}else
				abort(404);
		}	
	}
}