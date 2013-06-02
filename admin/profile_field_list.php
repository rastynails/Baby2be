<?php

$file_key = 'prof_field_list';

require_once( '../internals/Header.inc.php' );

$active_tab = ( @$_GET['f_page'] ) ? @$_GET['f_page'] : 'all';

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile_field.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

$profile_field_list	= new AdminProfileField();

$frontend = new AdminFrontend();

$admin_config = new adminConfig();

require_once( 'inc.admin_menu.php' );
//Get and adapt input data

// delete fields
if ( @$_POST['del_field'] )
{
	if ( @$_POST['field_id'] )
		foreach ( @$_POST['field_id'] as $value )
		{
			$field_info = $profile_field_list->getFieldInfo( $value );
			if ( !$field_info['base_field'] )
			{
				$profile_field_list->delField( $value );
				$frontend->RegisterMessage( "`<code>{$field_info['name']}</code>` field has been deleted" );
			}
			else
			{
				$frontend->RegisterMessage( "You can not delete base field `<code>{$field_info['name']}</code>`", 'error' );	
			}
		}
	else
		$frontend->RegisterMessage( "Please, select field", 'notice' );		
	
	redirect( $_SERVER['REQUEST_URI'] );
}

// update section
if ( @$_POST['mov_field_section'] )
{
	if ( @$_POST['field_id'] )
	{
		foreach ( @$_POST['field_id'] as $value)	
		{
			$field_info = $profile_field_list->getFieldInfo( $value );
			if ( $profile_field_list->changeFieldSection( $value, @$_POST['new_field_section'] ) )
				$frontend->RegisterMessage( "`<code>{$field_info['name']}</code>` field was moved to `<code>".SK_Language::section('profile_fields')->section('section')->cdata(@$_POST['new_field_section'])."</code>` section" );
			else
				$frontend->RegisterMessage( "`<code>{$field_info['name']}</code>` field not moved", 'notice' );				
		}
	}
	else
		$frontend->RegisterMessage( "Select field", 'notice' );		
	
	redirect( $_SERVER['REQUEST_URI'] );
}

// change field page
if ( @$_POST['mov_field_page'] )
{
	if ( @$_GET['f_page'] )
	{
		if ( @$_POST['field_id'] )		
		{
			foreach( @$_POST['field_id'] as $value )			
			{
				$field_info = $profile_field_list->getFieldInfo( $value );
				switch( $profile_field_list->changeFieldPage( $value, @$_GET['f_page'], @$_POST['new_field_page'] ) )
				{
					case -2:
						$frontend->RegisterMessage( "You can not move last `<code>{$field_info['name']}</code>` field from not last page", 'notice' );
						break;
					case -1:
						$frontend->RegisterMessage( "Incorrect page number `<code>{$_POST['new_field_page']}</code>` for page type `<code>{$_GET['f_page']}</code>`", 'error' );
						break;
					case 0:
						$frontend->RegisterMessage( "`<code>{$field_info['name']}</code>` field not changed", 'notice' );
						break;
					default:
						$frontend->RegisterMessage( "`<code>{$field_info['name']}</code>` field moved to `<code>".SK_Language::section('profile_fields')->section('page_'.@$_GET['f_page'])->cdata(@$_POST['new_field_page'])."</code>` of `<code>{$_GET['f_page']}</code>` page" );
						break;				
				}
			}
		}
		else 
		{
			$frontend->RegisterMessage( "Select field", 'notice' );			
		}
	}
	else 
	{
		$frontend->RegisterMessage( "Select field type", 'notice' );		
	}
	
	redirect( $_SERVER['REQUEST_URI'] );
}

// change section order
if ( @$_GET['mov_sec_order'] )
{
	controlAdminGETActions();
	
	if ( @$_GET['section_id'] )
		switch ( $profile_field_list->changeSectionOrder( @$_GET['section_id'], @$_GET['mov_sec_order'] ) )
		{
			case -1:
				$frontend->RegisterMessage( "Section `<code>".SK_Language::section('profile_fields')->section('section')->cdata(@$_GET['section_id'])."</code>` not changed. Undefined action.", 'error' );
				break;
			case -2:
				$frontend->RegisterMessage( "Section `<code>".SK_Language::section('profile_fields')->section('section')->cdata(@$_GET['section_id'])."</code>` not changed. Undefined new section order.", 'notice' );
				break;
			case 0:
				$frontend->RegisterMessage( "Section `<code>".SK_Language::section('profile_fields')->section('section')->cdata(@$_GET['section_id'])."</code>` not changed", 'notice' );		
				break;
			default:
				$frontend->RegisterMessage( "Section `<code>".SK_Language::section('profile_fields')->section('section')->cdata(@$_GET['section_id'])."</code>` moved" );		
				break;
		}
	else
		$frontend->RegisterMessage( "Can not move unknown section", 'error' );		
	
	redirect( $_SERVER['PHP_SELF'] );
}

