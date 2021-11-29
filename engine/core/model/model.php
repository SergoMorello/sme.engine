<?php

class model extends modelSql {

	public function __init() {
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

	public function find($id) {
		return $this->where('id','=',$id);
	}

	public function save() {
		$modelObject = new modelObject($this);
		$vars = get_object_vars($this);
		unset($vars['table'],$vars['query']);
		
		if (get_object_vars(self::$__query)) {
			$arrQuery = array();
			foreach($vars as $key=>$value)
				$arrQuery[] = $key."='".$value."'";
			self::$dblink->query("UPDATE `".$this->getTableName()."` SET ".implode(",",$arrQuery)." WHERE ".$this->srtWhere());
		}else
			$modelObject->id = self::$dblink->query("INSERT INTO `".$this->getTableName()."` (id,".implode(",",array_keys($vars)).") VALUES (NULL,'".implode("','",$vars)."');",true);
			
		return $modelObject;
	}

	public function update($props) {
		if (is_array($props) && count($props)) {
			$arrQuery = array();
			foreach($props as $key=>$value)
				$arrQuery[] = $key."='".$value."'";

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