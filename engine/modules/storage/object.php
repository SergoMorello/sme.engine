<?php
namespace SME\Modules\Storage;

use SME\Core\Config;

class Object {
	private $disk;

	private function getDisk($disk = '') {

		$disk = is_null($disk) ? config('storage.default') : $disk;
		if (!is_null($this->disk))
			$disk = $this->disk;
		$path = config('storage.disks.'.$disk)['root'] ?? null;
		if ($path)
			return $path;
		else
			throw new \Exception('Disk '.$disk.' not found in config cache', 1);
	}

	private function makeFolders($path) {
		$splitPath = explode('/', $path);
		if (count($splitPath)<=1)
			return;
			
		array_pop($splitPath);

		$folders = implode('/', $splitPath);

		if (empty($folders) || is_dir($folders))
			return;

		mkdir($folders, 0777, true);
	}

	public function disk($name) {
		$this->disk = $name;
		return $this;
	}

	public function put($name, $data) {
		$fullPath = $this->getDisk().'/'.$name;
		$this->makeFolders($fullPath);
		if (file_put_contents($fullPath, $data))
			return $fullPath;
	}
	
	public function get($name) {
		return file_get_contents($this->getDisk().'/'.$name);
	}
	
	public function exists($name) {
		return file_exists($this->getDisk().'/'.$name);
	}
	
	public function path($name) {
		if (self::exists($name))
			return $this->getDisk().'/'.$name;
	}
	
	public function allFiles($path = '') {
		return array_diff(scandir($this->getDisk().'/'.$path), ['..', '.']);
	}

	public function delete($name) {
		$names = is_array($name) ? $name : [$name];
		foreach($names as $name)
			if (!unlink($this->getDisk().'/'.$name))
				return false;
		return true;
	}
}