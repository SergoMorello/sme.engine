<?php
namespace SME\Core\Model;

class ModelObject extends ModelCore implements \ArrayAccess, \Countable {
	private $__paginate, $__count, $__lastKey;

	public function __construct($result = []) {
		$this->__count = 0;
		$this->setVars($result);
	}

	public function __invoke($params) {
		$this->__paginate = $params['paginate'] ?? null;
		return $this;
	}

	public function offsetSet($offset, $value) {
        if (is_null($offset)) {
			$this->__lastKey = $this->getLastKey() + 1;
            $this->{$this->__lastKey} = $value;
        } else {
            $this->{$offset} = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->{$offset});
    }

    public function offsetUnset($offset) {
        unset($this->{$offset});
    }

    public function offsetGet($offset) {
        return isset($this->{$offset}) ? $this->{$offset} : null;
    }

	public function getLastKey() {
		return is_null($this->__lastKey) ? null : intval($this->__lastKey);
	}

	private function setVars($result) {
		if (is_array($result) || is_object($result)) {
			foreach($result as $key => $value) {
				$this->$key = $value;
				$this->__lastKey = $key;
				++$this->__count;
			}	
		}
	}
	
	private function getVars() {
		$vars = get_object_vars($this);
		unset($vars['__paginate'], $vars['__count'], $vars['__lastKey']);
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