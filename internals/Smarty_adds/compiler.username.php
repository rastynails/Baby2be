<?php

function smarty_compiler_username( $tag_attrs, Core_LayoutCompiler $compiler )
{
	$attrs = Core_LayoutCompiler::parseTagAttrs($tag_attrs, $compiler);
	
	if ( !isset($attrs['profile_id']) ) {
		$compiler->_syntax_error('tpl function {username} missing attribute "profile_id"', E_USER_ERROR);
	}
	
	$php_output = "app_Profile::username({$attrs['profile_id']})";
	
	if ( !isset($attrs['href']) ) {
		$php_output =
<<<EOT
'<a href="' . app_Profile::href({$attrs['profile_id']}) . '">' . $php_output . '</a>'
EOT;
	}
	elseif ( $attrs['href'] != false ) {
		$php_output =
<<<EOT
'<a href="' . {$attrs['href']} . '">' . $php_output . '</a>'
EOT;
	}
	
	return !isset($attrs['assign'])
		? "echo $php_output;\n"
		: "\$this->_tpl_vars[{$attrs['assign']}] = $php_output;\n";
}
