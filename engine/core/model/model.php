<?php

class model extends modelSql {

	public function __init() {
		self::clearQuery();
		$this->setTableName($this->table ?? get_called_class());
	}

	public function __sql() {
		return $this->strQuerySelect();
	}

	public function get() {
		return new modelObject(self::$dblink->get_list($this->strQuerySelect()));
	}

	public function first() {
		return new modelObject(self::$dblink->get_row($this->strQuerySelect()));
	}

	public function count() {
		return self::$dblink->get_num($this->strQuerySelect());
	}

	public function find($id) {
		return $this->where('id','=',$id);
	}

	private function getValues($values, $onlyValues = false) {
		$return = [];
		if (!is_array($values))
			return $return;
		foreach($values as $key => $value) {
			$value = (is_object($value) && method_exists($value, 'getValue')) ? $value->getValue() : "'".$value."'";
			if ($onlyValues) 
				$return[] = $value;
			else
				$return[] = $key."=".$value;
		}
		return $return;
	}

	public function save() {
		$modelObject = new modelObject($this);
		$vars = get_object_vars($this);
		unset($vars['table'],$vars['query']);
		
		if (get_object_vars(self::$__query)) {
			$arrQuery = $this->getValues($vars);
			self::$dblink->query("UPDATE `".$this->getTableName()."` SET ".implode(",",$arrQuery)." WHERE ".$this->srtWhere());
		}else{
			$arrQuery = $this->getValues($vars, true);
			$modelObject->id = self::$dblink->query("INSERT INTO `".$this->getTableName()."` (id,".implode(",",array_keys($vars)).") VALUES (NULL,".implode(",",$arrQuery).");",true);
		}
			
		return $modelObject;
	}

	public function update($props) {
		if (is_array($props) && count($props)) {
			$arrQuery = $this->getValues($props);

			self::$dblink->query("UPDATE `".$this->getTableName()."` SET ".implode(",",$arrQuery)." WHERE ".$this->srtWhere());
			return new modelObject($this);
		}
	}

	public function delete() {
		if (self::$__query) {
			$ret = self::$dblink->query("DELETE FROM `".$this->getTableName()."`".(count(self::$__query->where) ? " WHERE " : NULL).$this->srtWhere());
			$this->clearQuery();
			return $ret;
		}
		return false;
	}
}