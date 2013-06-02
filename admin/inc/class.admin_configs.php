<?php
/**
 * Project:    SkaDate reloaded
 * File:       class.admin_configs.php
 *
 * @link http://www.skadate.com/
 * @package SkaDate
 * @version 4.0
 */


/**
 * This class is intended for custimize different package settings
 *
 * @package SkaDate
 * @since 5.0
 * @author Denis J
 * @link http://www.skalinks.com/ca/forum/ Technical Support forum
 */

class adminConfig
{
	private static $validation = array(
										'invalidated'=>array(),
										'validated'=>array(),
										'processed'=>false
										);

	public function SaveConfig( $section_name, $config_name, $value )
	{
		$section = self::getSection($section_name);
		 //check config
		return $section->set($config_name,$value);
	}

	/**
	 * Save configs of specified section name
	 * Takes configs values from $data array.
	 * Returns afffected rows.
	 *
	 * @param string $section
	 * @return integer
	 */
	public static function SaveConfigs( $data )
	{
		self::$validation['processed'] = false;
		self::$validation['invalidated'] = self::$validation['validated'] = array();
		if(!isset($data['section']) || !isset($data['save_configs'])) return false;

		$configs = self::ConfigList($data['section']);

                if ( !empty($data['exclude']) )
                {
                    $_action = explode( ':', $data['exclude'] );
                    $_exclude = explode( ',', $_action[0] );

                    if ( !empty($_action[1]) && $_action[1] == '^' )
                    {
                        $available_conf = array_diff_key( $configs, array_flip($_exclude) );
                    }
                    else
                    {
                        $available_conf = array_flip( $_exclude );
                    }
                }

		$affected_rows = 0;
		foreach ( $configs as $conf_name => $conf_info  )
		{
			if ($conf_info["presentation"] == "hidden" && !isset($data[$conf_name])) {
				continue;
			}

                        if ( !empty($available_conf) && !array_key_exists($conf_name, $available_conf) )
                        {
                            continue;
                        }

                        $data[$conf_name] = isset($data[$conf_name]) ? $data[$conf_name] : 0;
                        
			if ( $conf_info['value'] != @$data[$conf_name] )
			{

				if(!self::checkConfig($conf_info['config_id'],@$data[$conf_name]))
				{
					self::$validation['invalidated'][] = $conf_name;
					continue;
				}

				self::$validation['validated'][]= $conf_name;


				switch ($conf_info["presentation"]) {
					case 'varchar':
					case 'textarea':
					case 'select':
					case 'text':
					case 'palette':
					 	$value = (string)$data[$conf_name];
						break;

					case 'color':
						$value = trim($data[$conf_name]);
						if (!self::colorCheck($value)) {
							return false;
						}
						break;

					case 'checkbox':
						$value = (bool)$data[$conf_name];
						break;

					case 'integer':
						$value = intval($data[$conf_name]);
						break;

					case 'float':
						$value = floatval($data[$conf_name]);
						break;
					default:
						$value = $data[$conf_name];
				}

				$query = SK_MySQL::placeholder( "UPDATE `".TBL_CONFIG."` SET `value`='?'
					WHERE `config_id`=?", json_encode($value), $conf_info['config_id'] );

				SK_MySQL::query($query);

				$affected_rows += SK_MySQL::affected_rows();
			}
		}
		self::$validation['processed'] = true;
		$result =  count(self::$validation['invalidated'])?false:$affected_rows;

		if ( $affected_rows )
		{
		    SK_Config::removeCache();
		}

		return $result;
	}

	private static function colorCheck($color) {
		$rgxp = '~^[0-9ABCDEFabcdef]{3}$|^[0-9ABCDEFabcdef]{6}$~';
		return preg_match($rgxp, $color, $matches);
	}

	/**
	 * Get result of last operation
	 * if AdminFrontend object was passed - print result Messages
	 * else return validation array;
	 * @param AdminFrontend  &$frontend
	 * @return array
	 */
	public static function getResult(AdminFrontend  $frontend = null , $redirect = true)
	{
		$result = array();
		if ( self::$validation['processed'] ) {
			$result = self::$validation;
		} else {
			return array();
		}

		if ($frontend) {

			if ( count(self::$validation['validated']) && !count(self::$validation['invalidated']) ) {
				$frontend->registerMessage("Changes were saved.");
			}
			elseif (count(self::$validation['validated']) && count(self::$validation['invalidated'])) {
				$frontend->registerMessage("Some changes were not Saved.",'notice');
			}
			elseif (!count(self::$validation['validated']) && count(self::$validation['invalidated'])) {
				$frontend->registerMessage("Changes were not Saved.",'notice');
			}
		}

		if ($redirect) {
			redirect( $_SERVER['REQUEST_URI'] );
		}

		return $result;
	}
	private static function checkConfig( $config_id , $value)
	{
		$query = SK_MySQL::placeholder('SELECT `php_validation` FROM `'.TBL_CONFIG.'` WHERE `config_id`=?',$config_id);
		$function_body = SK_MySQL::query($query)->fetch_cell();
		if(!trim($function_body)) return true;
		$function = create_function('$value',$function_body);
		return $function($value);
	}

	/**
	 * Get result section from section bredcrumb
	 *
	 * @param string $section_name
	 * @return SK_Config_Section
	 */
	private static function getSection( $section_name )
	{
		if(!strlen($section_name)) throw new Exception('Empty Section name in '.__FILE__.' line : '.__LINE__);
		$sections = explode('.',$section_name);
		$section = null;
		foreach ($sections as $section_name)
		{
			if(!isset($section))
				$section = SK_Config::Section(trim($section_name));
			 else
				$section = $section->Section(trim($section_name));
		}
		return $section;
	}

	public static function getChildSections( $section_name )
	{
		$section_id = self::getSection($section_name)->getSectionInfo()->config_section_id;
		if(!$section_id) return array();
		$query = SK_MySQL::placeholder('SELECT `config_section_id` AS `id`, `section` AS `name` FROM `'.TBL_CONFIG_SECTION.'` WHERE `parent_section_id`=?',$section_id);
		$result = SK_MySQL::query($query);
		$out = array();
		while ( $section = $result->fetch_object())
			$out[$section->id] = $section_name.'.'.$section->name;
		return $out;
	}

	public static function ConfigList($section_name)
	{
		$section_id = self::getSection($section_name)->getSectionInfo()->config_section_id;
		$query = SK_MySQL::placeholder('SELECT * FROM `'.TBL_CONFIG.'` WHERE `config_section_id`=? ORDER BY `config_id`',$section_id);
		$result = SK_MySQL::query($query);

		$configs = array();
		while ($config = $result->fetch_assoc())
		{
			$config['value'] = json_decode($config['value']);
			$configs[$config['name']] = $config;
			if(in_array( $config['name'],self::$validation['invalidated'] ))
                        {
				$configs[$config['name']]['invalidated']=true;
                        }

			if(in_array($config['presentation'],array('select','palette','multicheckbox','radio')))
			{
				$_result = SK_MySQL::query(SK_MySQL::placeholder('SELECT * FROM `'.TBL_CONFIG_VALUE.'` WHERE `config_id`=?',$config['config_id']));
				while ($value = $_result->fetch_assoc())
					$configs[$config['name']]['values'][$value['value']] = array( 'value' =>$value['value'], 'label' => $value['label']);
			}
		}
		return $configs;
	}

	private static function getConfigJSChecker( $section_name )
	{
		$error_message = 'Some config values are incorrect!';
		$configs = self::ConfigList($section_name);
		$checker = '';
		foreach ($configs as $name => $config )
		{

			if(!trim($config['js_validation'])) continue;

			$func_body = str_replace('"','\"',$config['js_validation']);

			if ($config['presentation']=="hidden") {
				continue;
			}

			switch ($config['presentation'])
			{
				case 'checkbox':
				case 'radio':
					$checker .= "if( !Function('value','$func_body')(F.$name.checked){\n";
					break;
				default:
					$checker .= "if( !Function('value','$func_body')(F.$name.value)){\n";
					break;
			}

			$checker .= "if(typeof(first_err)=='undefined')first_err = F.$name ;\n\$jq(F.$name).parents('.config_value:eq(0)').addClass('config_invalid');\n";
			$checker .= "page_message('$error_message','notice');\n";
			$checker .= "validated = false;}\n;";
		}
		$checker .= ";\$jq(first_err).focus();\nreturn validated;\n";
        $tempArr = explode('.',$section_name);
		$section_name = array_pop($tempArr);
		return "<script>\n function {$section_name}_checker( F ){\nvar validated = true;\nvar first_err; \n$checker }</script>";

	}

	public static function FrontendPrintConfig( $params=array() )
	{
		global $frontend;

		if( !strlen($params['section']) ) return '';
		$out = '';
		$section = self::getSection($params['section']);
		$section_info = $section->getSectionInfo();

		$configs = count($params['data']) ? $params['data'] :self::ConfigList( $params['section']);

                if ( !empty($params['exclude']) )
                {
                    $frontend->assign( 'exclude', $params['exclude'] );
                    $_action = explode( ':', $params['exclude'] );
                    $_exclude = explode( ',', $_action[0] );
                    
                    if ( !empty($_action[1]) && $_action[1] == '^' )
                    {
                        foreach ( $_exclude as $val )
                        {
                            if ( array_key_exists($val, $configs) )
                            {
                                unset( $configs[$val] );
                            }
                        }
                    }
                    else
                    {
                        $_configs = array();

                        foreach ( $_exclude as $val )
                        {
                            if (array_key_exists($val, $configs) )
                            {
                                $_configs[$val] = $configs[$val];
                            }
                        }

                        $configs = $_configs;
                    }
                }
                else
                {
                    $frontend->assign( 'exclude', array() );
                }

		$additional_js = '';
//=========================================< Palette Definition >=========================================================================

		$palette = array_filter($configs,create_function('$var','return ($var["presentation"]=="palette");'));

		if (count($palette)){
			$palette_class = file_get_contents(DIR_ADMIN.'js'.DIRECTORY_SEPARATOR.'palette.js');

			$additional_js .= $palette_class;

			foreach ($palette as $key => $item ){

				$selectedValues = explode("|", $item['value']);
				$availableValues = array();

				foreach ($item['values'] as $val)
				{
					if($val['value'] == '' || in_array($val['value'], $selectedValues))
						continue;

					$availableValues[] = $val['value'];
				}
				$json_availableValues = "[";
				foreach ($availableValues as $availableValue)
					$json_availableValues .="'$availableValue', ";
				$json_availableValues = substr($json_availableValues, 0, -2)."]";
				$json_availableValues = ($json_availableValues == ']')?"[]":$json_availableValues;

				$json_selectedValues = "[ ";
				foreach ($selectedValues as $selectedValue)
					$json_selectedValues .= ($selectedValue && $selectedValue!='none')?"'$selectedValue', ":'';
				$json_selectedValues = substr($json_selectedValues, 0, -2)."]";
				$json_selectedValues = ($json_selectedValues == ']')?"[]":$json_selectedValues;

				$definition = "{$item['name']}_palette = new Palette('{$item['name']}', \$jq('select.admissible_values'
								, '#{$item['name']}_palette_cont').get(0), \$jq('select.selected_values'
								, '#{$item['name']}_palette_cont').get(0)
								, $json_availableValues, $json_selectedValues ); ";

				$configs[$key]['js_definition'] = $definition;

			}

		}

//=========================================< / Palette Definition >=========================================================================

