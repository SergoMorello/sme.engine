<?php
namespace SME\Core\Model;

class ModelSql extends ModelCore {

    protected $__query;

	protected function getVars() {
		$vars = get_object_vars($this);
		if (isset($vars['__query']))
			unset($vars['__query']);
		return $vars;
	}

    //SELECT
	public function select(...$data) {
		$data = (is_array($data) && count($data) == 1) ? $data[0] : $data;
		$data = is_array($data) ? $data : [$data];
		foreach($data as $dt) {
			$this->__query->select[] = preg_split("/ as /i",$dt);
		}
		return $this;
	}

	//WHERE
	public function where(...$data) {
        $this->genParams($data, function($a, $b, $c){
            return $a.$b.self::value($c);
        }, $this->__query->where, ['b'=>'=']);
        return $this;
	}

	//WHERE IN
	public function whereIn(...$data) {
        $this->genParams($data, function($a, $b, $c){
            return "`".$a."` IN (".self::values(',', $c).")";
        }, $this->__query->whereIn);
        return $this;
	}

	//LIMIT
	public function limit($limit) {
		$this->__query->limit[0] = $limit;
		return $this;
	}

	//ORDER BY
	public function orderBy(...$data) {
        $this->genParams($data, function($a, $b, $c){
            return "`".$a."` ".strtoupper($c);
        }, $this->__query->orderBy,['c'=>'DESC']);
        return $this;
	}

	//GROUP BY
	public function groupBy($group) {
		$this->__query->groupBy[0] = $group;
		return $this;
	}

	//LEFT JOIN
	public function leftJoin($table, $callback) {
		return $callback(new class($table, $this->getTableName()) extends ModelMethods {
			private $query, $joinTable, $count, $index;

			public function __construct($table, $curentTable) {
				$this->joinTable = $table;
				$this->setTableName($curentTable);
				$this->count = 0;
				$this->query = [];
				$this->index = isset($this->__query->leftJoin) ? array_key_last($this->__query->leftJoin) + 1 : 0;
			}

			private function split() {
				return count($this->query) > 0 ? ' AND ' :'`'.$this->joinTable.'` ON ';
			}

			public function joinSave() {
				$this->__query->leftJoin[$this->index] = implode('',$this->query);
			}

			public function on(...$props) {
                $this->genParams($props, function($a, $b, $c){
                    return $this->split().$a.' '.$b.' '.self::value($c);
                }, $this->query, ['b' => '=']);

				$this->joinSave();

                return $this;
			}
		});
	}

    private function srtSelect() {
		$ret = "";
		if (isset($this->__query->select) && count($this->__query->select))
			foreach($this->__query->select as $select) {
				$ret .= ''.implode(" AS `",$select).(count($select)>1 ? '`' : null);
				if (end($this->__query->select)!=$select)
					$ret .= ',';
			}
		else
			$ret .= "*";
		return $ret;
	}

	protected function srtWhere() {
		if (isset($this->__query->where) && count($this->__query->where))
			return implode(" AND ",$this->__query->where);
	}

	protected function srtWhereIn() {
		if (isset($this->__query->whereIn) && count($this->__query->whereIn))
			return implode(" AND ",$this->__query->whereIn);
	}

	protected function strLimit() {
		if (isset($this->__query->limit[0]) && $this->__query->limit[0])
			return " LIMIT ".$this->__query->limit[0];
	}

	protected function srtOrderBy() {
		if (isset($this->__query->orderBy) && count($this->__query->orderBy))
			return " ORDER BY ".implode(", ",$this->__query->orderBy);
	}

	protected function srtGroupBy() {
		if (isset($this->__query->groupBy) && count($this->__query->groupBy))
			return " GROUP BY ".$this->__query->groupBy[0];
	}

	protected function srtLeftJoin() {
		if (isset($this->__query->leftJoin) && count($this->__query->leftJoin))
			return ' LEFT JOIN '.implode(" LEFT JOIN ",$this->__query->leftJoin);
	}

	protected function strQuerySelect() {
		$ret = "SELECT ";
		
		$ret .= $this->srtSelect();
		
		$ret .= " FROM "."`".$this->getTableName()."`";

		$ret .= $this->srtLeftJoin();
		
		$ret .= ((isset($this->__query->where) && count($this->__query->where)) || (isset($this->__query->whereIn) && count($this->__query->whereIn))) ? " WHERE " : " ";
		
		$ret .= $this->srtWhere();
		
		$ret .= ((isset($this->__query->where) && count($this->__query->where)) && (isset($this->__query->whereIn) && count($this->__query->whereIn))) ? " AND " : " ";
		
		$ret .= $this->srtWhereIn();
		
		$ret .= $this->srtGroupBy();
		
		$ret .= $this->srtOrderBy();
		
		$ret .= $this->strLimit();
		
		return trim($ret);
	}
}