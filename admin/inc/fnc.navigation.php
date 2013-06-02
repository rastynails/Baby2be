<?php

function FrontendPrintMenuSelect( $params )
{
	$items = @$params['menu_items'];
	$selected_item = @$params['selected'];
	$menu = @$params['menu'];
	foreach ( $items as $menu_name=>$menu_items )
	{
		$selected = (!intval($selected_item) && ($menu == $menu_name )) ? 'selected' : '';
		$output.="<option $selected value='$menu_name'>".SK_Language::section('nav_menu_item')->cdata($menu_name)."</option>";
		if(count($menu_items))
		{
			$params['menu_items'] = $menu_items['menu'];
			$params['menu'] = $menu_name;
			$output.=getMenuItems($params);
		}
	}			
	return $output;	
}

function getMenuItems( $params )
{
	$items				= @$params['menu_items'];
	$padding			= @$params['padding'] + 2;
	$parent_menu_name	= @$params['parent_name'];
	$selected_item_id	= @$params['selected'];
		
	$space = str_repeat( '&nbsp;', $padding );
		
	foreach ( $items as $key => $item_info )
	{
		$selected		= ( $selected_item_id == $item_info['menu_item_id'] ) ? 'selected' : '';
		$option_cont	= $space.'&#9493';
		
		$output .= '<option value="'.$item_info['menu_item_id'].'" '.$selected.' >'.$option_cont.'&nbsp;'.
		SK_Language::section('nav_menu_item')->cdata($item_info['name']).'</option>';
						
						
		if ( $item_info['sub_items']  )
		{
			$output .= getMenuItems( array(
				'menu_items'	=> $item_info['sub_items'],
				'padding'		=> $padding, 
				'parent_name'	=> $item_info['name'], 
				'selected'		=> $selected_item_id,
				) );			
		}
	}	
	
	return $output;
}

function FrontendPrintDocuments( $params )
{
	$documents			= $params['documents'];
	$padding			= $params['padding'] + 10;
	$parent_doc_key		= $params['parent_doc'];
		
	$total_items = count( $documents );
	
	foreach ( $documents as $key => $doc_info )
	{
		$output .= '<tr class="'.CycleTrClass().'" '.$params['highlight'].'>
						<td>';
							$output .= ( !$doc_info['base_document'] ) ? '<input type="checkbox" name="doc_arr[]" value="'.$doc_info['document_key'].'" />' : '';
						$output .= '</td>
						<td style="padding-left:'.$padding.'px">';
						
						$output .= ( $doc_info['parent_document_key'] ) ? '<span class="nested_cat_sign" title="Parent menu: '.SK_Language::section('nav_doc_item')->cdata($doc_info['parent_document_key']).'">&#9493</span>' : '';
						if ( $key ) 
							$output .= '<a class="arr_up" href="'.URL_ADMIN.'nav_doc.php?doc_key='.$doc_info['document_key'].'&move=up"></a>';
						else 
							 $output .= '<div class="arr_up_i"></div>';
						if ( ( $key + 1 ) < $total_items )
							$output .= '<a class="arr_down" href="'.URL_ADMIN.'nav_doc.php?doc_key='.$doc_info['document_key'].'&move=down"></a>';
						else 
							$output .= '<div class="arr_down_i"></div>&nbsp;';
											
						$output .= '&nbsp;<a href="'.URL_ADMIN.'nav_doc_create.php?doc_key='.$doc_info['document_key'].'" >'.SK_Language::section('nav_doc_item')->cdata($doc_info['document_key']).'</a>
						</td>';
						
						$disabled = ( !$doc_info['status'] ) ? 'disabled' : '';
						
						$guest_checked = ( $doc_info['access_guest'] ) ? 'checked' : '';
						$output .= '<td align="center" >
										<input type="checkbox" id="guest_'.$doc_info['document_key'].'" name="'.$doc_info['document_key'].'[guest]" '.$guest_checked.' '.$disabled.'/>
									</td>';	
						$member_checked = ( $doc_info['access_member'] ) ? 'checked' : '';
						$output .= '<td align="center" >
										<input type="checkbox" id="member_'.$doc_info['document_key'].'" name="'.$doc_info['document_key'].'[member]" '.$member_checked.' '.$disabled.'/>
									</td>';	
						$access_checked = ( $doc_info['status'] ) ? 'checked' : '';
						$output .= '<td align="center">
										<input type="checkbox" name="doc_access['.$doc_info['document_key'].']" '.$access_checked.'/>
									</td>';
												
					$output .= '</tr>';
		if ( $doc_info['sub_docs'] )
		{
			$output .= FrontendPrintDocuments( array(
				'documents'		=>	$doc_info['sub_docs'],
				'padding'		=> $padding, 
				'highlight'		=> $params['highlight'],
				'parent_doc'	=> $doc_info['parent_document_key'], 
				) );			
		}
	}	
	
	return $output;
}

