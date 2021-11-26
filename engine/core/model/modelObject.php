<?php

class modelObject extends modelCore {
    public function __construct($result=[]) {
        $this->setVars($result);
        modelSql::clearQuery();
    }

    private function setVars($result) {
        foreach($result as $key=>$value)
            $this->$key = $value; 
    }

    public function count() {
        return count($this->toArray());
    }

    public function toArray() {
        $array = [];
        foreach(get_object_vars($this) as $key=>$value)
            $array[$key] = $value;
        return $array;
    }
}