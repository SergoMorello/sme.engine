<?php
namespace SME\Core\Request\Objects;

use SME\Modules\Storage;

class File {
	protected $name, $type, $tmp_name, $error, $size;

	public function __construct($props) {
		foreach($props as $key => $value)
			$this->$key = $value;
	}

	public function getData() {
		return file_get_contents($this->tmp_name);
	}

	public function getHash($algo = 'md5') {
		return hash_file($algo, $this->tmp_name);
	}

	public function getExtension() {
		return substr($this->name, strrpos($this->name, '.') + 1);
	}

	public function getName() {
		return $this->name;
	}

	public function getType() {
		return $this->type;
	}

	public function getPath() {
		return $this->tmp_name;
	}

	public function getError() {
		return $this->error;
	}

	public function getSize() {
		return $this->size;
	}

	public function store($path = '', $disk = '') {
		return Storage::disk($disk)->put($path.'/'.$this->name, $this->getData());
	}

	public function storeAs($path, $name, $disk = '') {
		return Storage::disk($disk)->put($path.'/'.$name, $this->getData());
	}
}