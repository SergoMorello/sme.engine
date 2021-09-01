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

	function connect() {
        try {
            $this->dblink = new PDO($this->type.":host=".$this->host.";dbname=".$this->name, $this->user, $this->pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			if ($this->debug)
				$this->dblink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch (PDOException $e) {
            die("Error: ".$e->getMessage());
		}
	}
	
	function disconnect() {
		$this->dblink = null;
	}

	
	function query($query,$id=false) {
        $result = [];
        if ($id==true) {
            $result = $this->dblink->prepare($query);
            $result->execute();
            $result=$this->dblink->lastInsertId();
        }else
            $result = $this->dblink->exec($query);
		
		return $result;
	}
	
	function get_row($query) {
		$returned = [];
		if ($result = $this->dblink->query($query)) {
			$result->setFetchMode(PDO::FETCH_OBJ);
			$returned = $result->fetch();
		}
		return $returned;
	}
	
	function get_list($query) {
		$returned = array();	
		if ($result = $this->dblink->query($query)) {
			$result->setFetchMode(PDO::FETCH_OBJ);
			while ($row = $result->fetch()) {
				$returned[] = $row;
			}
		}
		return $returned;
	}
	
	function get_num($query) {
		$result = $this->dblink->query($query);
		$num = $result->rowCount();
		return $num;
	}

}
?>