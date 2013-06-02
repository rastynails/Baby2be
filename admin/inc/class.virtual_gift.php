<?php

class VirtualGift
{

    /**
     * Returns the list of virtual gifts templates.
     *
     * @return array
     */
    public static function getGiftTemplateList( $cat_id = null )
    {
        $cat_cond = $cat_id ? " AND `t`.`category_id` = " . (int)$cat_id : "";
        
    	$query = "SELECT `t`.*, `c`.`title` AS `category` FROM `".TBL_VIRTUAL_GIFT_TPL."` AS `t`
    	   LEFT JOIN `".TBL_VIRTUAL_GIFT_CATEGORY."` AS `c` ON(`c`.`id`=`t`.`category_id`)
    	   WHERE `t`.`status` != 'deleted' $cat_cond
    	   ORDER BY `t`.`tpl_id` DESC";
    	
    	$result = SK_MySQL::query($query);
    	$tpl_list = array();
    	
    	while ( $tpl = $result->fetch_assoc() )
    	{
    	    $tpl['picture'] = app_VirtualGift::getGiftPictureSrc($tpl['tpl_id'], $tpl['picture_hash']);
    		$tpl_list[] = $tpl;
    	}
    	
    	return $tpl_list;
    }
    
    /**
     * Returns virtual gift template's info.
     *
     * @param integer $template_id
     * @return array
     */
    public static function getGiftTemplate( $template_id )
    {	
    	if ( !is_numeric( $template_id ) )
    		return false;
    
    	$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_VIRTUAL_GIFT_TPL."` 
    	   WHERE `tpl_id`=?", $template_id);
    
    	$tpl = SK_MySQL::query($query)->fetch_assoc();
    	$tpl['picture'] = app_VirtualGift::getGiftPictureSrc($tpl['tpl_id'], $tpl['picture_hash']);
    	    
    	return $tpl;
    }
    
