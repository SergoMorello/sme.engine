<?php
class viewCore extends core {
	static $_sectionObj,$_section;
	protected function genCache($view,$dirV) {
		//return 1;
		$cacheViewName = md5($dirV.$view);
		$cacheViewIndex = core::$dirCache.".index";
		$md5Hash = md5_file($dirV.$view.".php");
		$createIndex = function($cacheViewIndex,$obj) {
			if (file_put_contents($cacheViewIndex,json_encode((is_callable($obj) ? $obj() : $obj))))
				return true;
		};
		if (file_exists($cacheViewIndex)) {
			$cacheIndex = json_decode(file_get_contents($cacheViewIndex));
			$checkIndex = function($cacheIndex,$cacheViewName) {
				foreach($cacheIndex as $key=>$index) {
					if ($index->name==$cacheViewName)
						return $key;
				}
				return -1;
			};
			$keyIndex = $checkIndex($cacheIndex,$cacheViewName);
			if ($keyIndex>=0) {
				if ($cacheIndex[$keyIndex]->hash!=$md5Hash) {
					$cacheIndex[$keyIndex]->hash = $md5Hash;
					return $createIndex($cacheViewIndex,$cacheIndex);
				}
			}else{
				$cacheIndex[] = ['name'=>$cacheViewName,'hash'=>$md5Hash];
				return $createIndex($cacheViewIndex,$cacheIndex);
			}
		}else
			return $createIndex($cacheViewIndex,function() use ($cacheViewName,$md5Hash) {
				return [['name'=>$cacheViewName,'hash'=>$md5Hash]];
			});
	}
	public function setSection($name,$buffer) {
		self::$_section[$name] = $buffer;
	}
	public function getSection($name) {
		return self::$_section[$name];
	}
	protected function compiller($buffer) {
		$append = "";
		
		$splitArg = function($str) {
			return explode(',',$str);
		};
		
		$buffer = preg_replace_callback("/\{\{(.*)\}\}/", function($var){
			return "<?php echo ".$var[1]."; ?>";
		}, $buffer);
		
		///\@([a-z0-9]{1,})(\((\'|\"|)(.*)\\3\)|\(\)|)\s/isU
		$buffer = preg_replace_callback("/\@([a-z0-9]{1,})\((.*)\)|\@([a-z0-9]{1,})(?=\b|[\s-])/isU", function($var) use (&$append,&$splitArg) {
			$arg = $var[2] ? $splitArg($var[2]) : NULL;
			
			if ($var[3]=="php")
				return "<?php ";
			
			if ($var[3]=="endphp")
				return "?>";
			
			if ($var[3]=="endfor" || $var[3]=="endforeach" || $var[3]=="endif")
				return "<?php } ?>";
			
			if ($var[3]=="endsection")
				return "<?php ob_end_clean(); ?>";
			
			
			if ($var[1]=="for" || $var[1]=="foreach" || $var[1]=="if")
				return "<?php ".$var[1]."(".$var[2].") { ?> ";
			
			
			if ($var[1]=="section" && $arg[0] && !$arg[1])
				return "<?php ob_start(function(\$b){\$this->setSection(".$arg[0].",\$b);}); ?>";
			
			if ($var[1]=="section" && $arg[0] && $arg[1]) {
				return "<?php \$this->setSection(".$arg[0].",".$arg[1]."); ?>";
			}
			
			if ($var[1]=="yield")
				return "<?php echo \$this->getSection(".$arg[0]."); ?>";	
			
			if ($var[1]=="extends") {
				$varSection = str_replace(['\'','\"'],'',$arg[0]);
				$append .= "<?php ob_end_clean(); echo \$this->addView(".$arg[0]."); echo \$this->getSection('__view.".$varSection."'); ?>";
				return "<?php ob_start(function(\$b){self::\$_section['__view.".$varSection."']=\$b;}); ?>";
			}
			return $var[0];
		}, $buffer);
		
		
		$buffer .= $append ?? NULL;
		
		return $buffer;
	}
}