function FrontendPrintDocumentSelect( $params )
{
	$docs				= $params['documents'];
	$padding			= $params['padding'] + 2;
	$parent_doc_key		= $params['parent_doc'];
	$selected_doc_key	= $params['selected'];
	
	$space = str_repeat( '&nbsp;', $padding );
	
	foreach ( $docs as $key => $doc_info )
	{
		$selected		= ( $selected_doc_key == $doc_info['document_key'] ) ? 'selected' : '';
			
		$output .= '<option value="'.$doc_info['document_key'].'" '.$selected.' >'.$space.'&#9493&nbsp;'.
		SK_Language::section('nav_doc_item')->cdata($doc_info['document_key']).'</option>';
		
						
						
		if ( $doc_info['sub_docs'] )
		{
			$output .= FrontendPrintDocumentSelect( array(
				'documents'		=> $doc_info['sub_docs'],
				'padding'		=> $padding, 
				'parent_doc'	=> $doc_info['document_key'], 
				'selected'		=> $selected_doc_key,
				) );			
		}
	}	
	
	return $output;	
}

function frontendPrintMenuItems( $params )
{
    $items = $params['menu_items'];
    $childe = !empty( $params['childe'] );
    $parent_menu_name = $params['parent_name'];
    $memberships_arr = $params['memberships_arr'];
    $total_items = count( $items );

    if ( $childe )
    {
        $output .= '<ol>';
    }
    
    foreach ( $items as $key => $item_info )
    {
        $output .=
            '<li class="menu-item">
                <input type="hidden" name="menu_id" value="'.$item_info['menu_id'].'" />
                <input type="hidden" name="menu_item_id" value="'.$item_info['menu_item_id'].'" />
                <input type="hidden" name="parent_menu_item_id" value="'.$item_info['parent_menu_item_id'].'" />
                <div class="clearfix '.CycleTrClass('handler_').'" style="min-height: 31px;">
                    <div class="left-side">
                        <div class="left-side brd">
                            <input type="checkbox" value="'.$item_info['name'].'" name="item_arr['.$item_info['menu_item_id'].']">
                        </div>
                        <div class="left-side brd">
                            <span class="nested_cat_sign" title="Parent menu: '.$parent_menu_name.'">&#9493</span>';

                            if ( $key )
                            {
                                $output .= '<a class="up up-enb" href="'.URL_ADMIN.'nav_menu.php?item='.$item_info['menu_item_id'].'&move=up"></a>';
                            }
                            else
                            {
                                $output .= '<a class="up up-dis" href="#"></a>';
                            };
                            
                            if ( ($key + 1) < $total_items )
                            {
                                $output .= '<a class="down down-enb" href="'.URL_ADMIN.'nav_menu.php?item='.$item_info['menu_item_id'].'&move=down"></a>';
                            }
                            else
                            {
                                $output .= '<a class="down down-dis" href="#"></a>';
                            }

                            $output .= '<a href="'.URL_ADMIN.'nav_menu_create.php?item_id='.$item_info['menu_item_id'].'">'.SK_Language::section('nav_menu_item')->cdata($item_info['name']).'</a>
                        </div>
                    </div>
                    <div class="right-side">';

                    foreach ( $memberships_arr as $key => $membership_info )
                    {
                        $checked = ( in_array( $membership_info['membership_type_id'], $item_info['memberships'] ) ) ? 'checked' : '';
                        
                        $output .= '<div class="left-side brd div-val">
                                        <div style="height: 0; visibility: hidden;">
                                            '.SK_Language::text('membership.types.' . $memberships_arr[$key]['membership_type_id']).'
                                        </div>
                                        <div>
                                            <input type="checkbox" id="'.$item_info['menu_item_id'].'_'.$membership_info['membership_type_id'].'" name="'.$item_info['name'].'['.$membership_info['membership_type_id'].']" '.$checked.' />
                                        </div>
                                    </div>';
                    }

                    $output .= '</div>
                        </div>';

                    if ( $item_info['sub_items'] )
                    {
			$output .= frontendPrintMenuItems( array(
                                        'menu_items' => $item_info['sub_items'],
                                        'childe' => TRUE,
                                        'parent_name' => $item_info['name'],
                                        'memberships_arr' => $memberships_arr,
                                    ) );
                    }

            $output .= '</li>';
	}

        if ( $childe )
        {
            $output .= '</ol>';
        }
        
	return $output;
}