    /**
     * Updates virtual gift template.
     *
     * @param integer $template_id
     * @param integer $status template's status : 0 - not available; 1 - available
     * @return boolean
     */
    public static function updateGiftTemplate( $template_id, $status, $credits, $category = null, $hash )
    {	
    	$template_id = intval($template_id);
    	if ( !$template_id )
    		return false;
    
    	$status = ( $status == 1 ) ? 'active' : 'inactive';
    	
    	if ( $hash )
    	{
    		$query = SK_MySQL::placeholder("UPDATE `".TBL_VIRTUAL_GIFT_TPL."` 
                SET `status`='?', `picture_hash`='?', `credits`='?', `category_id`=?  
    			WHERE `tpl_id`=?", $status, $hash, floatval($credits), intval($category), $template_id);
    	}
    	else
    	{
    		$query = SK_MySQL::placeholder("UPDATE `".TBL_VIRTUAL_GIFT_TPL."` 
                SET `status`='?', `credits`='?', `category_id`=?
    			WHERE `tpl_id`=?", $status, floatval($credits), intval($category), $template_id);
    	}
    	
    	SK_MySQL::query($query);
    
    	if ( !SK_MySQL::affected_rows() )
    		return false;
    	
    	return true;
    }
    
    
    /**
     * Creates new virtual gift template.
     *
     * @param integer $status template's status : 0 - not available; 1 - available
     * @return boolean
     */
    public static function createGiftTemplate( $status, $credits, $category = null )
    {
    	$status = ($status == 1) ? 'active' : 'inactive';    	    	
    
        $hash = time();
    	$query = SK_MySQL::placeholder("INSERT INTO `".TBL_VIRTUAL_GIFT_TPL."` 
            (`picture_hash`, `status`, `credits`, `category_id`) 
    		VALUES( ?, '?', ?, ? )", $hash, $status, floatval($credits), $category);
    
    	SK_MySQL::query($query);
    	
    	$template_id = SK_MySQL::insert_id();
    	
    	return array('tpl_id' => $template_id, 'hash' => $hash);
    }
    
    /**
     * Deletes virtual gift template.
     *
    * @param integer $template_id	
     * @return boolean
     */
    public static function deleteGiftTemplate( $template_id )
    {
    	if ( !is_numeric( $template_id ) )
    		return false;
    	
        $query = SK_MySQL::placeholder("UPDATE `".TBL_VIRTUAL_GIFT_TPL."` 
            SET `status`='deleted'
            WHERE `tpl_id`=?", $template_id);
        
        SK_MySQL::query($query);
        
        return SK_MySQL::affected_rows() ? true : false;
    }
    
    
    public static function addCategory( $title )
    {
        if ( !strlen($title) )
        {
            return false;
        }
        
        $query = "SELECT MAX(`order`) FROM `".TBL_VIRTUAL_GIFT_CATEGORY."`";
        $order = (int)SK_MySQL::query($query)->fetch_cell() + 1;
        
        $query = SK_MySQL::placeholder("INSERT INTO `".TBL_VIRTUAL_GIFT_CATEGORY."` (`title`, `order`) 
            VALUES('?', ?)", trim($title), $order);
    
        SK_MySQL::query($query);
                
        return SK_MySQL::insert_id();
    }
    
    public static function deleteCategory( $cat_id )
    {
        if ( !is_numeric($cat_id) )
        {
            return false;
        }
        
        $query = SK_MySQL::placeholder("DELETE FROM `".TBL_VIRTUAL_GIFT_CATEGORY."`
            WHERE `id`=?", $cat_id);
        
        SK_MySQL::query($query);
        
        $query = SK_MySQL::placeholder("UPDATE `".TBL_VIRTUAL_GIFT_TPL."`
            SET `category_id` = NULL WHERE `category_id`=?", $cat_id);
        
        SK_MySQL::query($query);
        
        return true;
    }
    
    public function updateCategory( $cat_id, $title )
    {
        if ( !$cat_id || !strlen($title) )
        {
            return false;
        }
        
        $query = SK_MySQL::placeholder("UPDATE `".TBL_VIRTUAL_GIFT_CATEGORY."` 
            SET `title` = '?' WHERE `id` = ?", trim($title), $cat_id);
        
        SK_MySQL::query($query);
        
        return true;
    }
    
    public static function getCategoryList( )
    {
        $query = "SELECT * FROM `".TBL_VIRTUAL_GIFT_CATEGORY."` WHERE 1 ORDER BY `order` ASC";
        
        $res = SK_MySQL::query($query);
        
        $list = array();
        while ( $row = $res->fetch_assoc() )
        {
            $list[] = $row;
        }
        
        return $list;
    }
    
    public static function moveCategoryOrder( $cat_id, $move = 'up' )
    {
        if ( !is_numeric($cat_id) )
        {
            return false;
        }
        
        // get old order
        $query = "SELECT `order` FROM `".TBL_VIRTUAL_GIFT_CATEGORY."` WHERE `id`='$cat_id'";
        $old_order = SK_MySQL::query($query)->fetch_cell();
        
        switch ( $move )
        {
            case 'up':
                $query = "SELECT `id`, `order` FROM `".TBL_VIRTUAL_GIFT_CATEGORY."` 
                    WHERE `order` < '{$old_order}' ORDER BY `order` DESC LIMIT 1";
                break;
                
            case 'down':
                $query = "SELECT `id`, `order` FROM `".TBL_VIRTUAL_GIFT_CATEGORY."` 
                    WHERE `order` > '{$old_order}' ORDER BY `order` ASC LIMIT 1";
                break;
        }
        
        $cur_order = SK_MySQL::query($query)->fetch_assoc();

        if ( !$cur_order )
        {
            return false;
        }
        SK_MySQL::query(
            "UPDATE `".TBL_VIRTUAL_GIFT_CATEGORY."` SET `order`='{$cur_order['order']}' WHERE `id`='$cat_id'"
        );

        SK_MySQL::query(
            "UPDATE `".TBL_VIRTUAL_GIFT_CATEGORY."` SET `order`='{$old_order}' WHERE `id`='{$cur_order['id']}'"
        );
        
        return true;
    }
}