<?php
namespace SME\Core\Model;

class Model extends ModelSql {
	protected $table;
	
	public function __init() {
		self::clearQuery();
		$this->setTableName($this->table ?? get_called_class());
		return $this;
	}

	public static function __sql() {
		return self::model()->strQuerySelect();
	}

	public static function get() {
		return new modelObject(self::dblink()->get_list(self::model()->strQuerySelect()));
	}

	public static function first() {
		return new modelObject(self::dblink()->get_row(self::model()->strQuerySelect()));
	}

	public static function count() {
		return intval(self::dblink()->get_num(self::model()->strQuerySelect()));
	}

	public static function find($id) {
		return self::model()->where('id','=',$id);
	}

	private function getValues($values, $onlyValues = false) {
		$return = [];
		if (!is_array($values))
			return $return;
		foreach($values as $key => $value) {
			$value = self::value($value);
			if ($onlyValues) 
				$return[] = $value;
			else
				$return[] = $key."=".$value;
		}
		return $return;
	}

	public static function save() {
		$obj = self::model();
		$modelObject = new modelObject($obj);
		$vars = get_object_vars($obj);
		unset($vars['table'],$vars['query']);
		
		if (get_object_vars(self::$__query)) {
			$arrQuery = $obj->getValues($vars);
			self::dblink()->query("UPDATE `".$obj->getTableName()."` SET ".implode(",",$arrQuery)." WHERE ".$obj->srtWhere());
		}else{
			$arrQuery = $obj->getValues($vars, true);
			$modelObject->id = self::dblink()->query("INSERT INTO `".$obj->getTableName()."` (id,".implode(",",array_keys($vars)).") VALUES (NULL,".implode(",",$arrQuery).");",true);
		}
			
		return $modelObject;
	}

	public function update($props) {
		$obj = self::model();
		if (is_array($props) && count($props)) {
			$arrQuery = $obj->getValues($props);

			$obj->isUpdate = self::dblink()->query("UPDATE `".$obj->getTableName()."` SET ".implode(",",$arrQuery)." WHERE ".$obj->srtWhere());
			return new modelObject($obj);
		}
	}

	public static function delete() {
		$obj = self::model();
		if (self::$__query) {
			$ret = self::dblink()->query("DELETE FROM `".$obj->getTableName()."`".(count(self::$__query->where) ? " WHERE " : NULL).$obj->srtWhere());
			$obj->clearQuery();
			return $ret;
		}
		return $obj;
	}
}