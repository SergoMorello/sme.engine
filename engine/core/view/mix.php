<?php

class mix extends core {
	private const nameCache = '_mixData';

	public static function make($files, $name = 'scripts.js', $type = null) {
		if (!is_array($files)) {
			throw new Exception('mix make, files is not array!');
		}

		$nameCache = '_mixData';
		$cache = cache();
		$hash = '';

		if (config('APP_DEBUG'))
			$cache->forget(self::nameCache.$name);

		if ($cache->has(self::nameCache.$name)) {
			$data = $cache->get(self::nameCache.$name);
			$hash = $data['hash'];
		}else{

			$str = '';
			foreach($files as $file) {
				if (file_exists(PUBLIC_DIR.$file) && !empty($file))
					$str .= file_get_contents(PUBLIC_DIR.$file);
			}

			$str = self::compress($str);

			$hash = md5($str);

			$type = is_null($type) ? pathinfo($name, PATHINFO_EXTENSION)=='js' ? 'text/javascript' : 'text/css' : $type;

			$cache->put(self::nameCache.$name, [
				'hash' => $hash,
				'str' => $str,
				'size' => strlen($str),
				'type' => $type
			], 600);
		}

		return route('mix-get', [$hash, $name]);
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

	public function get(request $req) {
		$hash = $req->route('hash');
		$name = $req->route('name');

		$cache = cache();
		
		if (cache()->has(self::nameCache.$name)) {
			$cache = cache()->get(self::nameCache.$name);
			return response::header('Content-type', $cache['type'] ?? 'text/javascript')
			->header('Content-Length', $cache['size'] ?? 0)
			->make($cache['str'] ?? '');
		}else
			abort(404);
	}
}