<?php
namespace SME\Core\Model;

use SME\Core\Core;

class modelCore extends Core {
	private static $dblink;
    protected $table;

	public function __construct() {
		if (!is_null(self::$dblink))
			return;
		self::$dblink = new database(
			Config::get('database.dbType'),
			Config::get('database.dbHost'),
			Config::get('database.dbUser'),
			Config::get('database.dbPassword'),
			Config::get('database.dbName'),
			Config::get('app.debug')
		);
		
		try {
			self::$dblink->connect(true);
		} catch (PDOException $e) {
			if (Config::get('app.debug'))
				Exception::throw('error',['message' => $e->getMessage()]);
			else
				Exception::throw('error',['message' => 'Connect DB']);
		}
	}

	public static function __close() {
		if (!is_null(self::$dblink))
			self::$dblink->disconnect();
	}

	protected static function dblink() {
		return self::$dblink;
	}

    protected function getTableName() {
		return Config::get('app.dbPrefix').$this->table;
	}

    protected function setTableName($name) {
        $this->table = $name;
    }

	protected function value($value) {
		return (is_object($value) && method_exists($value, 'getValue')) ? $value->getValue() : "'".$value."'";
	}

	protected function values($split, $values) {
		$return = [];
		foreach($values as $value)
			$return[]= self::value($value);
		return implode($split, $return);
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