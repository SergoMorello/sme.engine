<?php
class model extends core {
	private $query;
	public function select(...$data) {
		foreach($data as $dt)
			$this->query->select[] = preg_split("/ as /i",$dt);
		return $this;
	}
	public function where(...$data) {
		if (count($data)==2 && !is_array($data[0]) && !is_array($data[1])) {
			$this->query->where[] = $data[0]."=".'"'.$data[1].'"';
			return $this;
		}
		if (count($data)==3 && !is_array($data[0]) && !is_array($data[1]) && !is_array($data[2])) {
			$this->query->where[] = $data[0].$data[1].'"'.$data[2].'"';
			return $this;
		}
		foreach($data as $dt)
			$this->query->where[] = $dt[0].$dt[1].'"'.$dt[2].'"';
		return $this;
	}
	public function limit($limit) {
		$this->query->limit[0] = $limit;
		return $this;
	}
	private function getTableName() {
		return isset($this->table) ? $this->table : get_called_class();
	}
	private function srtSelect() {
		$ret = "";
		if (count($this->query->select))
			foreach($this->query->select as $select) {
				$ret .= '`'.implode("` AS `",$select).'`';
				if (end($this->query->select)!=$select)
					$ret .= ',';
			}
		else
			$ret .= "*";
		return $ret;
	}
	private function srtWhere() {
		if (count($this->query->where))
			return " WHERE ".implode(" AND ",$this->query->where);
	}
	private function strLimit() {
		if ($this->query->limit[0])
			return " LIMIT ".$this->query->limit[0];
	}
	private function strQuerySelect() {
		$ret = "SELECT ";
		
		$ret .= $this->srtSelect();
		
		$ret .= " FROM "."`".$this->getTableName()."`";
		
		$ret .= $this->srtWhere();
		
		$ret .= $this->strLimit();
		return $ret;
	}
	public function get() {
		return (object)self::$dblink->get_list($this->strQuerySelect());
	}
	public function first() {
		return (object)self::$dblink->get_row($this->strQuerySelect());
	}
	public function count() {
		return self::$dblink->get_num($this->strQuerySelect());
	}
	public function find($id) {
		$this->query->where[] = 'id="'.$id.'"';
		return $this;
	}
	public function save() {
		$vars = get_object_vars($this);
		unset($vars['table'],$vars['query']);
		if ($this->query) {
			$arrQuery = array();
			foreach($vars as $key=>$value)
				$arrQuery[] = $key."='".$value."'";
			self::$dblink->query("UPDATE `".$this->getTableName()."` SET ".implode(",",$arrQuery).$this->srtWhere());
		}else
			$this->id = self::$dblink->query("INSERT INTO `".$this->getTableName()."` (id,".implode(",",array_keys($vars)).") VALUES (NULL,'".implode("','",$vars)."')",true);
		return $this;
	}
	public function delete() {
		if ($this->query)
			return self::$dblink->query("DELETE FROM `".$this->getTableName()."`".$this->srtWhere());
		return false;
	}
}