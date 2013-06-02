<?php 

function saveMailTemplate($subject, $text )
{
    if( !$text )
        return false;

    $_query = sql_placeholder( " INSERT INTO `".TBL_MAIL_TEMPLATE."` ( `subject`, `text` ) VALUES(?@) ", array( $subject, $text ) );

    return MySQL::affectedRows($_query) ? true : false;
}

function getMailTemplateList()
{
    $_query = "SELECT `mail_template_id`, IF( LENGTH(`text`) > 190, CONCAT( SUBSTRING(`text`, 1, 200 ), '...') , `text` ) AS `text` FROM `".TBL_MAIL_TEMPLATE."` WHERE 1 ORDER BY `mail_template_id` DESC ";
    return MySQL::fetchArray($_query);
}

function getTemplate( $template_id )
{
    if( !intval($template_id) )
        return array();
        
    $_query = sql_placeholder( "SELECT `mail_template_id`, `subject`, `text` FROM `".TBL_MAIL_TEMPLATE."` WHERE `mail_template_id` = ? ", $template_id );
    return MySQL::fetchRow($_query);
}

function deleteTemplate( $template_id )
{
    if( !intval($template_id) )
        return false;

    $_query = sql_placeholder( "DELETE FROM `".TBL_MAIL_TEMPLATE."` WHERE `mail_template_id` = ? ", $template_id );
    return MySQL::affectedRows($_query) ? true : false;
}

function updateTemplate( $template_id, $subject, $text )
{
    $template_id = intval($template_id);
    $subject = trim($subject);
    $text = trim($text);
    
    if( !$template_id || !$text || !$subject )
        return false;
        
    $_query = sql_placeholder( "UPDATE `".TBL_MAIL_TEMPLATE."` SET `subject` = ?, `text` = ? WHERE `mail_template_id` = ? LIMIT 1 ", $subject, $text, $template_id );
    $updated_rows = MySQL::affectedRows($_query);

    if( $updated_rows == -1 )
        return false;
        
    if( !$updated_rows == 0 )
    {
        $_query = sql_placeholder( "SELECT * FROM `".TBL_MAIL_TEMPLATE."` WHERE `mail_template_id` = ? ", $template_id );
        $result = MySQL::fetchResource($_query);
        if ( !mysql_num_rows($result) )
            return false;
    }

    return true;
}
