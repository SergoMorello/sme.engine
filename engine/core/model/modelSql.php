<?php

class modelSql extends modelCore {

    protected static $__query;

    public static function clearQuery() {
		self::$__query = (object)[];
	}

    //SELECT
	public function select(...$data) {
		$data = (is_array($data) && count($data) == 1) ? $data[0] : $data;
		$data = is_array($data) ? $data : [$data];
		foreach($data as $dt) {
			self::$__query->select[] = preg_split("/ as /i",$dt);
		}
		return $this;
	}

	//WHERE
	public function where(...$data) {
        $this->genParams($data, function($a, $b, $c){
            return $a.$b.self::value($c);
        }, self::$__query->where, ['b'=>'=']);
        return $this;
	}

	//WHERE IN
	public function whereIn(...$data) {
        $this->genParams($data, function($a, $b, $c){
            return "`".$a."` IN (".self::values(',', $c).")";
        }, self::$__query->whereIn);
        return $this;
	}

	//LIMIT
	public function limit($limit) {
		self::$__query->limit[0] = $limit;
		return $this;
	}

	//ORDER BY
	public function orderBy(...$data) {
        $this->genParams($data, function($a, $b, $c){
            return "`".$a."` ".strtoupper($c);
        }, self::$__query->orderBy,['c'=>'DESC']);
        return $this;
	}

	//GROUP BY
	public function groupBy($group) {
		self::$__query->groupBy[0] = $group;
		return $this;
	}

	//LEFT JOIN
	public function leftJoin($table, $callback) {
		return $callback(new class($table, $this->getTableName()) extends model{
			private $query, $joinTable, $count, $index;

			public function __construct($table, $curentTable) {
				$this->joinTable = $table;
				$this->setTableName($curentTable);
				$this->count = 0;
				$this->query = [];
				$this->index = isset(self::$__query->leftJoin) ? array_key_last(self::$__query->leftJoin) + 1 : 0;
			}

			private function split() {
				return count($this->query) > 0 ? ' AND ' :'`'.$this->joinTable.'` ON ';
			}

			public function save() {
				self::$__query->leftJoin[$this->index] = implode('',$this->query);
			}

			public function on(...$props) {
                $this->genParams($props, function($a, $b, $c){
                    return $this->split().$a.' '.$b.' '.self::value($c);
                }, $this->query, ['b' => '=']);

				$this->save();

                return $this;
			}
		});
	}

    private function srtSelect() {
		$ret = "";
		if (isset(self::$__query->select) && count(self::$__query->select))
			foreach(self::$__query->select as $select) {
				$ret .= ''.implode(" AS `",$select).(count($select)>1 ? '`' : null);
				if (end(self::$__query->select)!=$select)
					$ret .= ',';
			}
		else
			$ret .= "*";
		return $ret;
	}

	protected function srtWhere() {
		if (isset(self::$__query->where) && count(self::$__query->where))
			return implode(" AND ",self::$__query->where);
	}

	protected function srtWhereIn() {
		if (isset(self::$__query->whereIn) && count(self::$__query->whereIn))
			return implode(" AND ",self::$__query->whereIn);
	}

	protected function strLimit() {
		if (isset(self::$__query->limit[0]) && self::$__query->limit[0])
			return " LIMIT ".self::$__query->limit[0];
	}

	protected function srtOrderBy() {
		if (isset(self::$__query->orderBy) && count(self::$__query->orderBy))
			return " ORDER BY ".implode(", ",self::$__query->orderBy);
	}

	protected function srtGroupBy() {
		if (isset(self::$__query->groupBy) && count(self::$__query->groupBy))
			return " GROUP BY ".self::$__query->groupBy[0];
	}

	protected function srtLeftJoin() {
		if (isset(self::$__query->leftJoin) && count(self::$__query->leftJoin))
			return ' LEFT JOIN '.implode(" LEFT JOIN ",self::$__query->leftJoin);
	}

	protected function strQuerySelect() {
		$ret = "SELECT ";
		
		$ret .= $this->srtSelect();
		
		$ret .= " FROM "."`".$this->getTableName()."`";

		$ret .= $this->srtLeftJoin();
		
		$ret .= ((isset(self::$__query->where) && count(self::$__query->where)) || (isset(self::$__query->whereIn) && count(self::$__query->whereIn))) ? " WHERE " : " ";
		
		$ret .= $this->srtWhere();
		
		$ret .= ((isset(self::$__query->where) && count(self::$__query->where)) && (isset(self::$__query->whereIn) && count(self::$__query->whereIn))) ? " AND " : " ";
		
		$ret .= $this->srtWhereIn();
		
		$ret .= $this->srtGroupBy();
		
		$ret .= $this->srtOrderBy();
		
		$ret .= $this->strLimit();
		
		return trim($ret);
	}
}