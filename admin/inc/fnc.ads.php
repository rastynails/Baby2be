<?php

function frontendPrintDocuments( $params )
{
	global $language;
	
	$documents	= $params['documents'];
	$padding	= $params['padding'] + 10;
	$_positions	= $params['positions'];
		
	foreach ( $documents as $key => $doc_info )
	{
		$output .= '<tr class="'.CycleTrClass().'" '.$params['highlight'].'>
						<td style="padding-left:'.$padding.'px">';
						
						$output .= ( $doc_info['parent_document_key'] ) ? '<span class="nested_cat_sign" title="Parent menu: '.SK_Language::section('nav_doc_item')->text($doc_info['parent_document_key']).'">&#9493</span>' : '';
						$output .= '&nbsp;'.SK_Language::section('nav_doc_item')->text($doc_info['document_key']).'&nbsp;';
						
						$output .= '</td>';
						
						foreach ( $_positions as $_pos_info )
						{
							$output .= '<td align="center">';
							
							if ( $doc_info['ads_info'][$_pos_info['position_id']] )
							{
								$output .= '<input type="checkbox" class="flist_checkbox" name="pos_arr['.$doc_info['document_key'].'][]" value="'.$_pos_info['position_id'].'" id="'.$doc_info['document_key'].'_'.$_pos_info['position_id'].'" />';
								$output .= ( $doc_info['ads_info'][$_pos_info['position_id']]['template_set_id'] ) ? '<div class="ads_pos_present"><label for="'.$doc_info['document_key'].'_'.$_pos_info['position_id'].'">'.$doc_info['ads_info'][$_pos_info['position_id']]['name'].'</label></div>' : '';
							}
							else 
								$output .= '<div class="ads_pos_forbidden">not available</div>';
																	
							$output .= '</td>';
						}
					$output .= '</tr>';
		if ( $doc_info['sub_docs'] )
		{
			$output .= FrontendPrintDocuments( array(
				'documents'		=>	$doc_info['sub_docs'],
				'padding'		=> $padding, 
				'highlight'		=> $params['highlight'],
				'parent_doc'	=> $doc_info['parent_document_key'], 
				'positions'		=> $_positions,
				) );			
		}
	}	
	
	return $output;
}

function frontendPrintTemplates ( $params) 
{
	$all_templates = $params['all_templates'];
	$all_countries = $params['all_countries'];
	$template_set = $params['template_set'];	
	
	
	foreach ($all_templates as $template)
	{		
			$checked = "";
			$highlight = "";
			if ( isset($template_set['templates'][$template['template_id']]) )
			{
				$highlight = " style='color: #2f2f26; background-color: #ffee9f;' ";
				$checked = " checked='checked' ";
			}					
			
			$output .=  "<tr class='".CycleTrClass()."' onclick=\"document.getElementById('template[".$template['template_id']."]').checked=!document.getElementById('template[".$template['template_id']."]').checked; hightlight(this, document.getElementById('template[".$template['template_id']."]').checked)\" ".$highlight." >";

			$output .= 	"<td colspan='2' ><table><tr>";
			$output .=  "<td ><input ".$checked." type='checkbox' name='template[".$template['template_id']."]' id='template[".$template['template_id']."]' value='".$template['template_id']."' onclick='this.checked = !this.checked;'";
			$output .=  "<td align='center' ><div class='ads_tpl_view_2'>".$template['code']."</div></td><td>";
			
			$output .=  "<select id='ads_list_table_country_select[".$template['template_id']."]' name='country[".$template['template_id']."]' style='width: 100px;' />";
				 			foreach ($all_countries as $country)
				 			{				 				
				 				$output .=  "<option value='".$country['Country_str_code']."' ";
				 				if ($template_set['templates'][$template['template_id']]['Country_str_code'] === $country['Country_str_code'])
				 					$output .= "selected='selected' >";
				 				else
				 					$output .= ">";
					 			$output .= $country['Country_str_name']."</option>";
				 			}
			$output .=  "</select></td></tr></table></td></tr>";
		
	}	
	return $output;	
}

?>