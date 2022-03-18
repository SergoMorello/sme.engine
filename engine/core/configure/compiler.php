<?php
namespace SME\Core\Configure;

use SME\Core\View\Compiler;

// Compiler

// PHP
Compiler::declare('php',function() {
	return "<?php";
});

// END PHP
Compiler::declare('endphp',function() {
	return "?>";
});

// IF
Compiler::declare('if',function($arg) {
	return "<?php if(".$arg.") { ?>";
});

// END IF
Compiler::declare('endif',function() {
	return "<?php } ?>";
});

// FOR
Compiler::declare('for',function($arg) {
	return "<?php for(".$arg.") { ?>";
});

// END FOR
Compiler::declare('endfor',function() {
	return "<?php } ?>";
});

// FOREACH
Compiler::declare('foreach',function($arg) {
	return "<?php foreach(".$arg.") { ?>";
});

// END FOREACH
Compiler::declare('endforeach',function() {
	return "<?php } ?>";
});

// ELSE
Compiler::declare('else',function() {
	return "<?php }else{ ?>";
});

// SECTION SINGLE
Compiler::declare('sectiond',function($arg) {
	return "<?php ob_start(function(\$b){\$this->setSection(".$arg.",\$b);}); ?>";
});

// SECTION
Compiler::declare('section',function($arg1, $arg2, $append = null) {
	if (is_null($append))
		return "<?php ob_start(function(\$b){\$this->setSection(".$arg1.",\$b);}); ?>";	
	else{
		return "<?php \$this->setSection(".$arg1.",".$arg2."); ?>";
	}
		
});

// END SECTION
Compiler::declare('endsection',function() {
	return "<?php ob_end_clean(); ?>";
});

// YIELD
Compiler::declare('yield',function($arg) {
	return "<?php echo \$this->getSection(".$arg."); ?>";
});

// EXTENDS
Compiler::declare('extends',function($arg, $append) {
	$varSection = str_replace(['\'','\"'],'',$arg);
	
	$append("<?php ob_end_clean(); echo \$this->addView(".$arg.", [], \$__system); echo \$this->getSection('__view.".$varSection."'); ?>");
	
	return "<?php ob_start(function(\$b){self::\$__section['__view.".$varSection."']=\$b;}); ?>";
});

// INCLUDE
Compiler::declare('include',function($arg1, $arg2) {
	$arg2 = is_callable($arg2) ? '[]' : $arg2;
	
	return "<?php echo \$this->addView(".$arg1.", ".$arg2.", \$__system); ?>";
});

// LANG
Compiler::declare('lang',function($arg1, $arg2) {
	$arg2 = is_callable($arg2) ? '[]' : $arg2;
	return "<?php e(trans(".$arg1.", ".$arg2.")); ?>";
});