<?php
function sortGroups ( $groups, &$sorted_groups = array() , $group_id = false, $level = 1 )
{
	if ( $group_id ) 
	{
		$level++;
		$group_childs = explode( ' ', $groups[$group_id]['has_child'] );
		foreach ( $group_childs as $child_id )
		{
			if ( !$child_id ) {
				continue;
			}		
			if ( $groups[$child_id]['has_child'] ) {
				$groups[$child_id]['label'] = str_repeat('-', $level).' '.$groups[$child_id]['name'];
				$groups[$child_id]['margin'] = ($level+1)*15;	
				$sorted_groups[$child_id] = $groups[$child_id];				
				sortGroups( $groups, $sorted_groups, $child_id, $level ); 
			}
			else {
				$groups[$child_id]['label'] = str_repeat('-', $level).' '.$groups[$child_id]['name'];
				$groups[$child_id]['margin'] = ($level+1)*15;
				$sorted_groups[$child_id] = $groups[$child_id];	
			}
		}
		
		return ;
	}
	
	foreach ($groups as $group) 
	{
		$level = 1;
		if ( $group['parent_id'] ) {
			continue;		
		}
		$group['label'] = $group['name'];
		$group['margin'] = $level*15;
		$sorted_groups[$group['group_id']] = $group;
		
		if ( $group['has_child'] )		
		{
			$group_childs = explode( ' ', $group['has_child'] );
			foreach ( $group_childs as $child_id )
			{
				if ( !$child_id ) {
					continue;
				}						
				if ( $groups[$child_id]['has_child'] ) 
				{
					$groups[$child_id]['label'] = str_repeat('-', $level).' '.$groups[$child_id]['name'];
					$groups[$child_id]['margin'] = ($level+1)*15;
					$sorted_groups[$child_id] = $groups[$child_id];
					sortGroups( $groups, $sorted_groups, $child_id, $level ); 
				}
				else 
				{
					$groups[$child_id]['label'] = str_repeat('-', $level).' '.$groups[$child_id]['name'];
					$groups[$child_id]['margin'] = ($level+1)*15;
					$sorted_groups[$child_id] = $groups[$child_id];
				}				
			}
		}
	}

	return $sorted_groups;
}


function addCurrency( $code, $sign ) 
{
	$section = new SK_Config_Section('classifieds');
	$configs = $section->getConfigsList();
	
	$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_CONFIG_VALUE."` (`config_id`, `value`, `label`) VALUES(?, '?', '?')",
		$configs['currency']->config_id, $sign, $code );
	
	SK_MySQL::query( $query );

    if( SK_MySQL::affected_rows() )
    {
        SK_Config::removeCache();
    }
}

function deleteCurrency( $config_id, $code )
{
    $query = SK_MySQL::placeholder( "SELECT `value` FROM `".TBL_CONFIG_VALUE."` WHERE `config_id`=? AND `label`='?'", $config_id, $code );
    $code_value = MySQL::fetchField($query);

    $query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM `".TBL_CLASSIFIEDS_ITEM."` WHERE `currency`='?' ", $code_value );
    $count = MySQL::fetchField($query);

    // Currency is in use
    if ($count > 0)
    {
        return -1;
    }

	$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_CONFIG_VALUE."` WHERE `config_id`=? AND `label`='?'", $config_id, $code );
	
	SK_MySQL::query( $query );	
    
    if( SK_MySQL::affected_rows() )
    {
        //set new default currency
        $query = SK_MySQL::placeholder( "SELECT `value` FROM `".TBL_CONFIG_VALUE."` WHERE `config_id`=? LIMIT 1", $config_id );
        $new_code_value = MySQL::fetchField($query);

        $query = SK_MySQL::placeholder( "UPDATE `".TBL_CONFIG."` SET `value`='\"?\"' WHERE `config_id`=? LIMIT 1", $new_code_value, $config_id );
        MySQL::query($query);

        SK_Config::removeCache();
    }

}

function approveAllClsItems()
{
	$query = "UPDATE `".TBL_CLASSIFIEDS_ITEM."` SET `is_approved`=1 WHERE `is_approved`=0 ";    
	SK_MySQL::query( $query );
    
    $query = "UPDATE `".TBL_NEWSFEED_ACTION."` SET `status`='active' WHERE `entityType`='post_classifieds_item' AND `status`='approval' ";
    SK_MySQL::query( $query );
}
