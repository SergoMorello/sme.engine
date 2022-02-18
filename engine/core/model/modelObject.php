<?php

class modelObject extends modelCore {
	public function __construct($result=[]) {
		$this->setVars($result);
		modelSql::clearQuery();
	}

	private function setVars($result) {
		if (is_array($result) || is_object($result)) {
			foreach($result as $key => $value)
				$this->$key = $value; 
		}
	}

	public function count() {
		return count($this->toArray()) - 1;
	}

	public function toArray() {
		$array = [];
		foreach(get_object_vars($this) as $key => $value) {
			if (is_object($value) && method_exists($value, 'getValue'))
				$value = $value->getValue();
			$array[$key] = $value;
		}
		return $array;
	}
}