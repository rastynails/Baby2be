<?php

require_once( '../../internals/Header.inc.php' );

require_once( DIR_ADMIN_INC.'inc.auth.php' );

if ( !empty($_POST['fieldList']) )
{
    foreach ( $_POST['fieldList'] as $fieldSection )
    {
        $query = SK_MySQL::placeholder( 'UPDATE `' . TBL_PROF_FIELD_SECTION. '`
            SET `order` = ? WHERE `profile_field_section_id` = ?',
            $fieldSection['order'], $fieldSection['profile_field_section_id'] );

        SK_MySQL::query( $query );

        if ( !empty($fieldSection['fields']) )
        {
            foreach ( $fieldSection['fields'] as $field )
            {
                $query = SK_MySQL::placeholder( 'UPDATE `' . TBL_PROF_FIELD . '`
                    SET `profile_field_section_id` = ?, `order` = ? WHERE `profile_field_id` = ?',
                        $field['profile_field_section_id'], $field['order'], $field['profile_field_id'] );

                SK_MySQL::query( $query );
            }
        }
    }
}
