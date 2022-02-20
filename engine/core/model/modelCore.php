<?php

class modelCore extends core {
	private static $dblink;
    protected $table;

	public function __construct() {
		self::$dblink = new database(
			config::get('database.dbType'),
			config::get('database.dbHost'),
			config::get('database.dbUser'),
			config::get('database.dbPassword'),
			config::get('database.dbName'),
			config::get('app.debug')
		);
		
		try {
			self::$dblink->connect(true);
		} catch (PDOException $e) {
			if (config::get('app.debug'))
				exceptions::throw('error',['message' => $e->getMessage()]);
			else
				exceptions::throw('error',['message' => 'Connect DB']);
		}
	}

	public function __destruct() {
		if (!is_null(self::$dblink))
			self::$dblink->disconnect();
	}

	protected static function dblink() {
		return self::$dblink;
	}

    protected function getTableName() {
		return config::get('app.dbPrefix').$this->table;
	}

    protected function setTableName($name) {
        $this->table = $name;
    }

	protected function value($value) {
		return (is_object($value) && method_exists($value, 'getValue')) ? $value->getValue() : "'".$value."'";
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