		$numeric = array_filter($configs,create_function('$var','return ($var["presentation"]=="integer" || $var["presentation"]=="float");'));

		if (count($numeric)) {
			$counter_class = file_get_contents(DIR_ADMIN.'js'.DIRECTORY_SEPARATOR.'counter_field.js');
			$additional_js .= "\n\n" . $counter_class;
		}

		$color = array_filter($configs,create_function('$var','return ($var["presentation"]=="color");'));

		if (count($color)) {
			$color_class = file_get_contents(DIR_ADMIN.'js'.DIRECTORY_SEPARATOR.'color_picker.js');
			$additional_js .= "\n\n" . $color_class;

			$colors = array(
						 'ffffff','ffcccc','ffcc99','ffff99','ffffcc','99ff99','99ffff','ccffff','ccccff','ffccff',
						 'cccccc','ff6666','ff9966','ffff66','ffff33','66ff99','33ffff','66ffff','9999ff','ff99ff',
						 'c0c0c0','ff0000','ff9900','ffcc66','ffff00','33ff33','66cccc','33ccff','6666cc','cc66cc',
						 '999999','cc0000','ff6600','ffcc33','ffcc00','33cc00','00cccc','3366ff','6633ff','cc33cc',
						 '666666','990000','cc6600','cc9933','999900','009900','339999','3333ff','6600cc','993399',
						 '333333','660000','993300','996633','666600','006600','336666','000099','333399','663366',
						 '222222','550000','883300','883300','555500','004400','225555','000077','222288','441144',
						 '000000','330000','663300','663333','333300','003300','003333','000066','330099','330033'
						);

			foreach ($color as $key => $item ){
				$configs[$key]['colors'] = $colors;
				$configs[$key]['js_definition'] = "window.color_pickers['{$item['name']}'] = new colorPicker('{$item['name']}', '{$item['value']}');";
			}
		}

		$frontend->assign('additional_js',$additional_js);

		if(!count($configs)) return '';
        $tempArr = explode('.',$params['section']);
		$frontend->assign('js_checker',array('body'=>self::getConfigJSChecker($params['section']),'name'=>array_pop($tempArr).'_checker') );
		$frontend->assign_by_ref('configs',$configs );
		$frontend->assign('section_label', !empty( $params['caption'] ) ? $params['caption'] : (isset($params['section_label']) ? $params['section_label'] : $section_info->label));
		$frontend->assign_by_ref('section_name',$params['section']);
		$out = $frontend->fetch('inc/_config_tpl.html');
		$frontend->clear_assign(array('configs','section_name','section_label'),'js_checker');

		return $out;
	}
}
?>
