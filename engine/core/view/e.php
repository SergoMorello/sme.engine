<?php
namespace SME\Core\View;

class e {
	private $var, $echo;

	public function __construct($var) {
		$this->var = $var;
		$this->echo = $this->getVar();
	}

	public function __destruct() {
		echo $this->echo;
	}

	public function html() {
		if (is_object($this->var) && method_exists($this->var, '__toString' ))
			return $this->echo = (string)$this->var;
		$this->echo = htmlspecialchars($this->getVar());
	}

	public function getVar() {
		return (is_array($this->var) || is_object($this->var)) ? json_encode($this->var) : $this->var;
	}
}