<?php

function smarty_function_palette_picker( $params, SK_Layout $Layout )
{
	if ( !@$params['id'] ) {
		$Layout->trigger_error('missing param `id`', E_USER_WARNING);
		return;
	}
	
	// including js file
	SK_Layout::frontend_handler()->include_js_file(URL_STATIC.'palette_picker.js');
	
	$target_auto_id = $Layout->current_component()->getTagAutoId( $params['id'] );
	$cmp_auto_id = $Layout->current_component()->auto_id;
	
	SK_Layout::frontend_handler()->onload_js("window.sk_components['$cmp_auto_id'].palette_picker = new palettePicker('$target_auto_id');");

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
	$color_interface = '';	
	
	foreach ($colors as $color) {
		$color_interface .= '<div class="palette_col" title="'.$color.'" style="background-color: #'.$color.'"></div>';
	}
		
	// rendering html output
	return
	'<div id="pc-'.$target_auto_id.'">
		<span class="text_formatter">
			<a href="javascript://" id="'.$target_auto_id.'" title="palette" class="chat_palette chat_control"></a>
		</span>	
		
		<div style="position: relative;">
			<div class="palette_picker" style="display: none;">
				' . $color_interface . '
			</div>
		</div>
	</div>';
}
