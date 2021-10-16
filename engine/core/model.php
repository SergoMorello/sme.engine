<?php
class model extends core {
	private $query;
	//SELECT
	public function select(...$data) {
		foreach($data as $dt)
			$this->query->select[] = preg_split("/ as /i",$dt);
		return $this;
	}
	//WHERE
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
	//WHERE IN
	public function whereIn(...$data) {
		$gen = function($data) {
			$this->query->whereIn[] = "`".$data[0]."` IN (\"".implode("\",\"",$data[1])."\")";
		};
		if (count($data)==2 && !is_array($data[0]) && is_array($data[1]))
			$gen($data);
		else
			foreach($data as $dt)
				$gen($dt);
		return $this;
	}
	//LIMIT
	public function limit($limit) {
		$this->query->limit[0] = $limit;
		return $this;
	}
	//ORDER BY
	public function orderBy(...$data) {
		if (count($data)==1 && !is_array($data[0])) {
			$this->query->orderBy[] = "`".$data[0]."` DESC";
			return $this;
		}
		if (count($data)==2 && !is_array($data[0]) && !is_array($data[1])) {
			$this->query->orderBy[] = "`".$data[0]."` ".strtoupper($data[1]);
			return $this;
		}
		foreach($data as $dt)
			$this->query->orderBy[] = "`".$dt[0]."` ".($dt[1] ? strtoupper($dt[1]) : "DESC");
		return $this;
	}
	//GROUP BY
	public function groupBy($group) {
		$this->query->groupBy[0] = $group;
		return $this;
	}
	private function getTableName() {
		return isset($this->table) ? $this->table : get_called_class();
	}
	private function srtSelect() {
		$ret = "";
		if (isset($this->query->select) && count($this->query->select))
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
		if (isset($this->query->where) && count($this->query->where))
			return implode(" AND ",$this->query->where);
	}
	private function srtWhereIn() {
		if (isset($this->query->whereIn) && count($this->query->whereIn))
			return implode(" AND ",$this->query->whereIn);
	}
	private function strLimit() {
		if (isset($this->query->limit[0]) && $this->query->limit[0])
			return " LIMIT ".$this->query->limit[0];
	}
	private function srtOrderBy() {
		if (isset($this->query->orderBy) && count($this->query->orderBy))
			return " ORDER BY ".implode(", ",$this->query->orderBy);
	}
	private function srtGroupBy() {
		if (isset($this->query->groupBy) && count($this->query->groupBy))
			return " GROUP BY ".$this->query->groupBy[0];
	}
	private function strQuerySelect() {
		$ret = "SELECT ";
		
		$ret .= $this->srtSelect();
		
		$ret .= " FROM "."`".$this->getTableName()."`";
		
		$ret .= ((isset($this->query->where) && count($this->query->where)) || (isset($this->query->whereIn) && count($this->query->whereIn))) ? " WHERE " : " ";
		
		$ret .= $this->srtWhere();
		
		$ret .= ((isset($this->query->where) && count($this->query->where)) && (isset($this->query->whereIn) && count($this->query->whereIn))) ? " AND " : " ";
		
		$ret .= $this->srtWhereIn();
		
		$ret .= $this->srtGroupBy();
		
		$ret .= $this->srtOrderBy();
		
		$ret .= $this->strLimit();
		return $ret;
	}
	private function clearQuery() {
		unset($this->query);
	}
	public function get() {
		$ret = (object)self::$dblink->get_list($this->strQuerySelect());
		$this->clearQuery();
		return $ret;
	}
	public function first() {
		$ret = (object)self::$dblink->get_row($this->strQuerySelect());
		$this->clearQuery();
		return $ret;
	}
	public function count() {
		$ret = self::$dblink->get_num($this->strQuerySelect());
		$this->clearQuery();
		return $ret;
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
			self::$dblink->query("UPDATE `".$this->getTableName()."` SET ".implode(",",$arrQuery)." WHERE ".$this->srtWhere());
		}else
			$this->id = self::$dblink->query("INSERT INTO `".$this->getTableName()."` (id,".implode(",",array_keys($vars)).") VALUES (NULL,'".implode("','",$vars)."')",true);
		$this->clearQuery();
		return $this;
	}
	public function delete() {
		if ($this->query) {
			$ret = self::$dblink->query("DELETE FROM `".$this->getTableName()."`".(count($this->query->where) ? " WHERE " : NULL).$this->srtWhere());
			$this->clearQuery();
			return $ret;
		}
		return false;
	}
}