// change field order
if ( @$_GET['mov_field_order'] )
{
	controlAdminGETActions();
	
	if ( @$_GET['field_id'] )
	{
		$field_info = $profile_field_list->getFieldInfo( @$_GET['field_id'] );
		if ( $profile_field_list->changeFieldOrder( @$_GET['field_id'], @$_GET['mov_field_order'] ) )
			$frontend->RegisterMessage( "`<code>{$field_info['name']}</code>` field moved" );		
		else
			$frontend->RegisterMessage( "`<code>{$field_info['name']}</code>` field not changed", 'notice' );			
	}
	else
	{
		$frontend->RegisterMessage( "Can not move unknown field", 'error' );		
	}
	
	redirect( $_SERVER['PHP_SELF'] );
}

// delete field from page
if ( @$_POST['del_field_from_page'] )
{
	if ( @$_GET['f_page'] )
	{
		if ( @$_POST['field_id'] )
		{
			foreach ( @$_POST['field_id'] as $value )
			{
				$field_info	= $profile_field_list->getFieldInfo( $value );
				switch ( $profile_field_list->deleteFieldFromPage( $value, @$_GET['f_page'], @$_GET['page_num'] ) )
				{
					case -2:
						$frontend->RegisterMessage( "Can not delete base field `<code>{$field_info['name']}</code>` from page", 'notice' );
						break;
					case -1:
						$frontend->RegisterMessage( "You can not delete last `<code>{$field_info['name']}</code>` field from not last `<code>{$_GET['f_page']}</code>` page", 'notice' );
						break;
					default:
						$frontend->RegisterMessage( "Field `<code>{$field_info['name']}</code>` deleted from `<code>{$_GET['f_page']}</code>` page" );	
						break;
				}
			}
		}
		else 
		{
			$frontend->RegisterMessage( "Select field", 'notice' );			
		}
	}
	else
	{
		$frontend->RegisterMessage( "Select page type", 'notice' );		
	}
	
	redirect( $_SERVER['REQUEST_URI'] );
}

// add new section
if ( @$_POST['add_new_section'] )
{
	if ( @$_POST['new_section_name'] )	
	{
		$sec_id	= $profile_field_list->addSection();
		SK_LanguageEdit::setKey('profile_fields.section', $sec_id, is_array(@$_POST['new_section_name']) ? array_filter(@$_POST['new_section_name'],'strlen') : array() );
		$frontend->RegisterMessage( "New section `<code>" . SK_Language::section('profile_fields')->section('section')->cdata($sec_id)."</code>` created" );	
	}
	else
	{
		$frontend->RegisterMessage( "New section name in default language missing", 'notice' );		
	}
	redirect( $_SERVER['REQUEST_URI'] );
}

// delete section
if ( @$_POST['delete_section'] )
{
	if ( @$_POST['list_del_section'] )	
		switch ( $profile_field_list->delSection( @$_POST['list_del_section'] ) )		
		{
			case -1:
				$frontend->RegisterMessage( "Can not delete default section", 'error' );
				break;
			case 0:
				$frontend->RegisterMessage( "Section `<code>".SK_Language::section('profile_fields')->section('section')->cdata(@$_POST['list_del_section'])."</code>` deleted" );
				SK_LanguageEdit::deleteKey('profile_fields.section',@$_POST['list_del_section']);
				break;
			default:
				$frontend->RegisterMessage( "Section `<code>".SK_Language::section('profile_fields')->section('section')->cdata(@$_POST['list_del_section'])."</code>` deleted" );
				$frontend->RegisterMessage( "Fields moved to `<code>".SK_Language::section('profile_fields')->section('section')->cdata('1')."</code>` default section", 'notice' );
				SK_LanguageEdit::deleteKey('profile_fields.section',@$_POST['list_del_section']);
				break;			
		}
	else 
		$frontend->RegisterMessage( "Select section", 'notice' );		
	
	redirect( $_SERVER['REQUEST_URI'] );
}

// add new page
if ( @$_POST['add_new_page'] && @$_GET['f_page'] )
{
	if ( @$_POST['new_page_name'] )	
	{
		$new_page	= $profile_field_list->addPage( @$_GET['f_page'] );
		if ( $new_page )		
		{
			SK_LanguageEdit::setKey('profile_fields.page_'.@$_GET['f_page'], $new_page, is_array(@$_POST['new_page_name']) ? array_filter(@$_POST['new_page_name'],'strlen'):array());
			$frontend->RegisterMessage( "Page `<code>{$_POST['new_page_name'][SK_Config::Section('languages')->default_lang_id]}</code>` created" );
		}
		else 
			$frontend->RegisterMessage( "Incorrect page type", 'error' );			
		
	}
	else
		$frontend->RegisterMessage( "New page name missing", 'notice' );		
	
	redirect( $_SERVER['REQUEST_URI'] );
}

