<?php
namespace SME\Core\Daten;

class DatenObject {
	private $format;

	public function __construct($time = null) {
		$this->timestamp = $time ?? time();
		$this->format = DATE_RFC822;
		foreach(getdate($this->timestamp) as $key => $value)
			$this->{$key} = $value;
	}

	public function __toString() {
		return $this->toDateTimeString();
	}

	public function toArray() {
		$return = [];
		foreach(get_object_vars($this) as $key => $value)
			$return[$key] = $value;
		return $return;
	}

	public function getValue() {
		return "'".$this->format('Y-m-d H:i:s')->toDateTimeString()."'";
	}

	public function toDateTimeString() {
		return date($this->format, $this->timestamp);
	}

	public function format($format) {
		$this->format = $format;
		return $this;
	}
}