<?php
namespace SME\Core\Model;

class ModelObject extends ModelCore {
	private $__paginate, $__count;

	public function __construct($result = []) {
		$this->__count = count((array)$result);
		$this->setVars($result);
	}

	public function __invoke($params) {
		$this->__paginate = $params['paginate'] ?? null;
		return $this;
	}

	private function setVars($result) {
		if (is_array($result) || is_object($result)) {
			foreach($result as $key => $value)
				$this->$key = $value; 
		}
	}

	private function getVars() {
		$vars = get_object_vars($this);
		unset($vars['__paginate'], $vars['__count']);
		return $vars;
	}

	public function links($view = null) {
		if (!is_null($this->__paginate))
			return $this->__paginate->__init($view, $this->count());
	}

	public function first() {
		$vars = $this->getVars();
		return array_shift($vars) ?? null;
	}

	public function count() {
		return $this->__count;
	}

	public function toArray() {
		$array = [];	
		foreach($this->getVars() as $key => $value) {
			if (is_object($value) && method_exists($value, 'getValue'))
				$value = $value->getValue();
			$array[$key] = $value;
		}
		return $array;
	}
}