<?php
namespace SME\Core\Request\Objects;

class Files extends File {
	private $__count;
	public function __construct($filesInput) {
		if (!isset($filesInput['name']))
			return;
		is_array($filesInput['name']) ? $this->__updateFiles($filesInput) : $this->__updateFile($filesInput);
	}

	public function count() {
		return $this->__count ?? 0;
	}

	public function first() {
		return $this->{0} ?? null;
	}

	private function __updateFile($file) {
		foreach($file as $key => $value) {
			if (empty($value))
				continue;
			$this->{$key} = $value;
		}
	}

	private function __updateFiles($files) {
		foreach($files['name'] as $key => $value) {
			if (empty($value))
				continue;
			$this->$key = new File([
				'name' => $value,
				'type' => $files['type'][$key] ?? '',
				'tmp_name' => $files['tmp_name'][$key] ?? '',
				'error' => $files['error'][$key] ?? '',
				'size' => $files['size'][$key] ?? ''
			]);
			++$this->__count;
		}
	}
}