// delete page
if ( @$_POST['delete_page'] && @$_GET['f_page'])
{
	if ( @$_POST['list_del_page'] )	
		switch ( $profile_field_list->delPage( @$_GET['f_page'], @$_POST['list_del_page'] ) )		
		{
			case -1:
				$frontend->RegisterMessage( "New page type incorrect", 'error' );
				break;
			case -2:
				$frontend->RegisterMessage( "Can not delete first page", 'error' );
				break;
			case -3:
				$frontend->RegisterMessage( "None last page cannot be deleted!", 'notice' );
				break;
			default:
				$frontend->RegisterMessage( "Page `<code>".SK_Language::section('profile_fields')->section('page_'.@$_GET['f_page'])->cdata(@$_POST['list_del_page'])."</code>` deleted.<br> All page fields moved to first page." );
				if ( @$_GET['page_num'] == @$_POST['list_del_page'] )
				{
					@$_GET['page_num']--;					
				}
				SK_LanguageEdit::deleteKey('profile_fields.page_'.@$_GET['f_page'],@$_POST['list_del_page']);
			}
	else
		$frontend->RegisterMessage( "Select page", 'notice' );		
	
	redirect( $_SERVER['REQUEST_URI'] );
}

if (adminConfig::SaveConfigs($_POST) ){
	$result = adminConfig::getResult();
	if(count($result['validated']))
	{
		AdminProfileField::clearCache();
		if(in_array('default_username_field_display',$result['validated']))
			$frontend->registerMessage( 'Field <b>'.@$_POST['default_username_field_display'].'</b> was set for display instead username' );
		else 
		{
			$frontend->registerMessage( 'Advanced settings were changed' );
		}
	}
	else 
		$frontend->registerMessage( 'Advanced settings were not changed', 'notice' );
	redirect( $_SERVER['REQUEST_URI'] );
}
		
	


// add field to page
if ( @$_GET['f_page'] && @$_POST['set_field'] )
{
	controlAdminGETActions();
	
	switch ( $profile_field_list->addFieldToPage( @$_POST['set_field_id'], @$_GET['f_page'], @$_POST['set_field_page_num'] ) )
	{
		case -1:
			$frontend->RegisterMessage( "Select field", 'notice' );
			break;
		case -2:
			$frontend->RegisterMessage( "Select {$GET['f_page']} page number", 'notice' );
			break;
		case -3:
			$frontend->RegisterMessage( "Incorrect page type", 'error' );
			break;
		default:
			$field_info	= $profile_field_list->GetFieldInfo( @$_POST['set_field_id'] ) ;
			$frontend->RegisterMessage( "Field `<code>{$field_info['name']}</code>` added to `<code>{$_GET['f_page']}</code>` page" );
			break;			
	}	
	
	redirect( $_SERVER['REQUEST_URI'] );
}

// get fields on current page
$fpage = empty($_GET['f_page']) ? null : $_GET['f_page'];
$pageNum = empty($_GET['page_num']) ? null : $_GET['page_num'];
$crv = empty($_GET['current_reliant_value']) ? null : $_GET['current_reliant_value'];

$pr_fields = $profile_field_list->getAllFields( $fpage, $pageNum, $crv );


// get all fields pages
$pr_page_types = $profile_field_list->getPageTypes();


// get numbers of select type of page
if ( @$_GET['f_page'] )
{
	$pr_page_nums = $profile_field_list->getPageNums( $fpage );
	$frontend->assign_by_ref( 'pr_page_nums', $pr_page_nums );
	
	$field_not_in_page	= $profile_field_list->getNotInPageFields( $fpage );
	$frontend->assign_by_ref( 'field_not_in_page', $field_not_in_page );
}
else
{
	$pr_page_sections = $profile_field_list->getAllSection();
	$frontend->assign_by_ref( 'pr_page_sections', $pr_page_sections );	
	
	$field_types	= $profile_field_list->getFieldTypes();
	$frontend->assign_by_ref( 'field_types', $field_types );
	
	$field_configs['default_username_field_display']['list_values'] = AdminProfileField::getFieldsByPresentation( 'text' );	
}

// get reliant field info and register it in frontend

$reliant_field_info = array( 'name' => 'sex', 'values' => SK_ProfileFields::get('sex')->values, 'current_value' => intval( @$_GET['current_reliant_value'] ) );
$frontend->assign_by_ref( 'reliant_field_info', $reliant_field_info );

$_page['title'] = "Profile Fields";

$template = 'prof_field_list.html';


// get configs
$configs = adminConfig::ConfigList('profile_fields.advanced');
$frontend->assign_by_ref( 'prof_f_configs', $configs );

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'field_list.js' );

if ( empty($_GET['f_page']) )
{
    $frontend->assign( 'all', true );
    $frontend->IncludeJsFile( URL_ADMIN_JS . 'jquery-1.7.2.min.js' );
    $frontend->IncludeJsFile( URL_ADMIN_JS . 'jquery-ui-1.8.16.custom.min.js' );
    $frontend->IncludeJsFile( URL_ADMIN_JS . 'jquery.mjs.nestedSortable.js' );
    $frontend->IncludeJsFile( URL_ADMIN_JS . 'section_sortable.js' );
}

$frontend->includeCSSFile( URL_ADMIN_CSS . 'drag_and_drop.css' );

$frontend->assign_by_ref( 'pr_fields', $pr_fields );
$frontend->assign_by_ref( 'pr_page_types', $pr_page_types );
$frontend->display( $template );

?>
