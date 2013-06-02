<?php

function smarty_function_online_btn( array $params, SK_Layout $Layout )
{
    if ( !key_exists('profile_id', $params) ) {
        trigger_error('tpl function {online_btn ...} missing attribute "profile_id"', E_USER_WARNING);
        return;
    }
    if ( !($profile_id = (int)$params["profile_id"]) ) {
        trigger_error('tpl function {online_btn ...} invalid "profile_id" value', E_USER_WARNING);
        return;
    }
    
    $label = SK_Language::text("%interface.online_btn.label");
    $feature_enabled = app_Features::isAvailable(11);

    $blocked = app_Bookmark::isProfileBlocked($profile_id, SK_HttpUser::profile_id());
    
    if (!$feature_enabled || !SK_HttpUser::profile_id() || $profile_id == SK_HttpUser::profile_id()) {
        return '<span class="online_button">'.$label.'</span>';
    }

    $blocked = app_Bookmark::isProfileBlocked($profile_id, SK_HttpUser::profile_id());
    
    if ($blocked) {
        return '<span class="online_button">'.$label.'</span>';
    }
    
    
    if (app_ProfilePreferences::get('my_profile', 'hide_im_btn', $profile_id)) {
        return '<span class="online_button">'.$label.'</span>';
    }
    
    
    
    if (SK_Config::section("chuppo")->enable_chuppo_im) {
        $opp_key = app_ProfileField::getProfileUniqueId($profile_id);
        $pr_key = app_ProfileField::getProfileUniqueId(SK_HttpUser::profile_id());
        
        $click_func = "window.open( '" . SK_Navigation::href("private_chat", array("userKey"=>$pr_key, "oppUserKey"=>$opp_key)) . "', '', 'height=540,width=415,left=100,top=100,scrollbars=no,resizable=no' ); return false";
        
        return '<a class="online_button" href="javascript://" onclick="' . $click_func . '">'.$label.'</a>'; 
    }

    if( (boolean) SK_Config::section('123_wm')->enable_123wm )
    {
        $u = app_Profile::getFieldValues($profile_id, 'username');

        //return '<span class="highlight"><a href="javascript://" onclick="FC_invite_1to1_chat(\''.$u.'\');">'.$label.'</a></span>';
        return '<span class="highlight"><a href="javascript://" onclick="window.initiate123wm(\''.$u.'\');">'.$label.'</a></span>';
    }

    return '<a class="online_button" href="javascript://" onclick="SK_openIM(' . $profile_id . '); return false;">'.$label.'</a>'; 
}

