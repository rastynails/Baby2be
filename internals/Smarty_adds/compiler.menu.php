<?php

function smarty_compiler_menu( $tag_attrs, Core_LayoutCompiler $Compiler )
{
	$attrs = $Compiler->_parse_attrs($tag_attrs);
	
	if ( isset($attrs['type']) ) {
		if ( DEV_MODE && !preg_match("~$Compiler->_qstr_regexp~", $attrs['type']) ) {
			$Compiler->_syntax_error('{menu attribute `type` can take only a quoted string literal value (hardcoded)', E_USER_WARNING, __FILE__, __LINE__);
		}
		eval("\$menu_type = {$attrs['type']};");
	}
	else {
		$menu_type = 'list';
	}
	
	$Layout = SK_Layout::getInstance();
	$theme_dir = SK_Layout::theme_dir();
	
	$dir_sep = DIRECTORY_SEPARATOR;
	$tpl_file = "plugins{$dir_sep}menu-$menu_type.tpl";
	
	// themed template overwriting
	if ( $Layout->template_exists($theme_dir . $tpl_file) ) {
		$tpl_file = $theme_dir . $tpl_file;
	}
	
	$php_embeded_out = $Compiler->_compile_include_tag("$tag_attrs file='$tpl_file' tpl_self='$tpl_file'");
	
	$_length = strlen($php_embeded_out);
	
	return substr($php_embeded_out, 5, $_length-7);
}

