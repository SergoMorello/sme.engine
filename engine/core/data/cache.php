<?php

class cache extends core {
	public static function put($key,$value,$time=0) {
		$cache = self::index()->set($key, $value, $time);
		return file_put_contents(self::dirCache.$cache->name,$cache->value);
	}
	private static function index() {
		return (new class{
			function __construct() {
				$this->check();
			}
			private function check() {
				if (!file_exists(core::dirCache.'.index'))
					$this->update();
				
				foreach($this->get() as $line)
					if ($line->time>0 && time()>$line->time)
						if ($obj = $this->delete($line->key))
							unlink(core::dirCache.$obj->name);
			}
			private function findKey($obj,$key) {
				foreach($obj as $keyIt=>$line)
					if ($line->key==$key)
						return $keyIt;
				return -1;
			}
			private function update($obj="") {
				file_put_contents(core::dirCache.'.index',(empty($obj) ? '[]' : json_encode($obj)));
			}
			private function remArrKey($arr,$key) {
				$res = [];
				foreach($arr as $keyIt=>$value) {
					if ($key==$keyIt)
						continue;
					$res[] = $value;
				}
				return $res;
			}
			public function get($key="") {
				$res = json_decode(file_get_contents(core::dirCache.'.index'));
				if (empty($key))
					return $res;
				foreach($res as $line)
					if ($line->key==$key)
						return $line;
			}
			private function dataType($value) {
				$ret = (object)[
							'type'=>'string',
							'value'=>$value
						];
				if (is_callable($value)) {
					$ret->type = 'callable';
					$ret->value = $value();
				}else
				if (is_array($value)) {
					$ret->type = 'array';
					$ret->value = serialize($value);
				}else
				if (is_object($value)) {
					$ret->type = 'object';
					$ret->value = serialize($value);
				}
				return $ret;
			}
			public function set($key, $value, $time) {
				$obj = $this->get();
				$time = $time>0 ? time()+$time : 0;
				$name = md5($key);
				$valType = $this->dataType($value);
				if (($keyIt = $this->findKey($obj,$key))>=0) {
					$objIt = $obj[$keyIt];
					$objIt->time = $time;
					$objIt->type = $valType->type;
				}else
					$obj[] = (object)['key'=>$key,'time'=>$time,'type'=>$valType->type,'name'=>$name];
				$this->update($obj);
				return (object)[
						'name'=>$name,
						'value'=>$valType->value
						];
			}
			public function delete($key) {
				$obj = $this->get();
				if (($keyIt = $this->findKey($obj,$key))>=0) {
					$deleteObj = $obj[$keyIt];
					$obj = $this->remArrKey($obj,$keyIt);
					$this->update($obj);
					return $deleteObj;
				}
			}
		});
	}
	
	public static function get($key,$default="") {
		if ($cache = self::index()->get($key)) {
			$return = file_get_contents(self::dirCache.$cache->name);
			if ($cache->type=='array' || $cache->type=='object')
				return unserialize($return);
			return $return;
		}else
			return empty($default) ? NULL : $default;
	}
	
	public static function pull($key) {
		$res = self::get($key);
		self::forget($key);
		return $res;
	}
	
	public static function forget($key) {
		if ($obj = self::index()->delete($key))
			return unlink(self::dirCache.$obj->name);
	}
	
	public static function has($key) {
		return self::index()->get($key) ? true : false;
	}
	
	public static function flush() {
		foreach(self::index()->get() as $cache)
			self::forget($cache->key);
		return true;
	}
}