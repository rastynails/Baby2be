<?php

class Core_JSCompiler
{
	
	public static function compileFormJSHandler( SK_Form $form, Core_LayoutCompiler $compiler )
	{
		$class_name = get_class($form);
		$prototype = $form->js_prototype();
		
		$js_contents =
<<<EOT
function $class_name(auto_id) {
	this.DOMConstruct('$class_name', auto_id);
}
$class_name.prototype = new SK_FormHandler($prototype);\n
EOT;
		$form->frontend_data['js_class'] = $class_name;
		
		$_params = array(
			'filename'	=>	$compiler->get_external_auto_src($class_name).'.js',
			'contents'	=>	$js_contents,
			'create_dirs' => true
		);
	    require_once SMARTY_CORE_DIR.'core.write_file.php';
        $instance = SK_Layout::getInstance();
	    smarty_core_write_file($_params, $instance);
	    
	    $form->frontend_data['js_file'] = $compiler->get_external_auto_src($class_name, null, true).'.js';
	}
	
}
