<?php
namespace App\Models;

use SME\Core\Model\Model;

class Test extends Model {

	// Если нужно переназначить имя таблицы
	protected $table = 'contacts';

	public static function test() {
		return self::where('id',13)->get();
	}
}