<?php
class database {
	private $type,$host,$user,$pass,$name,$debug;
	
    function __construct($db_type='mysql',$db_host='127.0.0.1',$db_user='root',$db_pass=NULL,$db_name=NULL,$db_debug=false) {
		$this->type = $db_type;
		$this->host = $db_host;
		$this->user = $db_user;
		$this->pass = $db_pass;
		$this->name = $db_name;
		$this->debug = $db_debug;
	}

	public function connect($next=false) {
        try {
            $this->dblink = new PDO($this->type.":host=".$this->host.";dbname=".$this->name.";charset=UTF8", $this->user, $this->pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"));
			if ($this->debug)
				$this->dblink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch (PDOException $e) {
			if($next)
				throw new PDOException($e);
			else
				die("Error: ".$e->getMessage());
		}
	}
	
	public function disconnect() {
		$this->dblink = null;
	}

	public function query($query,$id=false) {
        $result = [];
        if ($id==true) {
            $result = $this->dblink->prepare($query);
            $result->execute();
            $result=$this->dblink->lastInsertId();
        }else
            $result = $this->dblink->exec($query);
		
		return $result;
	}
	
	public function get_row($query) {
		$returned = [];
		if ($result = $this->dblink->query($query)) {
			$result->setFetchMode(PDO::FETCH_OBJ);
			$returned = $result->fetch();
		}
		return $returned;
	}
	
	public function get_list($query) {
		$returned = [];	
		if ($result = $this->dblink->query($query)) {
			$result->setFetchMode(PDO::FETCH_OBJ);
			while ($row = $result->fetch()) {
				$returned[] = $row;
			}
		}
		return $returned;
	}
	
	public function get_num($query) {
		$result = $this->dblink->query($query);
		$num = $result->rowCount();
		return $num;
	}

}
?>