<?php
if (stristr(htmlentities($_SERVER['PHP_SELF']), "inc_database.php")) {
	die("<table style='padding: 2px; border: 1px solid #999; background-color: #EEE; font-family: Verdana; font-size: 10px;' align='center'><tr><td><b>Error:</b> This file cannot be opened directly!</td></tr></table>");
}

class database {
	var $dblink;
	var $db_type = "mysql";
	var $host = "localhost";
	var $user = "root";
	var $pass;
	var $name;
	
    function __construct() {
		global $db_type,$db_host,$db_user,$db_pass,$db_name;
		
		$this->type = $db_type;
		$this->host = $db_host;
		$this->user = $db_user;
		$this->pass = $db_pass;
		$this->name = $db_name;
	}
	
	function database() {
		global $db_type,$db_host,$db_user,$db_pass,$db_name;
		
		$this->type = $db_type;
		$this->host = $db_host;
		$this->user = $db_user;
		$this->pass = $db_pass;
		$this->name = $db_name;
	}

	function connect() {
        try {
            $this->dblink = new PDO($this->type.":host=".$this->host.";dbname=".$this->name."", $this->user, $this->pass);
		}catch (PDOException $e) {
            die("<table style='padding: 2px; border: 1px solid #999; background-color: #EEE; font-family: Verdana; font-size: 10px;' align='center'><tr><td><b>Error:</b> ".$e->getMessage()."</td></tr></table>");
		}
	}
	
	function disconnect() {
		$this->dblink = null;
	}

	
	function query($query,$id=false) {
        $result;
        if ($id==true) {
            $result = $this->dblink->prepare($query);
            $result->execute();
            $result=$this->dblink->lastInsertId();
        }else{
            $result = $this->dblink->exec($query);
        }
		return $result;
	}
	
	function get_row($query,$utf8=false) {
		$returned = 0;
		if ($utf8) {$this->dblink->query('SET CHARACTER SET utf8');}
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