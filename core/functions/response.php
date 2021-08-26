<?php
class response {
	private $data;
	function __construct($data,$code) {
		$this->data = $data;
		$this->setCode($code);
	}
	private function setCode($code) {
		header($_SERVER['SERVER_PROTOCOL']." ".$code);
	}
	public function json($arr,$code=200) {
		$arr = (array)$arr;	
		header('Content-Type: application/json');
		$this->setCode($code);
		return json_encode($arr,true);
	}
}
function response($data="",$code=200) {
	$response = new response($data,$code);
	return $data ? $data : $response;
}