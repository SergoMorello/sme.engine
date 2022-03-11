<?php
namespace SME\Core\Model;

use SME\Http\Request;

class ModelMethods extends ModelSql {
	public function __invoke($table) {
		$this->setTableName($table);
		return $this;
	}

	public function __construct() {
		$this->__query = (object)[];
	}

	public function __sql() {
		return $this->strQuerySelect();
	}

	public function get() {
		return new modelObject(self::dblink()->get_list($this->strQuerySelect()));
	}

	public function first() {
		return new modelObject(self::dblink()->get_row($this->strQuerySelect()));
	}

	public function count() {
		return intval(self::dblink()->get_num($this->strQuerySelect()));
	}

	public function find($id) {
		return $this->where('id','=',$id);
	}

	public function paginate($num) {
		$page = Request::route('page');
		$page = $page < 1 ? 1 : $page;
		$count = $this->count();
		$pages = ceil($count / $num);
		$page = $pages >= $page ? $page : $pages;
		$ofsbgn = ($page * $num) - $num;

		$this->limit($ofsbgn, $num);
		$query = self::dblink()->get_list($this->strQuerySelect());
		$countPage = count($query);
		return (new modelObject($query))([
			'paginate' => new Paginate($count, $num, $page, $pages, $countPage)
		]);
	}

	private function getValues($values, $onlyValues = false) {
		$return = [];
		if (!is_array($values))
			return $return;
		foreach($values as $key => $value) {
			if (is_null($value))
				continue;
			$value = self::value($value);
			if ($onlyValues) 
				$return[] = $value;
			else
				$return[] = $key."=".$value;
		}
		return $return;
	}

	public function save() {
		$modelObject = new modelObject($this);
		$vars = $this->getVars($this);
		
		if (get_object_vars($this->__query)) {
			$arrQuery = $this->getValues($vars);
			self::dblink()->query("UPDATE `".$this->getTableName()."` SET ".implode(",",$arrQuery)." WHERE ".$this->srtWhere());
		}else{
			$arrQuery = $this->getValues($vars, true);
			$modelObject->id = self::dblink()->query("INSERT INTO `".$this->getTableName()."` (id,".implode(",",array_keys($vars)).") VALUES (NULL,".implode(",",$arrQuery).");",true);
		}
			
		return $modelObject;
	}

	public function update($props) {
		if (is_array($props) && count($props)) {
			$arrQuery = $this->getValues($props);
			if (count($arrQuery))
				$this->isUpdate = self::dblink()->query("UPDATE `".$this->getTableName()."` SET ".implode(",",$arrQuery)." WHERE ".$this->srtWhere());
			return new modelObject($this);
		}
	}

	public function delete() {
		if ($this->__query) {
			$ret = self::dblink()->query("DELETE FROM `".$this->getTableName()."`".(count($this->__query->where) ? " WHERE " : NULL).$this->srtWhere());
			return $ret;
		}
		return $this;
	}
}