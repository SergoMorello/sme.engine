<?php

class modelCore extends core {

    private $table;

    protected function getTableName() {
		return $this->table;
	}

    protected function setTableName($name) {
        $this->table = $name;
    }

    protected function genParams($params, $callback, &$data, $default=[]) {
        if (!is_array($params))
            return;

        $gen = function($arrParams) use (&$callback, &$default) {
            if (count($arrParams)==1) {
                return $callback($arrParams[0], $default['b'] ?? null, $default['c'] ?? null);
            }

            if (count($arrParams)==2) {
                return $callback($arrParams[0], $default['b'] ?? null, $arrParams[1]);
            }
            
            if (count($arrParams)==3) {
                return $callback($arrParams[0], $arrParams[1], $arrParams[2]);
            }
        };
        
        if (isset($params[0]) && is_array($params[0])) {
            $params = is_array($params[0][0]) ? $params[0] : $params;
            foreach($params as $param)
                $data[] = $gen($param);
            return $data;
        }
       
        return $data[] = $gen($params);
    }
}