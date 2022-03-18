<?php
namespace SME\Core\View;

use SME\Core\Core;
use SME\Core\Config;
use SME\Modules\Cache;
use SME\Modules\Storage;

class Compiler extends Core {

	const dirCompiler = STORAGE.'.cache/compiler/';

	static $_section;

	private static function setConfig() {
		$dir = Config::get('view.compiled') ?? storage_path('.compiler');
		Config::set('cache', [
			'stores' => [
				'__compiler_index' => [
					'driver' => 'file',
					'path' => $dir.'index/'
				]
			]
		]);

		Config::set('storage', [
			'disks' => [
				'__compiler_view' => [
					'driver' => 'local',
					'root' => $dir.'view/'
				]
			]
		]);	
	}

	protected static function genCache($path) {
		self::setConfig();

		$cacheIndex = Cache::store('__compiler_index');
		$storageView = Storage::disk('__compiler_view');
		$hashName = md5($path);
		$hashMod = md5($hashName.filemtime($path));

		$putCache = function() use (&$cacheIndex, &$storageView, &$hashName, &$hashMod, &$path) {
			$cacheIndex->put($hashName, $hashMod);
			$storageView->put($hashName, self::compile(file_get_contents($path)));
		};
		
		if ($cacheIndex->has($hashName)) {
			if ($cacheIndex->get($hashName) == $hashMod) {
				if ($storageView->exists($hashName)) {
					return $storageView->path($hashName);
				}else{
					$putCache();
				}
			}else{
				$putCache();
			}
		}else{
			$putCache();
		}
		return $storageView->path($hashName);
	}

	public function setSection($name, $buffer) {
		self::$_section[$name] = $buffer;
	}

	public function getSection($name) {
		return self::$_section[$name] ?? NULL;
	}

	protected static function compile($buffer) {
		
		$buffer .= PHP_EOL;
		
		$appendBuffer = "";
		
		$append = function($var) use (&$appendBuffer) {
			$appendBuffer .= $var;
		};
		
		$splitArg = function($str) {
			$return = [];
			$args = [];
			
			$str = preg_replace_callback(['/\[(.*)\]/isU', '/([^,\s]*)(\(([^()]|(?2))*\))([\s]*)\{(.*)\}/isU', '/([^,\s]*)\((.*)\)/isU'], function($var) use (&$args) {
				if (!empty($var[0])) {
					$key = '__arg_'.count($args);
					$args[$key] = $var[0];	
					return $key;
				}
			}, $str);

				$return = explode(',', $str);
				foreach($return as $key => $arg) {
					$arg = trim($arg);
					foreach($args as $keyArg => $valueArg) {	
						$return[$key] = str_replace($keyArg, $valueArg, $return[$key]);
					}
				}
				
			return $return;
		};
		
		$buffer = preg_replace_callback('/\@html(.*)@endhtml/isU', function($var) use (&$convertSpec){
			return self::convertSpec($var[1])->encode();
		}, $buffer);
		
		$buffer = preg_replace_callback('/\{\{\-\-(.*)\-\-\}\}/isU', function($var){
			return "";
		}, $buffer);

		$buffer = preg_replace_callback('/\{\{(.*)\}\}/isU', function($var){
			return "<?php e(".$var[1].")->html(); ?>";
		}, $buffer);
		
		$buffer = preg_replace_callback('/\{\!\!(.*)\!\!\}/isU', function($var){
			return "<?php e(".$var[1]."); ?>";
		}, $buffer);
		

		$buffer = preg_replace_callback(['/\@([a-z0-9]{1,})[\r\n|\n|\s]/isU','/\@([^()\n\@]{0,})(\(((?>[^()\n]|(?2))*)\))/isU'], function($var) use (&$append, &$splitArg) {
			
			if (count(self::$arrCompilerView)) {
				$name = $var[1] ?? '';
				$args = (isset($var[3]) && $var[3]) ? $splitArg($var[3]) : [];
				foreach(self::$arrCompilerView as $rule) {
					if ($name == $rule['name']) {
						if (count($args) <= (new \ReflectionFunction($rule['return']))->getNumberOfParameters()) {
							$args[] = &$append;		
							return $rule['return'](...$args);
						}
					}
				}
			}
			
			return $var[0];
		}, $buffer);
		
		$buffer .= $appendBuffer ?? NULL;

		$buffer = self::convertSpec($buffer)->decode();
		
		return $buffer;
	}

	private static function convertSpec($buffer) {
		return new class($buffer){
			private $buffer, $spec;

			public function __construct($buffer) {
				$this->buffer = $buffer;
				$this->spec = [
					[
						'{',
						'}',
						'@',
						':',
						'$',
						'<?',
						'?>'
					],
					[
						'&lcub;',
						'&rcub;',
						'&commat;',
						'&colon;',
						'&dollar;',
						'&lt;?',
						'?&gt;'
					]
				];
			}

			public function encode() {
				return str_replace($this->spec[0], $this->spec[1], $this->buffer);
			}

			public function decode() {
				unset($this->spec[0][5],$this->spec[0][6],$this->spec[1][5],$this->spec[1][6]);
		
				return str_replace($this->spec[1], $this->spec[0], $this->buffer);
			}
		};
	}

	public static function declare($name, $return) {
		self::$arrCompilerView[] = [
			'name' => $name,
			'return' => $return
		];
	}

	public static function flush() {
		self::setConfig();
		$cacheIndex = Cache::store('__compiler_index');
		$storageView = Storage::disk('__compiler_view');
		$storageView->delete($storageView->allFiles());
		return $cacheIndex->flush();
	}
}