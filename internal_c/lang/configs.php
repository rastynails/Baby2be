<?php
SK_Config::$sections = array (
  'layout' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '1',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'layout',
       'label' => 'Layout',
       'parent_section_id' => '0',
       'config_section_id' => '1',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'caching' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '149',
         'config_section_id' => '1',
         'name' => 'caching',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Site Caching',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'theme' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '1',
         'config_section_id' => '1',
         'name' => 'theme',
         'value' => 'gh',
         'presentation' => 'select',
         'description' => 'Layout Theme',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'mailbox' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '7',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'mailbox',
       'label' => 'Mailbox',
       'parent_section_id' => '0',
       'config_section_id' => '7',
    )),
     'sub_sections' => 
    array (
      'spam_filter' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '8',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'spam_filter',
           'label' => 'Spam filter setings',
           'parent_section_id' => '7',
           'config_section_id' => '8',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'mailbox_message_filter' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '4',
             'config_section_id' => '8',
             'name' => 'mailbox_message_filter',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Turn on profile mailbox message filter',
             'php_validation' => NULL,
             'js_validation' => '',
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
    ),
     'configs' => 
    array (
    ),
     'config_values' => 
    array (
    ),
  )),
  'navigation' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '12',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'navigation',
       'label' => 'Navigation',
       'parent_section_id' => '0',
       'config_section_id' => '12',
    )),
     'sub_sections' => 
    array (
      'settings' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '13',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'settings',
           'label' => 'Navigation settings ',
           'parent_section_id' => '12',
           'config_section_id' => '13',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'display_index' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '6',
             'config_section_id' => '13',
             'name' => 'display_index',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Remove default page from the URLs (index.php)?',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'join_document_redirect' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '48',
             'config_section_id' => '13',
             'name' => 'join_document_redirect',
             'value' => 'home',
             'presentation' => 'select',
             'description' => 'After join, redirect profile to:',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'mod_rewrite' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '96',
             'config_section_id' => '13',
             'name' => 'mod_rewrite',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Enable friendly url',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'signin_document_redirect' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '287',
             'config_section_id' => '13',
             'name' => 'signin_document_redirect',
             'value' => 'home',
             'presentation' => 'select',
             'description' => 'After sign in, redirect profile to:',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'signout_document_redirect' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '5',
             'config_section_id' => '13',
             'name' => 'signout_document_redirect',
             'value' => 'index',
             'presentation' => 'select',
             'description' => 'After logout, redirect profile to:',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
    ),
     'configs' => 
    array (
    ),
     'config_values' => 
    array (
    ),
  )),
  'site' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '2',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'site',
       'label' => 'Global Configuration',
       'parent_section_id' => '0',
       'config_section_id' => '2',
    )),
     'sub_sections' => 
    array (
      'admin' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '3',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'admin',
           'label' => 'Admin settings',
           'parent_section_id' => '2',
           'config_section_id' => '3',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'admin_email' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '150',
             'config_section_id' => '3',
             'name' => 'admin_email',
             'value' => 'admin@yoursite.com',
             'presentation' => 'varchar',
             'description' => 'Admin email',
             'php_validation' => 'return preg_match("/^[a-zA-Z0-9_\\-\\.]+@[a-zA-Z0-9_-]+?.[a-zA-Z0-9_]{2,}(\\.\\w{2})?$/i", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^a-zA-Z0-9_\\.@-]/.test(value)));',
          )),
          'admin_password' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '3',
             'config_section_id' => '3',
             'name' => 'admin_password',
             'value' => 'a05a633fd922772543c29444f730db4708f8d907',
             'presentation' => 'hidden',
             'description' => 'Admin password',
             'php_validation' => 'return !preg_match("/[^a-zA-Z0-9_]/", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^a-zA-Z0-9_]/.test(value) ) );',
          )),
          'admin_username' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '2',
             'config_section_id' => '3',
             'name' => 'admin_username',
             'value' => 'admin',
             'presentation' => 'varchar',
             'description' => 'Admin username',
             'php_validation' => 'return !preg_match("/[^a-zA-Z0-9_]/", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^a-zA-Z0-9_]/.test(value) ) );',
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
      'additional' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '22',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'additional',
           'label' => 'Advanced Settings',
           'parent_section_id' => '2',
           'config_section_id' => '22',
        )),
         'sub_sections' => 
        array (
          'profile_list' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '21',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'profile_list',
               'label' => 'Profile List Settings',
               'parent_section_id' => '22',
               'config_section_id' => '21',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'display_looking_for_hotlist' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '267',
                 'config_section_id' => '21',
                 'name' => 'display_looking_for_hotlist',
                 'value' => false,
                 'presentation' => 'checkbox',
                 'description' => 'Members can see only profiles of preferred sex in Hotlist',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'display_only_looking_for' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '266',
                 'config_section_id' => '21',
                 'name' => 'display_only_looking_for',
                 'value' => false,
                 'presentation' => 'checkbox',
                 'description' => 'Members can see only profiles of preferred sex in Profile lists',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'display_photo_count' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '180',
                 'config_section_id' => '21',
                 'name' => 'display_photo_count',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Display photo count',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'display_sign_up_date' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '20',
                 'config_section_id' => '21',
                 'name' => 'display_sign_up_date',
                 'value' => false,
                 'presentation' => 'checkbox',
                 'description' => 'Display join date',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'gender_exclusion' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '321',
                 'config_section_id' => '21',
                 'name' => 'gender_exclusion',
                 'value' => false,
                 'presentation' => 'checkbox',
                 'description' => 'Disable same-gender interactions ',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'hot_list_limit' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '264',
                 'config_section_id' => '21',
                 'name' => 'hot_list_limit',
                 'value' => 0,
                 'presentation' => 'integer',
                 'description' => 'Number of profiles in hot list',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'intellectual_search' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '183',
                 'config_section_id' => '21',
                 'name' => 'intellectual_search',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Search for couples',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'nav_per_page' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '19',
                 'config_section_id' => '21',
                 'name' => 'nav_per_page',
                 'value' => 6,
                 'presentation' => 'integer',
                 'description' => 'Pages range in profile lists navigation',
                 'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
                 'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
              )),
              'new_members_period' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '31',
                 'config_section_id' => '21',
                 'name' => 'new_members_period',
                 'value' => 100,
                 'presentation' => 'integer',
                 'description' => 'New Members period (days)',
                 'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
                 'js_validation' => NULL,
              )),
              'online_gender_separate' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '25',
                 'config_section_id' => '21',
                 'name' => 'online_gender_separate',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Divide online profile lists by gender',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'order' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '44',
                 'config_section_id' => '21',
                 'name' => 'order',
                 'value' => 'with photo|last activity|',
                 'presentation' => 'palette',
                 'description' => 'Profile List Order',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'quick_search_location_type' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '110',
                 'config_section_id' => '21',
                 'name' => 'quick_search_location_type',
                 'value' => 'city',
                 'presentation' => 'select',
                 'description' => 'Quick search location',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'result_per_page' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '16',
                 'config_section_id' => '21',
                 'name' => 'result_per_page',
                 'value' => 15,
                 'presentation' => 'integer',
                 'description' => 'Number of profiles per page in search results, matches, etc ',
                 'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
                 'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
              )),
              'search_distance_unit' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '29',
                 'config_section_id' => '21',
                 'name' => 'search_distance_unit',
                 'value' => 'km',
                 'presentation' => 'select',
                 'description' => 'Distance unit in proximity search',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'show_last_act' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '17',
                 'config_section_id' => '21',
                 'name' => 'show_last_act',
                 'value' => 'all',
                 'presentation' => 'select',
                 'description' => 'Display last activity',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'show_last_act_day' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '18',
                 'config_section_id' => '21',
                 'name' => 'show_last_act_day',
                 'value' => 0,
                 'presentation' => 'integer',
                 'description' => 'Hide last activity info after (days)',
                 'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
                 'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
              )),
              'simple_friend_list_count' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '234',
                 'config_section_id' => '21',
                 'name' => 'simple_friend_list_count',
                 'value' => 12,
                 'presentation' => 'integer',
                 'description' => 'Number of friends on <b>Profile View</b> page',
                 'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
                 'js_validation' => NULL,
              )),
              'view_mode' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '21',
                 'config_section_id' => '21',
                 'name' => 'view_mode',
                 'value' => 'gallery',
                 'presentation' => 'select',
                 'description' => 'Default profile lists view mode',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
            ),
             'config_values' => 
            array (
              'order' => 
              array (
                0 => 
                array (
                  'config_id' => '44',
                  'value' => 'last activity',
                  'label' => 'last activity',
                  'name' => 'order',
                ),
                1 => 
                array (
                  'config_id' => '44',
                  'value' => 'paid members',
                  'label' => 'paid members',
                  'name' => 'order',
                ),
                2 => 
                array (
                  'config_id' => '44',
                  'value' => 'with photo',
                  'label' => 'with photo',
                  'name' => 'order',
                ),
                3 => 
                array (
                  'config_id' => '44',
                  'value' => 'join date',
                  'label' => 'join date',
                  'name' => 'order',
                ),
              ),
              'quick_search_location_type' => 
              array (
                0 => 
                array (
                  'config_id' => '110',
                  'value' => 'zip',
                  'label' => 'Search by Zip',
                  'name' => 'quick_search_location_type',
                ),
                1 => 
                array (
                  'config_id' => '110',
                  'value' => 'city',
                  'label' => 'Search by City',
                  'name' => 'quick_search_location_type',
                ),
              ),
              'search_distance_unit' => 
              array (
                0 => 
                array (
                  'config_id' => '29',
                  'value' => 'mile',
                  'label' => 'Mile',
                  'name' => 'search_distance_unit',
                ),
                1 => 
                array (
                  'config_id' => '29',
                  'value' => 'km',
                  'label' => 'Km',
                  'name' => 'search_distance_unit',
                ),
              ),
              'show_last_act' => 
              array (
                0 => 
                array (
                  'config_id' => '17',
                  'value' => 'all',
                  'label' => 'Always display',
                  'name' => 'show_last_act',
                ),
                1 => 
                array (
                  'config_id' => '17',
                  'value' => 'day',
                  'label' => 'Hide after days',
                  'name' => 'show_last_act',
                ),
                2 => 
                array (
                  'config_id' => '17',
                  'value' => 'off',
                  'label' => 'Always hide',
                  'name' => 'show_last_act',
                ),
              ),
              'view_mode' => 
              array (
                0 => 
                array (
                  'config_id' => '21',
                  'value' => 'gallery',
                  'label' => 'Gallery',
                  'name' => 'view_mode',
                ),
                1 => 
                array (
                  'config_id' => '21',
                  'value' => 'details',
                  'label' => 'Profile Details',
                  'name' => 'view_mode',
                ),
              ),
            ),
          )),
          'profile' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '23',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'profile',
               'label' => 'Autologin/Logout Settings',
               'parent_section_id' => '22',
               'config_section_id' => '23',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'allow_emailverify_no_access' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '100',
                 'config_section_id' => '23',
                 'name' => 'allow_emailverify_no_access',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Members with unverified email address are allowed to access the site',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'allow_emailverify_undefined_access' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '101',
                 'config_section_id' => '23',
                 'name' => 'allow_emailverify_undefined_access',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Members with emails status pending verification are allowed to access the site',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'allow_lang_switch' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '151',
                 'config_section_id' => '23',
                 'name' => 'allow_lang_switch',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Allow users to switch language',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'birthday_congratulation_email' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '322',
                 'config_section_id' => '23',
                 'name' => 'birthday_congratulation_email',
                 'value' => false,
                 'presentation' => 'checkbox',
                 'description' => 'Send automatic birthday congratulation email',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'captcha_on_join' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '50',
                 'config_section_id' => '23',
                 'name' => 'captcha_on_join',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Enable captcha image on join form',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'cookies_auth_exp_period_days' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '45',
                 'config_section_id' => '23',
                 'name' => 'cookies_auth_exp_period_days',
                 'value' => 7,
                 'presentation' => 'integer',
                 'description' => 'AutoLogin cookies expiration days count',
                 'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
                 'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
              )),
              'enable_no_follow' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '334',
                 'config_section_id' => '23',
                 'name' => 'enable_no_follow',
                 'value' => false,
                 'presentation' => 'checkbox',
                 'description' => 'Enable nofollow value for external links',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'invited_to_friends' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '181',
                 'config_section_id' => '23',
                 'name' => 'invited_to_friends',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Add invited members to profile friends',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'member_logout_sec' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '49',
                 'config_section_id' => '23',
                 'name' => 'member_logout_sec',
                 'value' => 1800,
                 'presentation' => 'integer',
                 'description' => 'Time before automatic member logout (sec)',
                 'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
                 'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
              )),
              'not_reviewed_profile_access' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '99',
                 'config_section_id' => '23',
                 'name' => 'not_reviewed_profile_access',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Allow not reviewed profiles to use site',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'profile_review_enabled' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '323',
                 'config_section_id' => '23',
                 'name' => 'profile_review_enabled',
                 'value' => false,
                 'presentation' => 'checkbox',
                 'description' => 'Updated profile approval',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'send_mail_when_reviewed' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '97',
                 'config_section_id' => '23',
                 'name' => 'send_mail_when_reviewed',
                 'value' => false,
                 'presentation' => 'checkbox',
                 'description' => 'Send \'Profile reviewed\' notification',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'send_onjoin_mail' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '240',
                 'config_section_id' => '23',
                 'name' => 'send_onjoin_mail',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Notify admin about new registered members',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'send_welcome_letter' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '47',
                 'config_section_id' => '23',
                 'name' => 'send_welcome_letter',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Send welcome letter on registration',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'suspend_registration' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '195',
                 'config_section_id' => '23',
                 'name' => 'suspend_registration',
                 'value' => false,
                 'presentation' => 'checkbox',
                 'description' => 'Suspend new members registration',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
            ),
             'config_values' => 
            array (
            ),
          )),
          'mailbox' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '28',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'mailbox',
               'label' => 'Mailbox Settings',
               'parent_section_id' => '22',
               'config_section_id' => '28',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'mails_per_page' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '30',
                 'config_section_id' => '28',
                 'name' => 'mails_per_page',
                 'value' => 15,
                 'presentation' => 'integer',
                 'description' => 'Number of messages per page in profile mailbox',
                 'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
                 'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
              )),
            ),
             'config_values' => 
            array (
            ),
          )),
          'tips' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '29',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'tips',
               'label' => 'Important Tips Settings',
               'parent_section_id' => '22',
               'config_section_id' => '29',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'show_default_tip' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '35',
                 'config_section_id' => '29',
                 'name' => 'show_default_tip',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Show "Last news" tip',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'show_poll_tip' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '224',
                 'config_section_id' => '29',
                 'name' => 'show_poll_tip',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Show "Poll suggestion" tip',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'show_sms_tip' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '166',
                 'config_section_id' => '29',
                 'name' => 'show_sms_tip',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Show "Services paid by SMS" tip',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'show_upgrade_tip' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '34',
                 'config_section_id' => '29',
                 'name' => 'show_upgrade_tip',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Show "Upgrade" tip ',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'show_upload_media_tip' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '33',
                 'config_section_id' => '29',
                 'name' => 'show_upload_media_tip',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Show "Upload Media" tip',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'show_upload_photo_tip' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '32',
                 'config_section_id' => '29',
                 'name' => 'show_upload_photo_tip',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Show "Upload Photos" tip',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
            ),
             'config_values' => 
            array (
            ),
          )),
          'subscribe' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '33',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'subscribe',
               'label' => 'Subscribe Page Settings',
               'parent_section_id' => '22',
               'config_section_id' => '33',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'show_permissions_diagram' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '46',
                 'config_section_id' => '33',
                 'name' => 'show_permissions_diagram',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Show memberships comparison diagram',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
            ),
             'config_values' => 
            array (
            ),
          )),
          'tags' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '58',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'tags',
               'label' => 'Tags settings',
               'parent_section_id' => '22',
               'config_section_id' => '58',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'navigator_tags_count' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '174',
                 'config_section_id' => '58',
                 'name' => 'navigator_tags_count',
                 'value' => 5,
                 'presentation' => 'integer',
                 'description' => 'Number of tags in tag navigator',
                 'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
                 'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
              )),
            ),
             'config_values' => 
            array (
            ),
          )),
          'im' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '77',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'im',
               'label' => 'IM Settings',
               'parent_section_id' => '22',
               'config_section_id' => '77',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'enable_sound' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '242',
                 'config_section_id' => '77',
                 'name' => 'enable_sound',
                 'value' => true,
                 'presentation' => 'checkbox',
                 'description' => 'Enable sound in IM',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
            ),
             'config_values' => 
            array (
            ),
          )),
          'speed_dating' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '76',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'speed_dating',
               'label' => 'Speed Dating Settings',
               'parent_section_id' => '22',
               'config_section_id' => '76',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'session_length' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '241',
                 'config_section_id' => '76',
                 'name' => 'session_length',
                 'value' => 5,
                 'presentation' => 'integer',
                 'description' => 'One dating session duration (min)',
                 'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
                 'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
              )),
            ),
             'config_values' => 
            array (
            ),
          )),
          'groups' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '78',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'groups',
               'label' => 'Group Settings',
               'parent_section_id' => '22',
               'config_section_id' => '78',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'result_per_page' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '244',
                 'config_section_id' => '78',
                 'name' => 'result_per_page',
                 'value' => 10,
                 'presentation' => 'integer',
                 'description' => 'Number of groups per page in group list',
                 'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
                 'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
              )),
            ),
             'config_values' => 
            array (
            ),
          )),
          'shoutbox' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '79',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'shoutbox',
               'label' => 'Shoutbox Settings',
               'parent_section_id' => '22',
               'config_section_id' => '79',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'post_count' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '245',
                 'config_section_id' => '79',
                 'name' => 'post_count',
                 'value' => 10,
                 'presentation' => 'integer',
                 'description' => 'Number of posts in shoutbox',
                 'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
                 'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
              )),
            ),
             'config_values' => 
            array (
            ),
          )),
          'googlemaps' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '95',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'googlemaps',
               'label' => 'Google Maps',
               'parent_section_id' => '22',
               'config_section_id' => '95',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'google_map_api_key' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '327',
                 'config_section_id' => '95',
                 'name' => 'google_map_api_key',
                 'value' => '',
                 'presentation' => 'varchar',
                 'description' => 'Google Map API Key',
                 'php_validation' => 'return preg_match("/^[^ \\t]*$/", $value);	',
                 'js_validation' => 'return /^[^ \\t]*$/.test(value);',
              )),
            ),
             'config_values' => 
            array (
            ),
          )),
        ),
         'configs' => 
        array (
        ),
         'config_values' => 
        array (
        ),
      )),
      'official' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '30',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'official',
           'label' => 'Official info ',
           'parent_section_id' => '2',
           'config_section_id' => '30',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'date_format' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '243',
             'config_section_id' => '30',
             'name' => 'date_format',
             'value' => 'd-m-y',
             'presentation' => 'select',
             'description' => 'Date format',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'military_time' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '201',
             'config_section_id' => '30',
             'name' => 'military_time',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Military Time',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'no_reply_email' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '235',
             'config_section_id' => '30',
             'name' => 'no_reply_email',
             'value' => 'noreply@yoursite.com',
             'presentation' => 'varchar',
             'description' => 'No reply email',
             'php_validation' => 'return preg_match("/^[a-zA-Z0-9\\-\\.]+@[a-zA-Z0-9_-]+?.[a-zA-Z0-9]{2,}(\\.\\w{2})?$/i", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^a-zA-Z0-9\\.@-]/.test(value)));',
          )),
          'site_email_billing' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '120',
             'config_section_id' => '30',
             'name' => 'site_email_billing',
             'value' => 'billing@yoursite.com',
             'presentation' => 'varchar',
             'description' => 'Site billing email',
             'php_validation' => 'return preg_match("/^[a-zA-Z0-9\\-\\.]+@[a-zA-Z0-9_-]+?.[a-zA-Z0-9]{2,}(\\.\\w{2,3})?$/i", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^a-zA-Z0-9\\.@-]/.test(value)));',
          )),
          'site_email_main' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '36',
             'config_section_id' => '30',
             'name' => 'site_email_main',
             'value' => 'admin@yoursite.com',
             'presentation' => 'varchar',
             'description' => 'Site general email',
             'php_validation' => 'return preg_match("/^[a-zA-Z0-9\\-\\.]+@[a-zA-Z0-9_-]+?.[a-zA-Z0-9]{2,}(\\.\\w{2,3})?$/i", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^a-zA-Z0-9\\.@-]/.test(value)));',
          )),
          'site_email_support' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '119',
             'config_section_id' => '30',
             'name' => 'site_email_support',
             'value' => 'support@yoursite.com',
             'presentation' => 'varchar',
             'description' => 'Site support email',
             'php_validation' => 'return preg_match("/^[a-zA-Z0-9\\-\\.]+@[a-zA-Z0-9_-]+?.[a-zA-Z0-9_]{2,}(\\.\\w{2,3})?$/i", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^a-zA-Z0-9\\.@-]/.test(value)));',
          )),
          'site_name' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '37',
             'config_section_id' => '30',
             'name' => 'site_name',
             'value' => 'Baby2Be.dk',
             'presentation' => 'varchar',
             'description' => 'Site name',
             'php_validation' => '',
             'js_validation' => '',
          )),
          'time_zone' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '154',
             'config_section_id' => '30',
             'name' => 'time_zone',
             'value' => 'Europe/Copenhagen',
             'presentation' => 'select',
             'description' => 'Site time zone',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
          'date_format' => 
          array (
            0 => 
            array (
              'config_id' => '243',
              'value' => 'd-m-y',
              'label' => 'Day-Month-Year',
              'name' => 'date_format',
            ),
            1 => 
            array (
              'config_id' => '243',
              'value' => 'm-d-y',
              'label' => 'Month-Day-Year',
              'name' => 'date_format',
            ),
            2 => 
            array (
              'config_id' => '243',
              'value' => 'y-d-m',
              'label' => 'Year-Day-Month',
              'name' => 'date_format',
            ),
            3 => 
            array (
              'config_id' => '243',
              'value' => 'y-m-d',
              'label' => 'Year-Month-Day',
              'name' => 'date_format',
            ),
          ),
          'time_zone' => 
          array (
            0 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/West',
              'label' => 'Australia/West',
              'name' => 'time_zone',
            ),
            1 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Victoria',
              'label' => 'Australia/Victoria',
              'name' => 'time_zone',
            ),
            2 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Tasmania',
              'label' => 'Australia/Tasmania',
              'name' => 'time_zone',
            ),
            3 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Sydney',
              'label' => 'Australia/Sydney',
              'name' => 'time_zone',
            ),
            4 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/South',
              'label' => 'Australia/South',
              'name' => 'time_zone',
            ),
            5 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Queensland',
              'label' => 'Australia/Queensland',
              'name' => 'time_zone',
            ),
            6 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Perth',
              'label' => 'Australia/Perth',
              'name' => 'time_zone',
            ),
            7 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/North',
              'label' => 'Australia/North',
              'name' => 'time_zone',
            ),
            8 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/NSW',
              'label' => 'Australia/NSW',
              'name' => 'time_zone',
            ),
            9 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Melbourne',
              'label' => 'Australia/Melbourne',
              'name' => 'time_zone',
            ),
            10 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Lord_Howe',
              'label' => 'Australia/Lord_Howe',
              'name' => 'time_zone',
            ),
            11 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Lindeman',
              'label' => 'Australia/Lindeman',
              'name' => 'time_zone',
            ),
            12 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/LHI',
              'label' => 'Australia/LHI',
              'name' => 'time_zone',
            ),
            13 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Hobart',
              'label' => 'Australia/Hobart',
              'name' => 'time_zone',
            ),
            14 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Darwin',
              'label' => 'Australia/Darwin',
              'name' => 'time_zone',
            ),
            15 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Currie',
              'label' => 'Australia/Currie',
              'name' => 'time_zone',
            ),
            16 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Canberra',
              'label' => 'Australia/Canberra',
              'name' => 'time_zone',
            ),
            17 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Broken_Hill',
              'label' => 'Australia/Broken_Hill',
              'name' => 'time_zone',
            ),
            18 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Brisbane',
              'label' => 'Australia/Brisbane',
              'name' => 'time_zone',
            ),
            19 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Adelaide',
              'label' => 'Australia/Adelaide',
              'name' => 'time_zone',
            ),
            20 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/ACT',
              'label' => 'Australia/ACT',
              'name' => 'time_zone',
            ),
            21 => 
            array (
              'config_id' => '154',
              'value' => 'Atlantic/Stanley',
              'label' => 'Atlantic/Stanley',
              'name' => 'time_zone',
            ),
            22 => 
            array (
              'config_id' => '154',
              'value' => 'Atlantic/St_Helena',
              'label' => 'Atlantic/St_Helena',
              'name' => 'time_zone',
            ),
            23 => 
            array (
              'config_id' => '154',
              'value' => 'Atlantic/South_Georgia',
              'label' => 'Atlantic/South_Georgia',
              'name' => 'time_zone',
            ),
            24 => 
            array (
              'config_id' => '154',
              'value' => 'Atlantic/Reykjavik',
              'label' => 'Atlantic/Reykjavik',
              'name' => 'time_zone',
            ),
            25 => 
            array (
              'config_id' => '154',
              'value' => 'Atlantic/Madeira',
              'label' => 'Atlantic/Madeira',
              'name' => 'time_zone',
            ),
            26 => 
            array (
              'config_id' => '154',
              'value' => 'Atlantic/Jan_Mayen',
              'label' => 'Atlantic/Jan_Mayen',
              'name' => 'time_zone',
            ),
            27 => 
            array (
              'config_id' => '154',
              'value' => 'Atlantic/Faeroe',
              'label' => 'Atlantic/Faeroe',
              'name' => 'time_zone',
            ),
            28 => 
            array (
              'config_id' => '154',
              'value' => 'Atlantic/Cape_Verde',
              'label' => 'Atlantic/Cape_Verde',
              'name' => 'time_zone',
            ),
            29 => 
            array (
              'config_id' => '154',
              'value' => 'Atlantic/Canary',
              'label' => 'Atlantic/Canary',
              'name' => 'time_zone',
            ),
            30 => 
            array (
              'config_id' => '154',
              'value' => 'Atlantic/Bermuda',
              'label' => 'Atlantic/Bermuda',
              'name' => 'time_zone',
            ),
            31 => 
            array (
              'config_id' => '154',
              'value' => 'Atlantic/Azores',
              'label' => 'Atlantic/Azores',
              'name' => 'time_zone',
            ),
            32 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Yerevan',
              'label' => 'Asia/Yerevan',
              'name' => 'time_zone',
            ),
            33 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Yekaterinburg',
              'label' => 'Asia/Yekaterinburg',
              'name' => 'time_zone',
            ),
            34 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Yakutsk',
              'label' => 'Asia/Yakutsk',
              'name' => 'time_zone',
            ),
            35 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Vladivostok',
              'label' => 'Asia/Vladivostok',
              'name' => 'time_zone',
            ),
            36 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Vientiane',
              'label' => 'Asia/Vientiane',
              'name' => 'time_zone',
            ),
            37 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Urumqi',
              'label' => 'Asia/Urumqi',
              'name' => 'time_zone',
            ),
            38 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Ulan_Bator',
              'label' => 'Asia/Ulan_Bator',
              'name' => 'time_zone',
            ),
            39 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Ulaanbaatar',
              'label' => 'Asia/Ulaanbaatar',
              'name' => 'time_zone',
            ),
            40 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Ujung_Pandang',
              'label' => 'Asia/Ujung_Pandang',
              'name' => 'time_zone',
            ),
            41 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Tokyo',
              'label' => 'Asia/Tokyo',
              'name' => 'time_zone',
            ),
            42 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Thimphu',
              'label' => 'Asia/Thimphu',
              'name' => 'time_zone',
            ),
            43 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Thimbu',
              'label' => 'Asia/Thimbu',
              'name' => 'time_zone',
            ),
            44 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Tel_Aviv',
              'label' => 'Asia/Tel_Aviv',
              'name' => 'time_zone',
            ),
            45 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Tehran',
              'label' => 'Asia/Tehran',
              'name' => 'time_zone',
            ),
            46 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Tbilisi',
              'label' => 'Asia/Tbilisi',
              'name' => 'time_zone',
            ),
            47 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Tashkent',
              'label' => 'Asia/Tashkent',
              'name' => 'time_zone',
            ),
            48 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Taipei',
              'label' => 'Asia/Taipei',
              'name' => 'time_zone',
            ),
            49 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Singapore',
              'label' => 'Asia/Singapore',
              'name' => 'time_zone',
            ),
            50 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Shanghai',
              'label' => 'Asia/Shanghai',
              'name' => 'time_zone',
            ),
            51 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Seoul',
              'label' => 'Asia/Seoul',
              'name' => 'time_zone',
            ),
            52 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Samarkand',
              'label' => 'Asia/Samarkand',
              'name' => 'time_zone',
            ),
            53 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Sakhalin',
              'label' => 'Asia/Sakhalin',
              'name' => 'time_zone',
            ),
            54 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Saigon',
              'label' => 'Asia/Saigon',
              'name' => 'time_zone',
            ),
            55 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Riyadh',
              'label' => 'Asia/Riyadh',
              'name' => 'time_zone',
            ),
            56 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Rangoon',
              'label' => 'Asia/Rangoon',
              'name' => 'time_zone',
            ),
            57 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Qyzylorda',
              'label' => 'Asia/Qyzylorda',
              'name' => 'time_zone',
            ),
            58 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Qatar',
              'label' => 'Asia/Qatar',
              'name' => 'time_zone',
            ),
            59 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Pyongyang',
              'label' => 'Asia/Pyongyang',
              'name' => 'time_zone',
            ),
            60 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Pontianak',
              'label' => 'Asia/Pontianak',
              'name' => 'time_zone',
            ),
            61 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Phnom_Penh',
              'label' => 'Asia/Phnom_Penh',
              'name' => 'time_zone',
            ),
            62 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Oral',
              'label' => 'Asia/Oral',
              'name' => 'time_zone',
            ),
            63 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Omsk',
              'label' => 'Asia/Omsk',
              'name' => 'time_zone',
            ),
            64 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Novosibirsk',
              'label' => 'Asia/Novosibirsk',
              'name' => 'time_zone',
            ),
            65 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Nicosia',
              'label' => 'Asia/Nicosia',
              'name' => 'time_zone',
            ),
            66 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Muscat',
              'label' => 'Asia/Muscat',
              'name' => 'time_zone',
            ),
            67 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Manila',
              'label' => 'Asia/Manila',
              'name' => 'time_zone',
            ),
            68 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Makassar',
              'label' => 'Asia/Makassar',
              'name' => 'time_zone',
            ),
            69 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Magadan',
              'label' => 'Asia/Magadan',
              'name' => 'time_zone',
            ),
            70 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Macau',
              'label' => 'Asia/Macau',
              'name' => 'time_zone',
            ),
            71 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Macao',
              'label' => 'Asia/Macao',
              'name' => 'time_zone',
            ),
            72 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Kuwait',
              'label' => 'Asia/Kuwait',
              'name' => 'time_zone',
            ),
            73 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Kuching',
              'label' => 'Asia/Kuching',
              'name' => 'time_zone',
            ),
            74 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Kuala_Lumpur',
              'label' => 'Asia/Kuala_Lumpur',
              'name' => 'time_zone',
            ),
            75 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Krasnoyarsk',
              'label' => 'Asia/Krasnoyarsk',
              'name' => 'time_zone',
            ),
            76 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Katmandu',
              'label' => 'Asia/Katmandu',
              'name' => 'time_zone',
            ),
            77 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Kashgar',
              'label' => 'Asia/Kashgar',
              'name' => 'time_zone',
            ),
            78 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Karachi',
              'label' => 'Asia/Karachi',
              'name' => 'time_zone',
            ),
            79 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Kamchatka',
              'label' => 'Asia/Kamchatka',
              'name' => 'time_zone',
            ),
            80 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Kabul',
              'label' => 'Asia/Kabul',
              'name' => 'time_zone',
            ),
            81 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Jerusalem',
              'label' => 'Asia/Jerusalem',
              'name' => 'time_zone',
            ),
            82 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Jayapura',
              'label' => 'Asia/Jayapura',
              'name' => 'time_zone',
            ),
            83 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Jakarta',
              'label' => 'Asia/Jakarta',
              'name' => 'time_zone',
            ),
            84 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Istanbul',
              'label' => 'Asia/Istanbul',
              'name' => 'time_zone',
            ),
            85 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Irkutsk',
              'label' => 'Asia/Irkutsk',
              'name' => 'time_zone',
            ),
            86 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Hovd',
              'label' => 'Asia/Hovd',
              'name' => 'time_zone',
            ),
            87 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Hong_Kong',
              'label' => 'Asia/Hong_Kong',
              'name' => 'time_zone',
            ),
            88 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Harbin',
              'label' => 'Asia/Harbin',
              'name' => 'time_zone',
            ),
            89 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Gaza',
              'label' => 'Asia/Gaza',
              'name' => 'time_zone',
            ),
            90 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Dushanbe',
              'label' => 'Asia/Dushanbe',
              'name' => 'time_zone',
            ),
            91 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Dubai',
              'label' => 'Asia/Dubai',
              'name' => 'time_zone',
            ),
            92 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Dili',
              'label' => 'Asia/Dili',
              'name' => 'time_zone',
            ),
            93 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Dhaka',
              'label' => 'Asia/Dhaka',
              'name' => 'time_zone',
            ),
            94 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Damascus',
              'label' => 'Asia/Damascus',
              'name' => 'time_zone',
            ),
            95 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Dacca',
              'label' => 'Asia/Dacca',
              'name' => 'time_zone',
            ),
            96 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Colombo',
              'label' => 'Asia/Colombo',
              'name' => 'time_zone',
            ),
            97 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Chungking',
              'label' => 'Asia/Chungking',
              'name' => 'time_zone',
            ),
            98 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Chongqing',
              'label' => 'Asia/Chongqing',
              'name' => 'time_zone',
            ),
            99 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Choibalsan',
              'label' => 'Asia/Choibalsan',
              'name' => 'time_zone',
            ),
            100 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Calcutta',
              'label' => 'Asia/Calcutta',
              'name' => 'time_zone',
            ),
            101 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Brunei',
              'label' => 'Asia/Brunei',
              'name' => 'time_zone',
            ),
            102 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Bishkek',
              'label' => 'Asia/Bishkek',
              'name' => 'time_zone',
            ),
            103 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Beirut',
              'label' => 'Asia/Beirut',
              'name' => 'time_zone',
            ),
            104 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Bangkok',
              'label' => 'Asia/Bangkok',
              'name' => 'time_zone',
            ),
            105 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Baku',
              'label' => 'Asia/Baku',
              'name' => 'time_zone',
            ),
            106 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Bahrain',
              'label' => 'Asia/Bahrain',
              'name' => 'time_zone',
            ),
            107 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Baghdad',
              'label' => 'Asia/Baghdad',
              'name' => 'time_zone',
            ),
            108 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Ashkhabad',
              'label' => 'Asia/Ashkhabad',
              'name' => 'time_zone',
            ),
            109 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Ashgabat',
              'label' => 'Asia/Ashgabat',
              'name' => 'time_zone',
            ),
            110 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Aqtobe',
              'label' => 'Asia/Aqtobe',
              'name' => 'time_zone',
            ),
            111 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Aqtau',
              'label' => 'Asia/Aqtau',
              'name' => 'time_zone',
            ),
            112 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Anadyr',
              'label' => 'Asia/Anadyr',
              'name' => 'time_zone',
            ),
            113 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Amman',
              'label' => 'Asia/Amman',
              'name' => 'time_zone',
            ),
            114 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Almaty',
              'label' => 'Asia/Almaty',
              'name' => 'time_zone',
            ),
            115 => 
            array (
              'config_id' => '154',
              'value' => 'Asia/Aden',
              'label' => 'Asia/Aden',
              'name' => 'time_zone',
            ),
            116 => 
            array (
              'config_id' => '154',
              'value' => 'Antarctica/Vostok',
              'label' => 'Antarctica/Vostok',
              'name' => 'time_zone',
            ),
            117 => 
            array (
              'config_id' => '154',
              'value' => 'Antarctica/Syowa',
              'label' => 'Antarctica/Syowa',
              'name' => 'time_zone',
            ),
            118 => 
            array (
              'config_id' => '154',
              'value' => 'Antarctica/South_Pole',
              'label' => 'Antarctica/South_Pole',
              'name' => 'time_zone',
            ),
            119 => 
            array (
              'config_id' => '154',
              'value' => 'Antarctica/Rothera',
              'label' => 'Antarctica/Rothera',
              'name' => 'time_zone',
            ),
            120 => 
            array (
              'config_id' => '154',
              'value' => 'Antarctica/Palmer',
              'label' => 'Antarctica/Palmer',
              'name' => 'time_zone',
            ),
            121 => 
            array (
              'config_id' => '154',
              'value' => 'Antarctica/McMurdo',
              'label' => 'Antarctica/McMurdo',
              'name' => 'time_zone',
            ),
            122 => 
            array (
              'config_id' => '154',
              'value' => 'Antarctica/Mawson',
              'label' => 'Antarctica/Mawson',
              'name' => 'time_zone',
            ),
            123 => 
            array (
              'config_id' => '154',
              'value' => 'Antarctica/DumontDUrville',
              'label' => 'Antarctica/DumontDUrville',
              'name' => 'time_zone',
            ),
            124 => 
            array (
              'config_id' => '154',
              'value' => 'Antarctica/Davis',
              'label' => 'Antarctica/Davis',
              'name' => 'time_zone',
            ),
            125 => 
            array (
              'config_id' => '154',
              'value' => 'Antarctica/Casey',
              'label' => 'Antarctica/Casey',
              'name' => 'time_zone',
            ),
            126 => 
            array (
              'config_id' => '154',
              'value' => 'America/Yellowknife',
              'label' => 'America/Yellowknife',
              'name' => 'time_zone',
            ),
            127 => 
            array (
              'config_id' => '154',
              'value' => 'America/Yakutat',
              'label' => 'America/Yakutat',
              'name' => 'time_zone',
            ),
            128 => 
            array (
              'config_id' => '154',
              'value' => 'America/Winnipeg',
              'label' => 'America/Winnipeg',
              'name' => 'time_zone',
            ),
            129 => 
            array (
              'config_id' => '154',
              'value' => 'America/Whitehorse',
              'label' => 'America/Whitehorse',
              'name' => 'time_zone',
            ),
            130 => 
            array (
              'config_id' => '154',
              'value' => 'America/Virgin',
              'label' => 'America/Virgin',
              'name' => 'time_zone',
            ),
            131 => 
            array (
              'config_id' => '154',
              'value' => 'America/Vancouver',
              'label' => 'America/Vancouver',
              'name' => 'time_zone',
            ),
            132 => 
            array (
              'config_id' => '154',
              'value' => 'America/Tortola',
              'label' => 'America/Tortola',
              'name' => 'time_zone',
            ),
            133 => 
            array (
              'config_id' => '154',
              'value' => 'America/Toronto',
              'label' => 'America/Toronto',
              'name' => 'time_zone',
            ),
            134 => 
            array (
              'config_id' => '154',
              'value' => 'America/Tijuana',
              'label' => 'America/Tijuana',
              'name' => 'time_zone',
            ),
            135 => 
            array (
              'config_id' => '154',
              'value' => 'America/Thunder_Bay',
              'label' => 'America/Thunder_Bay',
              'name' => 'time_zone',
            ),
            136 => 
            array (
              'config_id' => '154',
              'value' => 'America/Thule',
              'label' => 'America/Thule',
              'name' => 'time_zone',
            ),
            137 => 
            array (
              'config_id' => '154',
              'value' => 'America/Tegucigalpa',
              'label' => 'America/Tegucigalpa',
              'name' => 'time_zone',
            ),
            138 => 
            array (
              'config_id' => '154',
              'value' => 'America/Swift_Current',
              'label' => 'America/Swift_Current',
              'name' => 'time_zone',
            ),
            139 => 
            array (
              'config_id' => '154',
              'value' => 'America/St_Vincent',
              'label' => 'America/St_Vincent',
              'name' => 'time_zone',
            ),
            140 => 
            array (
              'config_id' => '154',
              'value' => 'America/St_Thomas',
              'label' => 'America/St_Thomas',
              'name' => 'time_zone',
            ),
            141 => 
            array (
              'config_id' => '154',
              'value' => 'America/St_Lucia',
              'label' => 'America/St_Lucia',
              'name' => 'time_zone',
            ),
            142 => 
            array (
              'config_id' => '154',
              'value' => 'America/St_Kitts',
              'label' => 'America/St_Kitts',
              'name' => 'time_zone',
            ),
            143 => 
            array (
              'config_id' => '154',
              'value' => 'America/St_Johns',
              'label' => 'America/St_Johns',
              'name' => 'time_zone',
            ),
            144 => 
            array (
              'config_id' => '154',
              'value' => 'America/Shiprock',
              'label' => 'America/Shiprock',
              'name' => 'time_zone',
            ),
            145 => 
            array (
              'config_id' => '154',
              'value' => 'America/Scoresbysund',
              'label' => 'America/Scoresbysund',
              'name' => 'time_zone',
            ),
            146 => 
            array (
              'config_id' => '154',
              'value' => 'America/Sao_Paulo',
              'label' => 'America/Sao_Paulo',
              'name' => 'time_zone',
            ),
            147 => 
            array (
              'config_id' => '154',
              'value' => 'America/Santo_Domingo',
              'label' => 'America/Santo_Domingo',
              'name' => 'time_zone',
            ),
            148 => 
            array (
              'config_id' => '154',
              'value' => 'America/Santiago',
              'label' => 'America/Santiago',
              'name' => 'time_zone',
            ),
            149 => 
            array (
              'config_id' => '154',
              'value' => 'America/Rosario',
              'label' => 'America/Rosario',
              'name' => 'time_zone',
            ),
            150 => 
            array (
              'config_id' => '154',
              'value' => 'America/Rio_Branco',
              'label' => 'America/Rio_Branco',
              'name' => 'time_zone',
            ),
            151 => 
            array (
              'config_id' => '154',
              'value' => 'America/Regina',
              'label' => 'America/Regina',
              'name' => 'time_zone',
            ),
            152 => 
            array (
              'config_id' => '154',
              'value' => 'America/Recife',
              'label' => 'America/Recife',
              'name' => 'time_zone',
            ),
            153 => 
            array (
              'config_id' => '154',
              'value' => 'America/Rankin_Inlet',
              'label' => 'America/Rankin_Inlet',
              'name' => 'time_zone',
            ),
            154 => 
            array (
              'config_id' => '154',
              'value' => 'America/Rainy_River',
              'label' => 'America/Rainy_River',
              'name' => 'time_zone',
            ),
            155 => 
            array (
              'config_id' => '154',
              'value' => 'America/Puerto_Rico',
              'label' => 'America/Puerto_Rico',
              'name' => 'time_zone',
            ),
            156 => 
            array (
              'config_id' => '154',
              'value' => 'America/Porto_Velho',
              'label' => 'America/Porto_Velho',
              'name' => 'time_zone',
            ),
            157 => 
            array (
              'config_id' => '154',
              'value' => 'America/Porto_Acre',
              'label' => 'America/Porto_Acre',
              'name' => 'time_zone',
            ),
            158 => 
            array (
              'config_id' => '154',
              'value' => 'America/Port_of_Spain',
              'label' => 'America/Port_of_Spain',
              'name' => 'time_zone',
            ),
            159 => 
            array (
              'config_id' => '154',
              'value' => 'America/Port-au-Prince',
              'label' => 'America/Port-au-Prince',
              'name' => 'time_zone',
            ),
            160 => 
            array (
              'config_id' => '154',
              'value' => 'America/Phoenix',
              'label' => 'America/Phoenix',
              'name' => 'time_zone',
            ),
            161 => 
            array (
              'config_id' => '154',
              'value' => 'America/Paramaribo',
              'label' => 'America/Paramaribo',
              'name' => 'time_zone',
            ),
            162 => 
            array (
              'config_id' => '154',
              'value' => 'America/Pangnirtung',
              'label' => 'America/Pangnirtung',
              'name' => 'time_zone',
            ),
            163 => 
            array (
              'config_id' => '154',
              'value' => 'America/Panama',
              'label' => 'America/Panama',
              'name' => 'time_zone',
            ),
            164 => 
            array (
              'config_id' => '154',
              'value' => 'America/North_Dakota/Center',
              'label' => 'America/North_Dakota/Center',
              'name' => 'time_zone',
            ),
            165 => 
            array (
              'config_id' => '154',
              'value' => 'America/Noronha',
              'label' => 'America/Noronha',
              'name' => 'time_zone',
            ),
            166 => 
            array (
              'config_id' => '154',
              'value' => 'America/Nome',
              'label' => 'America/Nome',
              'name' => 'time_zone',
            ),
            167 => 
            array (
              'config_id' => '154',
              'value' => 'America/Nipigon',
              'label' => 'America/Nipigon',
              'name' => 'time_zone',
            ),
            168 => 
            array (
              'config_id' => '154',
              'value' => 'America/New_York',
              'label' => 'America/New_York',
              'name' => 'time_zone',
            ),
            169 => 
            array (
              'config_id' => '154',
              'value' => 'America/Nassau',
              'label' => 'America/Nassau',
              'name' => 'time_zone',
            ),
            170 => 
            array (
              'config_id' => '154',
              'value' => 'America/Montserrat',
              'label' => 'America/Montserrat',
              'name' => 'time_zone',
            ),
            171 => 
            array (
              'config_id' => '154',
              'value' => 'America/Montreal',
              'label' => 'America/Montreal',
              'name' => 'time_zone',
            ),
            172 => 
            array (
              'config_id' => '154',
              'value' => 'America/Montevideo',
              'label' => 'America/Montevideo',
              'name' => 'time_zone',
            ),
            173 => 
            array (
              'config_id' => '154',
              'value' => 'America/Monterrey',
              'label' => 'America/Monterrey',
              'name' => 'time_zone',
            ),
            174 => 
            array (
              'config_id' => '154',
              'value' => 'America/Miquelon',
              'label' => 'America/Miquelon',
              'name' => 'time_zone',
            ),
            175 => 
            array (
              'config_id' => '154',
              'value' => 'America/Mexico_City',
              'label' => 'America/Mexico_City',
              'name' => 'time_zone',
            ),
            176 => 
            array (
              'config_id' => '154',
              'value' => 'America/Merida',
              'label' => 'America/Merida',
              'name' => 'time_zone',
            ),
            177 => 
            array (
              'config_id' => '154',
              'value' => 'America/Menominee',
              'label' => 'America/Menominee',
              'name' => 'time_zone',
            ),
            178 => 
            array (
              'config_id' => '154',
              'value' => 'America/Mendoza',
              'label' => 'America/Mendoza',
              'name' => 'time_zone',
            ),
            179 => 
            array (
              'config_id' => '154',
              'value' => 'America/Mazatlan',
              'label' => 'America/Mazatlan',
              'name' => 'time_zone',
            ),
            180 => 
            array (
              'config_id' => '154',
              'value' => 'America/Martinique',
              'label' => 'America/Martinique',
              'name' => 'time_zone',
            ),
            181 => 
            array (
              'config_id' => '154',
              'value' => 'America/Manaus',
              'label' => 'America/Manaus',
              'name' => 'time_zone',
            ),
            182 => 
            array (
              'config_id' => '154',
              'value' => 'America/Managua',
              'label' => 'America/Managua',
              'name' => 'time_zone',
            ),
            183 => 
            array (
              'config_id' => '154',
              'value' => 'America/Maceio',
              'label' => 'America/Maceio',
              'name' => 'time_zone',
            ),
            184 => 
            array (
              'config_id' => '154',
              'value' => 'America/Louisville',
              'label' => 'America/Louisville',
              'name' => 'time_zone',
            ),
            185 => 
            array (
              'config_id' => '154',
              'value' => 'America/Los_Angeles',
              'label' => 'America/Los_Angeles',
              'name' => 'time_zone',
            ),
            186 => 
            array (
              'config_id' => '154',
              'value' => 'America/Lima',
              'label' => 'America/Lima',
              'name' => 'time_zone',
            ),
            187 => 
            array (
              'config_id' => '154',
              'value' => 'America/La_Paz',
              'label' => 'America/La_Paz',
              'name' => 'time_zone',
            ),
            188 => 
            array (
              'config_id' => '154',
              'value' => 'America/Knox_IN',
              'label' => 'America/Knox_IN',
              'name' => 'time_zone',
            ),
            189 => 
            array (
              'config_id' => '154',
              'value' => 'America/Kentucky/Monticello',
              'label' => 'America/Kentucky/Monticello',
              'name' => 'time_zone',
            ),
            190 => 
            array (
              'config_id' => '154',
              'value' => 'America/Kentucky/Louisville',
              'label' => 'America/Kentucky/Louisville',
              'name' => 'time_zone',
            ),
            191 => 
            array (
              'config_id' => '154',
              'value' => 'America/Juneau',
              'label' => 'America/Juneau',
              'name' => 'time_zone',
            ),
            192 => 
            array (
              'config_id' => '154',
              'value' => 'America/Jujuy',
              'label' => 'America/Jujuy',
              'name' => 'time_zone',
            ),
            193 => 
            array (
              'config_id' => '154',
              'value' => 'America/Jamaica',
              'label' => 'America/Jamaica',
              'name' => 'time_zone',
            ),
            194 => 
            array (
              'config_id' => '154',
              'value' => 'America/Iqaluit',
              'label' => 'America/Iqaluit',
              'name' => 'time_zone',
            ),
            195 => 
            array (
              'config_id' => '154',
              'value' => 'America/Inuvik',
              'label' => 'America/Inuvik',
              'name' => 'time_zone',
            ),
            196 => 
            array (
              'config_id' => '154',
              'value' => 'America/Indianapolis',
              'label' => 'America/Indianapolis',
              'name' => 'time_zone',
            ),
            197 => 
            array (
              'config_id' => '154',
              'value' => 'America/Indiana/Vevay',
              'label' => 'America/Indiana/Vevay',
              'name' => 'time_zone',
            ),
            198 => 
            array (
              'config_id' => '154',
              'value' => 'America/Indiana/Marengo',
              'label' => 'America/Indiana/Marengo',
              'name' => 'time_zone',
            ),
            199 => 
            array (
              'config_id' => '154',
              'value' => 'America/Indiana/Knox',
              'label' => 'America/Indiana/Knox',
              'name' => 'time_zone',
            ),
            200 => 
            array (
              'config_id' => '154',
              'value' => 'America/Indiana/Indianapolis',
              'label' => 'America/Indiana/Indianapolis',
              'name' => 'time_zone',
            ),
            201 => 
            array (
              'config_id' => '154',
              'value' => 'America/Hermosillo',
              'label' => 'America/Hermosillo',
              'name' => 'time_zone',
            ),
            202 => 
            array (
              'config_id' => '154',
              'value' => 'America/Havana',
              'label' => 'America/Havana',
              'name' => 'time_zone',
            ),
            203 => 
            array (
              'config_id' => '154',
              'value' => 'America/Halifax',
              'label' => 'America/Halifax',
              'name' => 'time_zone',
            ),
            204 => 
            array (
              'config_id' => '154',
              'value' => 'America/Guyana',
              'label' => 'America/Guyana',
              'name' => 'time_zone',
            ),
            205 => 
            array (
              'config_id' => '154',
              'value' => 'America/Guayaquil',
              'label' => 'America/Guayaquil',
              'name' => 'time_zone',
            ),
            206 => 
            array (
              'config_id' => '154',
              'value' => 'America/Guatemala',
              'label' => 'America/Guatemala',
              'name' => 'time_zone',
            ),
            207 => 
            array (
              'config_id' => '154',
              'value' => 'America/Guadeloupe',
              'label' => 'America/Guadeloupe',
              'name' => 'time_zone',
            ),
            208 => 
            array (
              'config_id' => '154',
              'value' => 'America/Grenada',
              'label' => 'America/Grenada',
              'name' => 'time_zone',
            ),
            209 => 
            array (
              'config_id' => '154',
              'value' => 'America/Grand_Turk',
              'label' => 'America/Grand_Turk',
              'name' => 'time_zone',
            ),
            210 => 
            array (
              'config_id' => '154',
              'value' => 'America/Goose_Bay',
              'label' => 'America/Goose_Bay',
              'name' => 'time_zone',
            ),
            211 => 
            array (
              'config_id' => '154',
              'value' => 'America/Godthab',
              'label' => 'America/Godthab',
              'name' => 'time_zone',
            ),
            212 => 
            array (
              'config_id' => '154',
              'value' => 'America/Glace_Bay',
              'label' => 'America/Glace_Bay',
              'name' => 'time_zone',
            ),
            213 => 
            array (
              'config_id' => '154',
              'value' => 'America/Fortaleza',
              'label' => 'America/Fortaleza',
              'name' => 'time_zone',
            ),
            214 => 
            array (
              'config_id' => '154',
              'value' => 'America/Fort_Wayne',
              'label' => 'America/Fort_Wayne',
              'name' => 'time_zone',
            ),
            215 => 
            array (
              'config_id' => '154',
              'value' => 'America/Ensenada',
              'label' => 'America/Ensenada',
              'name' => 'time_zone',
            ),
            216 => 
            array (
              'config_id' => '154',
              'value' => 'America/El_Salvador',
              'label' => 'America/El_Salvador',
              'name' => 'time_zone',
            ),
            217 => 
            array (
              'config_id' => '154',
              'value' => 'America/Eirunepe',
              'label' => 'America/Eirunepe',
              'name' => 'time_zone',
            ),
            218 => 
            array (
              'config_id' => '154',
              'value' => 'America/Edmonton',
              'label' => 'America/Edmonton',
              'name' => 'time_zone',
            ),
            219 => 
            array (
              'config_id' => '154',
              'value' => 'America/Dominica',
              'label' => 'America/Dominica',
              'name' => 'time_zone',
            ),
            220 => 
            array (
              'config_id' => '154',
              'value' => 'America/Detroit',
              'label' => 'America/Detroit',
              'name' => 'time_zone',
            ),
            221 => 
            array (
              'config_id' => '154',
              'value' => 'America/Denver',
              'label' => 'America/Denver',
              'name' => 'time_zone',
            ),
            222 => 
            array (
              'config_id' => '154',
              'value' => 'America/Dawson_Creek',
              'label' => 'America/Dawson_Creek',
              'name' => 'time_zone',
            ),
            223 => 
            array (
              'config_id' => '154',
              'value' => 'America/Dawson',
              'label' => 'America/Dawson',
              'name' => 'time_zone',
            ),
            224 => 
            array (
              'config_id' => '154',
              'value' => 'America/Danmarkshavn',
              'label' => 'America/Danmarkshavn',
              'name' => 'time_zone',
            ),
            225 => 
            array (
              'config_id' => '154',
              'value' => 'America/Curacao',
              'label' => 'America/Curacao',
              'name' => 'time_zone',
            ),
            226 => 
            array (
              'config_id' => '154',
              'value' => 'America/Cuiaba',
              'label' => 'America/Cuiaba',
              'name' => 'time_zone',
            ),
            227 => 
            array (
              'config_id' => '154',
              'value' => 'America/Costa_Rica',
              'label' => 'America/Costa_Rica',
              'name' => 'time_zone',
            ),
            228 => 
            array (
              'config_id' => '154',
              'value' => 'America/Cordoba',
              'label' => 'America/Cordoba',
              'name' => 'time_zone',
            ),
            229 => 
            array (
              'config_id' => '154',
              'value' => 'America/Coral_Harbour',
              'label' => 'America/Coral_Harbour',
              'name' => 'time_zone',
            ),
            230 => 
            array (
              'config_id' => '154',
              'value' => 'America/Chihuahua',
              'label' => 'America/Chihuahua',
              'name' => 'time_zone',
            ),
            231 => 
            array (
              'config_id' => '154',
              'value' => 'America/Chicago',
              'label' => 'America/Chicago',
              'name' => 'time_zone',
            ),
            232 => 
            array (
              'config_id' => '154',
              'value' => 'America/Cayman',
              'label' => 'America/Cayman',
              'name' => 'time_zone',
            ),
            233 => 
            array (
              'config_id' => '154',
              'value' => 'America/Cayenne',
              'label' => 'America/Cayenne',
              'name' => 'time_zone',
            ),
            234 => 
            array (
              'config_id' => '154',
              'value' => 'America/Catamarca',
              'label' => 'America/Catamarca',
              'name' => 'time_zone',
            ),
            235 => 
            array (
              'config_id' => '154',
              'value' => 'America/Caracas',
              'label' => 'America/Caracas',
              'name' => 'time_zone',
            ),
            236 => 
            array (
              'config_id' => '154',
              'value' => 'America/Cancun',
              'label' => 'America/Cancun',
              'name' => 'time_zone',
            ),
            237 => 
            array (
              'config_id' => '154',
              'value' => 'America/Campo_Grande',
              'label' => 'America/Campo_Grande',
              'name' => 'time_zone',
            ),
            238 => 
            array (
              'config_id' => '154',
              'value' => 'America/Cambridge_Bay',
              'label' => 'America/Cambridge_Bay',
              'name' => 'time_zone',
            ),
            239 => 
            array (
              'config_id' => '154',
              'value' => 'America/Buenos_Aires',
              'label' => 'America/Buenos_Aires',
              'name' => 'time_zone',
            ),
            240 => 
            array (
              'config_id' => '154',
              'value' => 'America/Boise',
              'label' => 'America/Boise',
              'name' => 'time_zone',
            ),
            241 => 
            array (
              'config_id' => '154',
              'value' => 'America/Bogota',
              'label' => 'America/Bogota',
              'name' => 'time_zone',
            ),
            242 => 
            array (
              'config_id' => '154',
              'value' => 'America/Boa_Vista',
              'label' => 'America/Boa_Vista',
              'name' => 'time_zone',
            ),
            243 => 
            array (
              'config_id' => '154',
              'value' => 'America/Belize',
              'label' => 'America/Belize',
              'name' => 'time_zone',
            ),
            244 => 
            array (
              'config_id' => '154',
              'value' => 'America/Belem',
              'label' => 'America/Belem',
              'name' => 'time_zone',
            ),
            245 => 
            array (
              'config_id' => '154',
              'value' => 'America/Barbados',
              'label' => 'America/Barbados',
              'name' => 'time_zone',
            ),
            246 => 
            array (
              'config_id' => '154',
              'value' => 'America/Bahia',
              'label' => 'America/Bahia',
              'name' => 'time_zone',
            ),
            247 => 
            array (
              'config_id' => '154',
              'value' => 'America/Atka',
              'label' => 'America/Atka',
              'name' => 'time_zone',
            ),
            248 => 
            array (
              'config_id' => '154',
              'value' => 'America/Asuncion',
              'label' => 'America/Asuncion',
              'name' => 'time_zone',
            ),
            249 => 
            array (
              'config_id' => '154',
              'value' => 'America/Aruba',
              'label' => 'America/Aruba',
              'name' => 'time_zone',
            ),
            250 => 
            array (
              'config_id' => '154',
              'value' => 'America/Argentina/Ushuaia',
              'label' => 'America/Argentina/Ushuaia',
              'name' => 'time_zone',
            ),
            251 => 
            array (
              'config_id' => '154',
              'value' => 'America/Argentina/Tucuman',
              'label' => 'America/Argentina/Tucuman',
              'name' => 'time_zone',
            ),
            252 => 
            array (
              'config_id' => '154',
              'value' => 'America/Argentina/San_Juan',
              'label' => 'America/Argentina/San_Juan',
              'name' => 'time_zone',
            ),
            253 => 
            array (
              'config_id' => '154',
              'value' => 'America/Argentina/Rio_Gallegos',
              'label' => 'America/Argentina/Rio_Gallegos',
              'name' => 'time_zone',
            ),
            254 => 
            array (
              'config_id' => '154',
              'value' => 'America/Argentina/Mendoza',
              'label' => 'America/Argentina/Mendoza',
              'name' => 'time_zone',
            ),
            255 => 
            array (
              'config_id' => '154',
              'value' => 'America/Argentina/La_Rioja',
              'label' => 'America/Argentina/La_Rioja',
              'name' => 'time_zone',
            ),
            256 => 
            array (
              'config_id' => '154',
              'value' => 'America/Argentina/Jujuy',
              'label' => 'America/Argentina/Jujuy',
              'name' => 'time_zone',
            ),
            257 => 
            array (
              'config_id' => '154',
              'value' => 'America/Argentina/Cordoba',
              'label' => 'America/Argentina/Cordoba',
              'name' => 'time_zone',
            ),
            258 => 
            array (
              'config_id' => '154',
              'value' => 'America/Argentina/ComodRivadavia',
              'label' => 'America/Argentina/ComodRivadavia',
              'name' => 'time_zone',
            ),
            259 => 
            array (
              'config_id' => '154',
              'value' => 'America/Argentina/Catamarca',
              'label' => 'America/Argentina/Catamarca',
              'name' => 'time_zone',
            ),
            260 => 
            array (
              'config_id' => '154',
              'value' => 'America/Argentina/Buenos_Aires',
              'label' => 'America/Argentina/Buenos_Aires',
              'name' => 'time_zone',
            ),
            261 => 
            array (
              'config_id' => '154',
              'value' => 'America/Araguaina',
              'label' => 'America/Araguaina',
              'name' => 'time_zone',
            ),
            262 => 
            array (
              'config_id' => '154',
              'value' => 'America/Antigua',
              'label' => 'America/Antigua',
              'name' => 'time_zone',
            ),
            263 => 
            array (
              'config_id' => '154',
              'value' => 'America/Anguilla',
              'label' => 'America/Anguilla',
              'name' => 'time_zone',
            ),
            264 => 
            array (
              'config_id' => '154',
              'value' => 'America/Anchorage',
              'label' => 'America/Anchorage',
              'name' => 'time_zone',
            ),
            265 => 
            array (
              'config_id' => '154',
              'value' => 'America/Adak',
              'label' => 'America/Adak',
              'name' => 'time_zone',
            ),
            266 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Windhoek',
              'label' => 'Africa/Windhoek',
              'name' => 'time_zone',
            ),
            267 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Tunis',
              'label' => 'Africa/Tunis',
              'name' => 'time_zone',
            ),
            268 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Tripoli',
              'label' => 'Africa/Tripoli',
              'name' => 'time_zone',
            ),
            269 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Timbuktu',
              'label' => 'Africa/Timbuktu',
              'name' => 'time_zone',
            ),
            270 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Sao_Tome',
              'label' => 'Africa/Sao_Tome',
              'name' => 'time_zone',
            ),
            271 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Porto-Novo',
              'label' => 'Africa/Porto-Novo',
              'name' => 'time_zone',
            ),
            272 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Ouagadougou',
              'label' => 'Africa/Ouagadougou',
              'name' => 'time_zone',
            ),
            273 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Nouakchott',
              'label' => 'Africa/Nouakchott',
              'name' => 'time_zone',
            ),
            274 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Niamey',
              'label' => 'Africa/Niamey',
              'name' => 'time_zone',
            ),
            275 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Ndjamena',
              'label' => 'Africa/Ndjamena',
              'name' => 'time_zone',
            ),
            276 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Nairobi',
              'label' => 'Africa/Nairobi',
              'name' => 'time_zone',
            ),
            277 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Monrovia',
              'label' => 'Africa/Monrovia',
              'name' => 'time_zone',
            ),
            278 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Mogadishu',
              'label' => 'Africa/Mogadishu',
              'name' => 'time_zone',
            ),
            279 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Mbabane',
              'label' => 'Africa/Mbabane',
              'name' => 'time_zone',
            ),
            280 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Maseru',
              'label' => 'Africa/Maseru',
              'name' => 'time_zone',
            ),
            281 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Maputo',
              'label' => 'Africa/Maputo',
              'name' => 'time_zone',
            ),
            282 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Malabo',
              'label' => 'Africa/Malabo',
              'name' => 'time_zone',
            ),
            283 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Lusaka',
              'label' => 'Africa/Lusaka',
              'name' => 'time_zone',
            ),
            284 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Lubumbashi',
              'label' => 'Africa/Lubumbashi',
              'name' => 'time_zone',
            ),
            285 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Luanda',
              'label' => 'Africa/Luanda',
              'name' => 'time_zone',
            ),
            286 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Lome',
              'label' => 'Africa/Lome',
              'name' => 'time_zone',
            ),
            287 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Libreville',
              'label' => 'Africa/Libreville',
              'name' => 'time_zone',
            ),
            288 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Lagos',
              'label' => 'Africa/Lagos',
              'name' => 'time_zone',
            ),
            289 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Kinshasa',
              'label' => 'Africa/Kinshasa',
              'name' => 'time_zone',
            ),
            290 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Kigali',
              'label' => 'Africa/Kigali',
              'name' => 'time_zone',
            ),
            291 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Khartoum',
              'label' => 'Africa/Khartoum',
              'name' => 'time_zone',
            ),
            292 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Kampala',
              'label' => 'Africa/Kampala',
              'name' => 'time_zone',
            ),
            293 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Johannesburg',
              'label' => 'Africa/Johannesburg',
              'name' => 'time_zone',
            ),
            294 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Harare',
              'label' => 'Africa/Harare',
              'name' => 'time_zone',
            ),
            295 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Gaborone',
              'label' => 'Africa/Gaborone',
              'name' => 'time_zone',
            ),
            296 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Freetown',
              'label' => 'Africa/Freetown',
              'name' => 'time_zone',
            ),
            297 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/El_Aaiun',
              'label' => 'Africa/El_Aaiun',
              'name' => 'time_zone',
            ),
            298 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Douala',
              'label' => 'Africa/Douala',
              'name' => 'time_zone',
            ),
            299 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Djibouti',
              'label' => 'Africa/Djibouti',
              'name' => 'time_zone',
            ),
            300 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Dar_es_Salaam',
              'label' => 'Africa/Dar_es_Salaam',
              'name' => 'time_zone',
            ),
            301 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Dakar',
              'label' => 'Africa/Dakar',
              'name' => 'time_zone',
            ),
            302 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Conakry',
              'label' => 'Africa/Conakry',
              'name' => 'time_zone',
            ),
            303 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Ceuta',
              'label' => 'Africa/Ceuta',
              'name' => 'time_zone',
            ),
            304 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Casablanca',
              'label' => 'Africa/Casablanca',
              'name' => 'time_zone',
            ),
            305 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Cairo',
              'label' => 'Africa/Cairo',
              'name' => 'time_zone',
            ),
            306 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Bujumbura',
              'label' => 'Africa/Bujumbura',
              'name' => 'time_zone',
            ),
            307 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Brazzaville',
              'label' => 'Africa/Brazzaville',
              'name' => 'time_zone',
            ),
            308 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Blantyre',
              'label' => 'Africa/Blantyre',
              'name' => 'time_zone',
            ),
            309 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Bissau',
              'label' => 'Africa/Bissau',
              'name' => 'time_zone',
            ),
            310 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Banjul',
              'label' => 'Africa/Banjul',
              'name' => 'time_zone',
            ),
            311 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Bangui',
              'label' => 'Africa/Bangui',
              'name' => 'time_zone',
            ),
            312 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Bamako',
              'label' => 'Africa/Bamako',
              'name' => 'time_zone',
            ),
            313 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Asmera',
              'label' => 'Africa/Asmera',
              'name' => 'time_zone',
            ),
            314 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Algiers',
              'label' => 'Africa/Algiers',
              'name' => 'time_zone',
            ),
            315 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Addis_Ababa',
              'label' => 'Africa/Addis_Ababa',
              'name' => 'time_zone',
            ),
            316 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Accra',
              'label' => 'Africa/Accra',
              'name' => 'time_zone',
            ),
            317 => 
            array (
              'config_id' => '154',
              'value' => 'Africa/Abidjan',
              'label' => 'Africa/Abidjan',
              'name' => 'time_zone',
            ),
            318 => 
            array (
              'config_id' => '154',
              'value' => 'Australia/Yancowinna',
              'label' => 'Australia/Yancowinna',
              'name' => 'time_zone',
            ),
            319 => 
            array (
              'config_id' => '154',
              'value' => 'Brazil/Acre',
              'label' => 'Brazil/Acre',
              'name' => 'time_zone',
            ),
            320 => 
            array (
              'config_id' => '154',
              'value' => 'Brazil/DeNoronha',
              'label' => 'Brazil/DeNoronha',
              'name' => 'time_zone',
            ),
            321 => 
            array (
              'config_id' => '154',
              'value' => 'Brazil/East',
              'label' => 'Brazil/East',
              'name' => 'time_zone',
            ),
            322 => 
            array (
              'config_id' => '154',
              'value' => 'Brazil/West',
              'label' => 'Brazil/West',
              'name' => 'time_zone',
            ),
            323 => 
            array (
              'config_id' => '154',
              'value' => 'Canada/Atlantic',
              'label' => 'Canada/Atlantic',
              'name' => 'time_zone',
            ),
            324 => 
            array (
              'config_id' => '154',
              'value' => 'Canada/Central',
              'label' => 'Canada/Central',
              'name' => 'time_zone',
            ),
            325 => 
            array (
              'config_id' => '154',
              'value' => 'Canada/East-Saskatchewan',
              'label' => 'Canada/East-Saskatchewan',
              'name' => 'time_zone',
            ),
            326 => 
            array (
              'config_id' => '154',
              'value' => 'Canada/Eastern',
              'label' => 'Canada/Eastern',
              'name' => 'time_zone',
            ),
            327 => 
            array (
              'config_id' => '154',
              'value' => 'Canada/Mountain',
              'label' => 'Canada/Mountain',
              'name' => 'time_zone',
            ),
            328 => 
            array (
              'config_id' => '154',
              'value' => 'Canada/Newfoundland',
              'label' => 'Canada/Newfoundland',
              'name' => 'time_zone',
            ),
            329 => 
            array (
              'config_id' => '154',
              'value' => 'Canada/Pacific',
              'label' => 'Canada/Pacific',
              'name' => 'time_zone',
            ),
            330 => 
            array (
              'config_id' => '154',
              'value' => 'Canada/Saskatchewan',
              'label' => 'Canada/Saskatchewan',
              'name' => 'time_zone',
            ),
            331 => 
            array (
              'config_id' => '154',
              'value' => 'Canada/Yukon',
              'label' => 'Canada/Yukon',
              'name' => 'time_zone',
            ),
            332 => 
            array (
              'config_id' => '154',
              'value' => 'Chile/Continental',
              'label' => 'Chile/Continental',
              'name' => 'time_zone',
            ),
            333 => 
            array (
              'config_id' => '154',
              'value' => 'Chile/EasterIsland',
              'label' => 'Chile/EasterIsland',
              'name' => 'time_zone',
            ),
            334 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Amsterdam',
              'label' => 'Europe/Amsterdam',
              'name' => 'time_zone',
            ),
            335 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Andorra',
              'label' => 'Europe/Andorra',
              'name' => 'time_zone',
            ),
            336 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Athens',
              'label' => 'Europe/Athens',
              'name' => 'time_zone',
            ),
            337 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Belfast',
              'label' => 'Europe/Belfast',
              'name' => 'time_zone',
            ),
            338 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Belgrade',
              'label' => 'Europe/Belgrade',
              'name' => 'time_zone',
            ),
            339 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Berlin',
              'label' => 'Europe/Berlin',
              'name' => 'time_zone',
            ),
            340 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Bratislava',
              'label' => 'Europe/Bratislava',
              'name' => 'time_zone',
            ),
            341 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Brussels',
              'label' => 'Europe/Brussels',
              'name' => 'time_zone',
            ),
            342 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Bucharest',
              'label' => 'Europe/Bucharest',
              'name' => 'time_zone',
            ),
            343 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Budapest',
              'label' => 'Europe/Budapest',
              'name' => 'time_zone',
            ),
            344 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Chisinau',
              'label' => 'Europe/Chisinau',
              'name' => 'time_zone',
            ),
            345 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Copenhagen',
              'label' => 'Europe/Copenhagen',
              'name' => 'time_zone',
            ),
            346 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Dublin',
              'label' => 'Europe/Dublin',
              'name' => 'time_zone',
            ),
            347 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Gibraltar',
              'label' => 'Europe/Gibraltar',
              'name' => 'time_zone',
            ),
            348 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Helsinki',
              'label' => 'Europe/Helsinki',
              'name' => 'time_zone',
            ),
            349 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Istanbul',
              'label' => 'Europe/Istanbul',
              'name' => 'time_zone',
            ),
            350 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Kaliningrad',
              'label' => 'Europe/Kaliningrad',
              'name' => 'time_zone',
            ),
            351 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Kiev',
              'label' => 'Europe/Kiev',
              'name' => 'time_zone',
            ),
            352 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Lisbon',
              'label' => 'Europe/Lisbon',
              'name' => 'time_zone',
            ),
            353 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Ljubljana',
              'label' => 'Europe/Ljubljana',
              'name' => 'time_zone',
            ),
            354 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/London',
              'label' => 'Europe/London',
              'name' => 'time_zone',
            ),
            355 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Luxembourg',
              'label' => 'Europe/Luxembourg',
              'name' => 'time_zone',
            ),
            356 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Madrid',
              'label' => 'Europe/Madrid',
              'name' => 'time_zone',
            ),
            357 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Malta',
              'label' => 'Europe/Malta',
              'name' => 'time_zone',
            ),
            358 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Mariehamn',
              'label' => 'Europe/Mariehamn',
              'name' => 'time_zone',
            ),
            359 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Minsk',
              'label' => 'Europe/Minsk',
              'name' => 'time_zone',
            ),
            360 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Monaco',
              'label' => 'Europe/Monaco',
              'name' => 'time_zone',
            ),
            361 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Moscow',
              'label' => 'Europe/Moscow',
              'name' => 'time_zone',
            ),
            362 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Nicosia',
              'label' => 'Europe/Nicosia',
              'name' => 'time_zone',
            ),
            363 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Oslo',
              'label' => 'Europe/Oslo',
              'name' => 'time_zone',
            ),
            364 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Paris',
              'label' => 'Europe/Paris',
              'name' => 'time_zone',
            ),
            365 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Prague',
              'label' => 'Europe/Prague',
              'name' => 'time_zone',
            ),
            366 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Riga',
              'label' => 'Europe/Riga',
              'name' => 'time_zone',
            ),
            367 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Rome',
              'label' => 'Europe/Rome',
              'name' => 'time_zone',
            ),
            368 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Samara',
              'label' => 'Europe/Samara',
              'name' => 'time_zone',
            ),
            369 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/San_Marino',
              'label' => 'Europe/San_Marino',
              'name' => 'time_zone',
            ),
            370 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Sarajevo',
              'label' => 'Europe/Sarajevo',
              'name' => 'time_zone',
            ),
            371 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Simferopol',
              'label' => 'Europe/Simferopol',
              'name' => 'time_zone',
            ),
            372 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Skopje',
              'label' => 'Europe/Skopje',
              'name' => 'time_zone',
            ),
            373 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Sofia',
              'label' => 'Europe/Sofia',
              'name' => 'time_zone',
            ),
            374 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Stockholm',
              'label' => 'Europe/Stockholm',
              'name' => 'time_zone',
            ),
            375 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Tallinn',
              'label' => 'Europe/Tallinn',
              'name' => 'time_zone',
            ),
            376 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Tirane',
              'label' => 'Europe/Tirane',
              'name' => 'time_zone',
            ),
            377 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Tiraspol',
              'label' => 'Europe/Tiraspol',
              'name' => 'time_zone',
            ),
            378 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Uzhgorod',
              'label' => 'Europe/Uzhgorod',
              'name' => 'time_zone',
            ),
            379 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Vaduz',
              'label' => 'Europe/Vaduz',
              'name' => 'time_zone',
            ),
            380 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Vatican',
              'label' => 'Europe/Vatican',
              'name' => 'time_zone',
            ),
            381 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Vienna',
              'label' => 'Europe/Vienna',
              'name' => 'time_zone',
            ),
            382 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Vilnius',
              'label' => 'Europe/Vilnius',
              'name' => 'time_zone',
            ),
            383 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Warsaw',
              'label' => 'Europe/Warsaw',
              'name' => 'time_zone',
            ),
            384 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Zagreb',
              'label' => 'Europe/Zagreb',
              'name' => 'time_zone',
            ),
            385 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Zaporozhye',
              'label' => 'Europe/Zaporozhye',
              'name' => 'time_zone',
            ),
            386 => 
            array (
              'config_id' => '154',
              'value' => 'Europe/Zurich',
              'label' => 'Europe/Zurich',
              'name' => 'time_zone',
            ),
            387 => 
            array (
              'config_id' => '154',
              'value' => 'Indian/Antananarivo',
              'label' => 'Indian/Antananarivo',
              'name' => 'time_zone',
            ),
            388 => 
            array (
              'config_id' => '154',
              'value' => 'Indian/Chagos',
              'label' => 'Indian/Chagos',
              'name' => 'time_zone',
            ),
            389 => 
            array (
              'config_id' => '154',
              'value' => 'Indian/Christmas',
              'label' => 'Indian/Christmas',
              'name' => 'time_zone',
            ),
            390 => 
            array (
              'config_id' => '154',
              'value' => 'Indian/Cocos',
              'label' => 'Indian/Cocos',
              'name' => 'time_zone',
            ),
            391 => 
            array (
              'config_id' => '154',
              'value' => 'Indian/Comoro',
              'label' => 'Indian/Comoro',
              'name' => 'time_zone',
            ),
            392 => 
            array (
              'config_id' => '154',
              'value' => 'Indian/Kerguelen',
              'label' => 'Indian/Kerguelen',
              'name' => 'time_zone',
            ),
            393 => 
            array (
              'config_id' => '154',
              'value' => 'Indian/Mahe',
              'label' => 'Indian/Mahe',
              'name' => 'time_zone',
            ),
            394 => 
            array (
              'config_id' => '154',
              'value' => 'Indian/Maldives',
              'label' => 'Indian/Maldives',
              'name' => 'time_zone',
            ),
            395 => 
            array (
              'config_id' => '154',
              'value' => 'Indian/Mauritius',
              'label' => 'Indian/Mauritius',
              'name' => 'time_zone',
            ),
            396 => 
            array (
              'config_id' => '154',
              'value' => 'Indian/Mayotte',
              'label' => 'Indian/Mayotte',
              'name' => 'time_zone',
            ),
            397 => 
            array (
              'config_id' => '154',
              'value' => 'Indian/Reunion',
              'label' => 'Indian/Reunion',
              'name' => 'time_zone',
            ),
            398 => 
            array (
              'config_id' => '154',
              'value' => 'Mexico/BajaNorte',
              'label' => 'Mexico/BajaNorte',
              'name' => 'time_zone',
            ),
            399 => 
            array (
              'config_id' => '154',
              'value' => 'Mexico/BajaSur',
              'label' => 'Mexico/BajaSur',
              'name' => 'time_zone',
            ),
            400 => 
            array (
              'config_id' => '154',
              'value' => 'Mexico/General',
              'label' => 'Mexico/General',
              'name' => 'time_zone',
            ),
            401 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Apia',
              'label' => 'Pacific/Apia',
              'name' => 'time_zone',
            ),
            402 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Auckland',
              'label' => 'Pacific/Auckland',
              'name' => 'time_zone',
            ),
            403 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Chatham',
              'label' => 'Pacific/Chatham',
              'name' => 'time_zone',
            ),
            404 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Easter',
              'label' => 'Pacific/Easter',
              'name' => 'time_zone',
            ),
            405 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Efate',
              'label' => 'Pacific/Efate',
              'name' => 'time_zone',
            ),
            406 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Enderbury',
              'label' => 'Pacific/Enderbury',
              'name' => 'time_zone',
            ),
            407 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Fakaofo',
              'label' => 'Pacific/Fakaofo',
              'name' => 'time_zone',
            ),
            408 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Fiji',
              'label' => 'Pacific/Fiji',
              'name' => 'time_zone',
            ),
            409 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Funafuti',
              'label' => 'Pacific/Funafuti',
              'name' => 'time_zone',
            ),
            410 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Galapagos',
              'label' => 'Pacific/Galapagos',
              'name' => 'time_zone',
            ),
            411 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Gambier',
              'label' => 'Pacific/Gambier',
              'name' => 'time_zone',
            ),
            412 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Guadalcanal',
              'label' => 'Pacific/Guadalcanal',
              'name' => 'time_zone',
            ),
            413 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Guam',
              'label' => 'Pacific/Guam',
              'name' => 'time_zone',
            ),
            414 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Honolulu',
              'label' => 'Pacific/Honolulu',
              'name' => 'time_zone',
            ),
            415 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Johnston',
              'label' => 'Pacific/Johnston',
              'name' => 'time_zone',
            ),
            416 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Kiritimati',
              'label' => 'Pacific/Kiritimati',
              'name' => 'time_zone',
            ),
            417 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Kosrae',
              'label' => 'Pacific/Kosrae',
              'name' => 'time_zone',
            ),
            418 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Kwajalein',
              'label' => 'Pacific/Kwajalein',
              'name' => 'time_zone',
            ),
            419 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Majuro',
              'label' => 'Pacific/Majuro',
              'name' => 'time_zone',
            ),
            420 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Marquesas',
              'label' => 'Pacific/Marquesas',
              'name' => 'time_zone',
            ),
            421 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Midway',
              'label' => 'Pacific/Midway',
              'name' => 'time_zone',
            ),
            422 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Nauru',
              'label' => 'Pacific/Nauru',
              'name' => 'time_zone',
            ),
            423 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Niue',
              'label' => 'Pacific/Niue',
              'name' => 'time_zone',
            ),
            424 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Norfolk',
              'label' => 'Pacific/Norfolk',
              'name' => 'time_zone',
            ),
            425 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Noumea',
              'label' => 'Pacific/Noumea',
              'name' => 'time_zone',
            ),
            426 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Pago_Pago',
              'label' => 'Pacific/Pago_Pago',
              'name' => 'time_zone',
            ),
            427 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Palau',
              'label' => 'Pacific/Palau',
              'name' => 'time_zone',
            ),
            428 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Pitcairn',
              'label' => 'Pacific/Pitcairn',
              'name' => 'time_zone',
            ),
            429 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Ponape',
              'label' => 'Pacific/Ponape',
              'name' => 'time_zone',
            ),
            430 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Port_Moresby',
              'label' => 'Pacific/Port_Moresby',
              'name' => 'time_zone',
            ),
            431 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Rarotonga',
              'label' => 'Pacific/Rarotonga',
              'name' => 'time_zone',
            ),
            432 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Saipan',
              'label' => 'Pacific/Saipan',
              'name' => 'time_zone',
            ),
            433 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Samoa',
              'label' => 'Pacific/Samoa',
              'name' => 'time_zone',
            ),
            434 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Tahiti',
              'label' => 'Pacific/Tahiti',
              'name' => 'time_zone',
            ),
            435 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Tarawa',
              'label' => 'Pacific/Tarawa',
              'name' => 'time_zone',
            ),
            436 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Tongatapu',
              'label' => 'Pacific/Tongatapu',
              'name' => 'time_zone',
            ),
            437 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Truk',
              'label' => 'Pacific/Truk',
              'name' => 'time_zone',
            ),
            438 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Wake',
              'label' => 'Pacific/Wake',
              'name' => 'time_zone',
            ),
            439 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Wallis',
              'label' => 'Pacific/Wallis',
              'name' => 'time_zone',
            ),
            440 => 
            array (
              'config_id' => '154',
              'value' => 'Pacific/Yap',
              'label' => 'Pacific/Yap',
              'name' => 'time_zone',
            ),
            441 => 
            array (
              'config_id' => '154',
              'value' => 'US/Alaska',
              'label' => 'US/Alaska',
              'name' => 'time_zone',
            ),
            442 => 
            array (
              'config_id' => '154',
              'value' => 'US/Aleutian',
              'label' => 'US/Aleutian',
              'name' => 'time_zone',
            ),
            443 => 
            array (
              'config_id' => '154',
              'value' => 'US/Arizona',
              'label' => 'US/Arizona',
              'name' => 'time_zone',
            ),
            444 => 
            array (
              'config_id' => '154',
              'value' => 'US/Central',
              'label' => 'US/Central',
              'name' => 'time_zone',
            ),
            445 => 
            array (
              'config_id' => '154',
              'value' => 'US/East-Indiana',
              'label' => 'US/East-Indiana',
              'name' => 'time_zone',
            ),
            446 => 
            array (
              'config_id' => '154',
              'value' => 'US/Eastern',
              'label' => 'US/Eastern',
              'name' => 'time_zone',
            ),
            447 => 
            array (
              'config_id' => '154',
              'value' => 'US/Hawaii',
              'label' => 'US/Hawaii',
              'name' => 'time_zone',
            ),
            448 => 
            array (
              'config_id' => '154',
              'value' => 'US/Indiana-Starke',
              'label' => 'US/Indiana-Starke',
              'name' => 'time_zone',
            ),
            449 => 
            array (
              'config_id' => '154',
              'value' => 'US/Michigan',
              'label' => 'US/Michigan',
              'name' => 'time_zone',
            ),
            450 => 
            array (
              'config_id' => '154',
              'value' => 'US/Mountain',
              'label' => 'US/Mountain',
              'name' => 'time_zone',
            ),
            451 => 
            array (
              'config_id' => '154',
              'value' => 'US/Pacific',
              'label' => 'US/Pacific',
              'name' => 'time_zone',
            ),
            452 => 
            array (
              'config_id' => '154',
              'value' => 'US/Pacific-New',
              'label' => 'US/Pacific-New',
              'name' => 'time_zone',
            ),
            453 => 
            array (
              'config_id' => '154',
              'value' => 'US/Samoa',
              'label' => 'US/Samoa',
              'name' => 'time_zone',
            ),
          ),
        ),
      )),
      'automode' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '38',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'automode',
           'label' => 'Auto Mode Settings',
           'parent_section_id' => '2',
           'config_section_id' => '38',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'bg_image_status' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '179',
             'config_section_id' => '38',
             'name' => 'bg_image_status',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Set status "active" automatically for new profile\'s background image',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'set_active_blog_post_on_submit' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '172',
             'config_section_id' => '38',
             'name' => 'set_active_blog_post_on_submit',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Set status "active" automatically for new submited blog posts',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'set_active_cls_on_creation' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '281',
             'config_section_id' => '38',
             'name' => 'set_active_cls_on_creation',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Set status "active" automatically for new created classifieds items',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'set_active_event_on_submit' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '171',
             'config_section_id' => '38',
             'name' => 'set_active_event_on_submit',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Set status "active" automatically for new submited events',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'set_active_group_on_creation' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '198',
             'config_section_id' => '38',
             'name' => 'set_active_group_on_creation',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Set status "active" automatically for new created groups',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'set_active_music_on_upload' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '217',
             'config_section_id' => '38',
             'name' => 'set_active_music_on_upload',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Set status "active" automatically for new uploaded profile music',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'set_active_photo_on_upload' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '67',
             'config_section_id' => '38',
             'name' => 'set_active_photo_on_upload',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Set status "active" automatically for new uploaded profile photos ',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'set_active_video_on_upload' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '62',
             'config_section_id' => '38',
             'name' => 'set_active_video_on_upload',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Set status "active" automatically for new uploaded profile video',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'set_profile_status_on_join' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '178',
             'config_section_id' => '38',
             'name' => 'set_profile_status_on_join',
             'value' => 'active',
             'presentation' => 'select',
             'description' => 'Default profile status after registration',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
          'set_profile_status_on_join' => 
          array (
            0 => 
            array (
              'config_id' => '178',
              'value' => 'active',
              'label' => 'Active',
              'name' => 'set_profile_status_on_join',
            ),
            1 => 
            array (
              'config_id' => '178',
              'value' => 'on_hold',
              'label' => 'On Hold',
              'name' => 'set_profile_status_on_join',
            ),
          ),
        ),
      )),
      'seo' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '48',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'seo',
           'label' => 'Seo Settings',
           'parent_section_id' => '2',
           'config_section_id' => '48',
        )),
         'sub_sections' => 
        array (
          'meta' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '49',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'meta',
               'label' => 'Additional meta tags',
               'parent_section_id' => '48',
               'config_section_id' => '49',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'add_meta' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '142',
                 'config_section_id' => '49',
                 'name' => 'add_meta',
                 'value' => '',
                 'presentation' => 'text',
                 'description' => 'Keywords',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
            ),
             'config_values' => 
            array (
            ),
          )),
          'google_analytics' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '50',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'google_analytics',
               'label' => 'Google Analytics',
               'parent_section_id' => '48',
               'config_section_id' => '50',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'code' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '143',
                 'config_section_id' => '50',
                 'name' => 'code',
                 'value' => '',
                 'presentation' => 'text',
                 'description' => NULL,
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'enabled' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '144',
                 'config_section_id' => '50',
                 'name' => 'enabled',
                 'value' => false,
                 'presentation' => 'checkbox',
                 'description' => 'Enabled',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
            ),
             'config_values' => 
            array (
            ),
          )),
        ),
         'configs' => 
        array (
        ),
         'config_values' => 
        array (
        ),
      )),
      'site_status' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '53',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'site_status',
           'label' => 'Site Status',
           'parent_section_id' => '2',
           'config_section_id' => '53',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'locked' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '153',
             'config_section_id' => '53',
             'name' => 'locked',
             'value' => 0,
             'presentation' => 'hidden',
             'description' => NULL,
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'suspended' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '152',
             'config_section_id' => '53',
             'name' => 'suspended',
             'value' => false,
             'presentation' => 'checkbox',
             'description' => 'Site Suspended',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
      'splash_screen' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '86',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'splash_screen',
           'label' => 'Splash Screen Config',
           'parent_section_id' => '2',
           'config_section_id' => '86',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'enable' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '296',
             'config_section_id' => '86',
             'name' => 'enable',
             'value' => false,
             'presentation' => 'checkbox',
             'description' => 'Enable splash screen',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'leave_url' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '294',
             'config_section_id' => '86',
             'name' => 'leave_url',
             'value' => 'http://google.com',
             'presentation' => 'varchar',
             'description' => '\'Leave\' URL ( Example: http://google.com )',
             'php_validation' => 'return preg_match("/^http(s)?:\\/\\/((\\d+\\.\\d+\\.\\d+\\.\\d+)|(([\\w-]+\\.)+([a-z,A-Z][\\w-]*)))(:[1-9][0-9]*)?(\\/?([\\w-.\\,\\/:%+@&*=]+[\\w- \\,.\\/?:%+@&=*|]*)?)?(#(.*))?$/", $value);',
             'js_validation' => '',
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
    ),
     'configs' => 
    array (
    ),
     'config_values' => 
    array (
    ),
  )),
  'languages' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '14',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'languages',
       'label' => 'Languages Settings',
       'parent_section_id' => '0',
       'config_section_id' => '14',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'auto_select' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '320',
         'config_section_id' => '14',
         'name' => 'auto_select',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Language autoselect',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'caching' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '155',
         'config_section_id' => '14',
         'name' => 'caching',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Cache Languages',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'default_lang_id' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '7',
         'config_section_id' => '14',
         'name' => 'default_lang_id',
         'value' => 61,
         'presentation' => 'hidden',
         'description' => 'Default Language',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'profile_fields' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '15',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'profile_fields',
       'label' => 'Profile Fields Settings',
       'parent_section_id' => '0',
       'config_section_id' => '15',
    )),
     'sub_sections' => 
    array (
      'advanced' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '17',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'advanced',
           'label' => 'Advanced options',
           'parent_section_id' => '15',
           'config_section_id' => '17',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'agerange_display_order' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '11',
             'config_section_id' => '17',
             'name' => 'agerange_display_order',
             'value' => 'asc',
             'presentation' => 'select',
             'description' => 'Order of second value display in "age_range" fields',
             'php_validation' => '',
             'js_validation' => '',
          )),
          'date_display_config' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '8',
             'config_section_id' => '17',
             'name' => 'date_display_config',
             'value' => 'date',
             'presentation' => 'select',
             'description' => 'Format of birthdate field display on view page',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'date_year_display_order' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '10',
             'config_section_id' => '17',
             'name' => 'date_year_display_order',
             'value' => 'desc',
             'presentation' => 'select',
             'description' => 'Order of year display in "date" fields',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'default_username_field_display' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '9',
             'config_section_id' => '17',
             'name' => 'default_username_field_display',
             'value' => 'username',
             'presentation' => 'select',
             'description' => 'Text field which display instead of username<br />Note: username search will be executed by this field',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
          'agerange_display_order' => 
          array (
            0 => 
            array (
              'config_id' => '11',
              'value' => 'asc',
              'label' => 'Asc',
              'name' => 'agerange_display_order',
            ),
            1 => 
            array (
              'config_id' => '11',
              'value' => 'desc',
              'label' => 'Desc',
              'name' => 'agerange_display_order',
            ),
          ),
          'date_display_config' => 
          array (
            0 => 
            array (
              'config_id' => '8',
              'value' => 'date',
              'label' => 'Date',
              'name' => 'date_display_config',
            ),
            1 => 
            array (
              'config_id' => '8',
              'value' => 'age',
              'label' => 'Age',
              'name' => 'date_display_config',
            ),
          ),
          'date_year_display_order' => 
          array (
            0 => 
            array (
              'config_id' => '10',
              'value' => 'asc',
              'label' => 'Asc',
              'name' => 'date_year_display_order',
            ),
            1 => 
            array (
              'config_id' => '10',
              'value' => 'desc',
              'label' => 'Desc',
              'name' => 'date_year_display_order',
            ),
          ),
          'default_username_field_display' => 
          array (
            0 => 
            array (
              'config_id' => '9',
              'value' => 'email',
              'label' => 'email',
              'name' => 'default_username_field_display',
            ),
            1 => 
            array (
              'config_id' => '9',
              'value' => 'username',
              'label' => 'username',
              'name' => 'default_username_field_display',
            ),
            2 => 
            array (
              'config_id' => '9',
              'value' => 'headline',
              'label' => 'headline',
              'name' => 'default_username_field_display',
            ),
            3 => 
            array (
              'config_id' => '9',
              'value' => 'real_name',
              'label' => 'real_name',
              'name' => 'default_username_field_display',
            ),
          ),
        ),
      )),
      'location' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '32',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'location',
           'label' => NULL,
           'parent_section_id' => '15',
           'config_section_id' => '32',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'order' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '43',
             'config_section_id' => '32',
             'name' => 'order',
             'value' => 'country_id,state_id,city_id,zip,custom_location',
             'presentation' => 'varchar',
             'description' => NULL,
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
    ),
     'configs' => 
    array (
    ),
     'config_values' => 
    array (
    ),
  )),
  'video' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '16',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'video',
       'label' => 'Video Settings',
       'parent_section_id' => '0',
       'config_section_id' => '16',
    )),
     'sub_sections' => 
    array (
      'flash_mode' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '36',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'flash_mode',
           'label' => 'Flash video mode configuration',
           'parent_section_id' => '16',
           'config_section_id' => '36',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'ffmpeg_path' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '54',
             'config_section_id' => '36',
             'name' => 'ffmpeg_path',
             'value' => '/usr/local/bin/ffmpeg',
             'presentation' => 'varchar',
             'description' => 'Path to ffmpeg',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'mencoder_path' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '56',
             'config_section_id' => '36',
             'name' => 'mencoder_path',
             'value' => '/usr/local/bin/mencoder',
             'presentation' => 'varchar',
             'description' => 'Path to Mencoder',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'mplayer_path' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '55',
             'config_section_id' => '36',
             'name' => 'mplayer_path',
             'value' => '/usr/local/bin/mplayer',
             'presentation' => 'varchar',
             'description' => 'Path to MPlayer',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
      'other_settings' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '37',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'other_settings',
           'label' => 'Other settings',
           'parent_section_id' => '16',
           'config_section_id' => '37',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'allow_embed_code' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '76',
             'config_section_id' => '37',
             'name' => 'allow_embed_code',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Allow members embed video codes',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'allow_upload_files' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '200',
             'config_section_id' => '37',
             'name' => 'allow_upload_files',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Allow members upload video files',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'display_media_list_limit' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '73',
             'config_section_id' => '37',
             'name' => 'display_media_list_limit',
             'value' => 15,
             'presentation' => 'integer',
             'description' => 'Number of videos per page in video lists',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'enable_categories' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '239',
             'config_section_id' => '37',
             'name' => 'enable_categories',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Enable video categories',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'show_share_details' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '165',
             'config_section_id' => '37',
             'name' => 'show_share_details',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Show video sharing details',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'upload_media_files_limit' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '58',
             'config_section_id' => '37',
             'name' => 'upload_media_files_limit',
             'value' => 10,
             'presentation' => 'integer',
             'description' => 'Maximum number of profile video',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'upload_media_file_extension' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '60',
             'config_section_id' => '37',
             'name' => 'upload_media_file_extension',
             'value' => 'avi,mpeg,wmv,flv,mov,mp4,mpg,mkv,vob',
             'presentation' => 'hidden',
             'description' => 'Video files allowed extensions',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'upload_media_file_mime_types' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '61',
             'config_section_id' => '37',
             'name' => 'upload_media_file_mime_types',
             'value' => 'video/mpeg,video/x-ms-wmv,video/avi,video/x-msvideo',
             'presentation' => 'hidden',
             'description' => 'Video files allowed mime types',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'upload_media_file_size_limit' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '59',
             'config_section_id' => '37',
             'name' => 'upload_media_file_size_limit',
             'value' => 5,
             'presentation' => 'float',
             'description' => 'Multimedia file size limit (in Mb)',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
      'watermark' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '39',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'watermark',
           'label' => 'Configure Video Watermark',
           'parent_section_id' => '16',
           'config_section_id' => '39',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'enable_video_watermark' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '65',
             'config_section_id' => '39',
             'name' => 'enable_video_watermark',
             'value' => 0,
             'presentation' => 'checkbox',
             'description' => 'Enable profile video watermark',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'watermark_img' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '66',
             'config_section_id' => '39',
             'name' => 'watermark_img',
             'value' => 406337334,
             'presentation' => 'integer',
             'description' => NULL,
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
    ),
     'configs' => 
    array (
      'media_mode' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '12',
         'config_section_id' => '16',
         'name' => 'media_mode',
         'value' => 'windows_media',
         'presentation' => 'select',
         'description' => 'Site media mode',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'small_video_height' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '80',
         'config_section_id' => '16',
         'name' => 'small_video_height',
         'value' => '267',
         'presentation' => 'hidden',
         'description' => 'Video album video height',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'small_video_width' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '79',
         'config_section_id' => '16',
         'name' => 'small_video_width',
         'value' => '322',
         'presentation' => 'hidden',
         'description' => 'Video album video width',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'video_height' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '64',
         'config_section_id' => '16',
         'name' => 'video_height',
         'value' => '380',
         'presentation' => 'hidden',
         'description' => 'Video height',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'video_thumb_height' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '78',
         'config_section_id' => '16',
         'name' => 'video_thumb_height',
         'value' => '100',
         'presentation' => 'hidden',
         'description' => 'Video thumbnail height',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'video_thumb_width' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '77',
         'config_section_id' => '16',
         'name' => 'video_thumb_width',
         'value' => '100',
         'presentation' => 'hidden',
         'description' => 'Video thumbnail width',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'video_width' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '63',
         'config_section_id' => '16',
         'name' => 'video_width',
         'value' => '470',
         'presentation' => 'hidden',
         'description' => 'Video width',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
      'media_mode' => 
      array (
        0 => 
        array (
          'config_id' => '12',
          'value' => 'flash_video',
          'label' => 'Flash player',
          'name' => 'media_mode',
        ),
        1 => 
        array (
          'config_id' => '12',
          'value' => 'windows_media',
          'label' => 'Windows player',
          'name' => 'media_mode',
        ),
      ),
    ),
  )),
  'reports' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '18',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'reports',
       'label' => 'Member Reports',
       'parent_section_id' => '0',
       'config_section_id' => '18',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'enable_report' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '13',
         'config_section_id' => '18',
         'name' => 'enable_report',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Allow members making reports',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'membership' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '19',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'membership',
       'label' => 'Membership settings',
       'parent_section_id' => '0',
       'config_section_id' => '19',
    )),
     'sub_sections' => 
    array (
      'plan' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '20',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'plan',
           'label' => 'Membership type\'s plan settings',
           'parent_section_id' => '19',
           'config_section_id' => '20',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'plan_recurring_to_single' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '15',
             'config_section_id' => '20',
             'name' => 'plan_recurring_to_single',
             'value' => false,
             'presentation' => 'checkbox',
             'description' => 'Enable transfer recurring payment plans to single ones',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
      'expiration' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '70',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'expiration',
           'label' => 'Membership expiration',
           'parent_section_id' => '19',
           'config_section_id' => '70',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'notify_before' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '225',
             'config_section_id' => '70',
             'name' => 'notify_before',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Notify users before membership expiration',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'notify_before_days' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '226',
             'config_section_id' => '70',
             'name' => 'notify_before_days',
             'value' => 9,
             'presentation' => 'integer',
             'description' => 'Notify users before membership expiration, days',
             'php_validation' => 'return $value < 10;',
             'js_validation' => 'return value < 10;',
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
    ),
     'configs' => 
    array (
      'default_membership_type_id' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '14',
         'config_section_id' => '19',
         'name' => 'default_membership_type_id',
         'value' => 18,
         'presentation' => 'integer',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'admin_system' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '24',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'admin_system',
       'label' => 'Admin hidden preferences',
       'parent_section_id' => '0',
       'config_section_id' => '24',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'profile_list_columns' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '23',
         'config_section_id' => '24',
         'name' => 'profile_list_columns',
         'value' => 
        SK_ConfigDtoObject::__set_state(array(
           'email_verified' => 
          SK_ConfigDtoObject::__set_state(array(
             'checked' => 'yes',
          )),
           'join_stamp' => 
          SK_ConfigDtoObject::__set_state(array(
             'checked' => 'yes',
          )),
           'height' => 
          SK_ConfigDtoObject::__set_state(array(
             'checked' => 'yes',
          )),
           'body_type' => 
          SK_ConfigDtoObject::__set_state(array(
             'checked' => 'yes',
          )),
           'drink' => 
          SK_ConfigDtoObject::__set_state(array(
             'checked' => 'yes',
          )),
           'i_am_at_least_18_years_old' => 
          SK_ConfigDtoObject::__set_state(array(
             'checked' => 'yes',
          )),
           'i_agree_with_tos' => 
          SK_ConfigDtoObject::__set_state(array(
             'checked' => 'yes',
          )),
        )),
         'presentation' => 'hidden',
         'description' => 'Array of fields to display in admin profile list ',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'ads' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '25',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'ads',
       'label' => 'Advertisement',
       'parent_section_id' => '0',
       'config_section_id' => '25',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'profile_list_ads_num_display' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '24',
         'config_section_id' => '25',
         'name' => 'profile_list_ads_num_display',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'scheduler' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '26',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'scheduler',
       'label' => 'Activity Scheduler Settings',
       'parent_section_id' => '0',
       'config_section_id' => '26',
    )),
     'sub_sections' => 
    array (
      'match_list' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '27',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'match_list',
           'label' => 'Match list Settings',
           'parent_section_id' => '26',
           'config_section_id' => '27',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'match_period_measure' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '28',
             'config_section_id' => '27',
             'name' => 'match_period_measure',
             'value' => '0',
             'presentation' => 'select',
             'description' => NULL,
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'new_matches' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '26',
             'config_section_id' => '27',
             'name' => 'new_matches',
             'value' => false,
             'presentation' => 'checkbox',
             'description' => 'Send only new matches',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'new_match_period' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '27',
             'config_section_id' => '27',
             'name' => 'new_match_period',
             'value' => 0,
             'presentation' => 'integer',
             'description' => 'Period',
             'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
          )),
          'scheduler_mlist_is_random' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '108',
             'config_section_id' => '27',
             'name' => 'scheduler_mlist_is_random',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Random profiles',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'scheduler_mlist_profile_count' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '107',
             'config_section_id' => '27',
             'name' => 'scheduler_mlist_profile_count',
             'value' => 10,
             'presentation' => 'integer',
             'description' => 'Max number of profiles in match list',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
          'match_period_measure' => 
          array (
            0 => 
            array (
              'config_id' => '28',
              'value' => '3',
              'label' => 'Months',
              'name' => 'match_period_measure',
            ),
            1 => 
            array (
              'config_id' => '28',
              'value' => '2',
              'label' => 'Weeks',
              'name' => 'match_period_measure',
            ),
            2 => 
            array (
              'config_id' => '28',
              'value' => '1',
              'label' => 'Days',
              'name' => 'match_period_measure',
            ),
          ),
        ),
      )),
    ),
     'configs' => 
    array (
      'mail_scheduler_time' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '106',
         'config_section_id' => '26',
         'name' => 'mail_scheduler_time',
         'value' => '30-13-month-10-*',
         'presentation' => 'varchar',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'next_run_time' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '271',
         'config_section_id' => '26',
         'name' => 'next_run_time',
         'value' => 1368210630,
         'presentation' => 'integer',
         'description' => 'Activity scheduler next run timestamp',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'profile_registration' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '31',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'profile_registration',
       'label' => NULL,
       'parent_section_id' => '0',
       'config_section_id' => '31',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'allow_max_invitation' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '38',
         'config_section_id' => '31',
         'name' => 'allow_max_invitation',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Member invitations are limited',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'invite_access' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '39',
         'config_section_id' => '31',
         'name' => 'invite_access',
         'value' => 'all',
         'presentation' => 'select',
         'description' => 'Who can invite new members?',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'invite_reset_period' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '197',
         'config_section_id' => '31',
         'name' => 'invite_reset_period',
         'value' => '30',
         'presentation' => 'select',
         'description' => '<div style="text-align: right">per</div>',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'invite_time_interval' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '40',
         'config_section_id' => '31',
         'name' => 'invite_time_interval',
         'value' => 5,
         'presentation' => 'integer',
         'description' => 'Number of days until invitation expires',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
      'limit_invitation' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '196',
         'config_section_id' => '31',
         'name' => 'limit_invitation',
         'value' => 50,
         'presentation' => 'integer',
         'description' => 'Number of member invitations',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
      'type' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '42',
         'config_section_id' => '31',
         'name' => 'type',
         'value' => 'free',
         'presentation' => 'select',
         'description' => 'How can members register?',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
      'invite_access' => 
      array (
        0 => 
        array (
          'config_id' => '39',
          'value' => 'all',
          'label' => 'Members/Admin',
          'name' => 'invite_access',
        ),
        1 => 
        array (
          'config_id' => '39',
          'value' => 'admin',
          'label' => 'Admin only',
          'name' => 'invite_access',
        ),
      ),
      'invite_reset_period' => 
      array (
        0 => 
        array (
          'config_id' => '197',
          'value' => '1',
          'label' => 'Day',
          'name' => 'invite_reset_period',
        ),
        1 => 
        array (
          'config_id' => '197',
          'value' => '7',
          'label' => 'Week',
          'name' => 'invite_reset_period',
        ),
        2 => 
        array (
          'config_id' => '197',
          'value' => '30',
          'label' => 'Month',
          'name' => 'invite_reset_period',
        ),
        3 => 
        array (
          'config_id' => '197',
          'value' => '365',
          'label' => 'Year',
          'name' => 'invite_reset_period',
        ),
      ),
      'type' => 
      array (
        0 => 
        array (
          'config_id' => '42',
          'value' => 'free',
          'label' => 'Free',
          'name' => 'type',
        ),
        1 => 
        array (
          'config_id' => '42',
          'value' => 'invite',
          'label' => 'By invitation only',
          'name' => 'type',
        ),
      ),
    ),
  )),
  'blogs' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '34',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'blogs',
       'label' => 'Blogs Settings',
       'parent_section_id' => '0',
       'config_section_id' => '34',
    )),
     'sub_sections' => 
    array (
      'listing' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '45',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'listing',
           'label' => 'Listing Configs',
           'parent_section_id' => '34',
           'config_section_id' => '45',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'blog_view_posts_count' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '114',
             'config_section_id' => '45',
             'name' => 'blog_view_posts_count',
             'value' => 10,
             'presentation' => 'integer',
             'description' => 'Posts count on profile blog page',
             'php_validation' => 'return ( (bool)intval($value) && $value<=100);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)) && value <= 100);',
          )),
          'index_page_blog_post_count' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '111',
             'config_section_id' => '45',
             'name' => 'index_page_blog_post_count',
             'value' => 5,
             'presentation' => 'integer',
             'description' => 'Posts count on index page',
             'php_validation' => 'return ( (bool)intval($value) && $value<=100);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)) && value <= 100);',
          )),
          'news_count' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '115',
             'config_section_id' => '45',
             'name' => 'news_count',
             'value' => 5,
             'presentation' => 'integer',
             'description' => 'News posts count',
             'php_validation' => 'return ( (bool)intval($value) && $value<=100);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)) && value <= 100);',
          )),
          'profile_page_blog_post_count' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '112',
             'config_section_id' => '45',
             'name' => 'profile_page_blog_post_count',
             'value' => 5,
             'presentation' => 'integer',
             'description' => 'Posts count on profile view page',
             'php_validation' => 'return ( (bool)intval($value) && $value<=100);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)) && value <= 100);',
          )),
          'short_posts_on_page_count' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '113',
             'config_section_id' => '45',
             'name' => 'short_posts_on_page_count',
             'value' => 15,
             'presentation' => 'integer',
             'description' => 'Short posts count on page ',
             'php_validation' => 'return ( (bool)intval($value) && $value<=100);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)) && value <= 100);',
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
      'image' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '59',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'image',
           'label' => 'Blogs Image Configs',
           'parent_section_id' => '34',
           'config_section_id' => '59',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'blog_post_image_max_height' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '176',
             'config_section_id' => '59',
             'name' => 'blog_post_image_max_height',
             'value' => 500,
             'presentation' => 'integer',
             'description' => 'Blogs image max height(px)',
             'php_validation' => '',
             'js_validation' => '',
          )),
          'blog_post_image_max_size' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '177',
             'config_section_id' => '59',
             'name' => 'blog_post_image_max_size',
             'value' => 500,
             'presentation' => 'integer',
             'description' => 'Blogs image max size(KB)',
             'php_validation' => '',
             'js_validation' => '',
          )),
          'blog_post_image_max_width' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '175',
             'config_section_id' => '59',
             'name' => 'blog_post_image_max_width',
             'value' => 500,
             'presentation' => 'integer',
             'description' => 'Blogs image max width(px)',
             'php_validation' => '',
             'js_validation' => '',
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
    ),
     'configs' => 
    array (
    ),
     'config_values' => 
    array (
    ),
  )),
  'chat' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '35',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'chat',
       'label' => 'Chat',
       'parent_section_id' => '0',
       'config_section_id' => '35',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'history_recent_msgs_num' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '52',
         'config_section_id' => '35',
         'name' => 'history_recent_msgs_num',
         'value' => 10,
         'presentation' => 'integer',
         'description' => 'Number of recent Chat messages to display',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'history_time' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '53',
         'config_section_id' => '35',
         'name' => 'history_time',
         'value' => 
        SK_ConfigDtoObject::__set_state(array(
           'digit' => 10,
           'unit' => 'seconds',
        )),
         'presentation' => 'hidden',
         'description' => 'Keep chat message history for',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'history_type' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '51',
         'config_section_id' => '35',
         'name' => 'history_type',
         'value' => 'recent_msg_num',
         'presentation' => 'hidden',
         'description' => 'Chat history type',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'photo' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '40',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'photo',
       'label' => 'Photo Configs',
       'parent_section_id' => '0',
       'config_section_id' => '40',
    )),
     'sub_sections' => 
    array (
      'general' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '41',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'general',
           'label' => 'Profile Photo Configuration',
           'parent_section_id' => '40',
           'config_section_id' => '41',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'allow_rotate' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '223',
             'config_section_id' => '41',
             'name' => 'allow_rotate',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Allow members to rotate photo',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'crop_thumb' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '185',
             'config_section_id' => '41',
             'name' => 'crop_thumb',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Crop Thumbnails',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'display_pp_and_fo_on_index' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '182',
             'config_section_id' => '41',
             'name' => 'display_pp_and_fo_on_index',
             'value' => false,
             'presentation' => 'checkbox',
             'description' => 'Display <b>friends only</b> and <b>password protected</b> photos on index page',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'max_albums' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '204',
             'config_section_id' => '41',
             'name' => 'max_albums',
             'value' => 10,
             'presentation' => 'integer',
             'description' => 'Maximum number of Photo Albums',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'max_count' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '70',
             'config_section_id' => '41',
             'name' => 'max_count',
             'value' => 12,
             'presentation' => 'integer',
             'description' => 'Maximum number of profile photos',
             'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
          )),
          'max_filesize' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '91',
             'config_section_id' => '41',
             'name' => 'max_filesize',
             'value' => 4,
             'presentation' => 'float',
             'description' => 'Member photo file size limit (in Mb)',
             'php_validation' => 'return (bool)floatval($value) ;',
             'js_validation' => '',
          )),
          'max_height' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '93',
             'config_section_id' => '41',
             'name' => 'max_height',
             'value' => 1600,
             'presentation' => 'integer',
             'description' => 'Member photo max height (in px)',
             'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
          )),
          'max_photos_in_album' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '203',
             'config_section_id' => '41',
             'name' => 'max_photos_in_album',
             'value' => 10,
             'presentation' => 'integer',
             'description' => 'Maximum number of photos in photo album',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'max_width' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '92',
             'config_section_id' => '41',
             'name' => 'max_width',
             'value' => 2000,
             'presentation' => 'integer',
             'description' => 'Member photo max width (in px)',
             'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
          )),
          'min_rates_count' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '109',
             'config_section_id' => '41',
             'name' => 'min_rates_count',
             'value' => 1,
             'presentation' => 'integer',
             'description' => 'Min rates count in Top Rated list',
             'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)) && value <= 250);',
          )),
          'per_page' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '105',
             'config_section_id' => '41',
             'name' => 'per_page',
             'value' => 15,
             'presentation' => 'integer',
             'description' => 'Number of photo per page in photo lists',
             'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)) && value <= 250);',
          )),
          'preview_height' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '69',
             'config_section_id' => '41',
             'name' => 'preview_height',
             'value' => 250,
             'presentation' => 'integer',
             'description' => 'Preview photo height',
             'php_validation' => 'return ( (bool)intval($value) && $value<= 250);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)) && value <= 250);',
          )),
          'preview_width' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '68',
             'config_section_id' => '41',
             'name' => 'preview_width',
             'value' => 250,
             'presentation' => 'integer',
             'description' => 'Preview photo width',
             'php_validation' => 'return ( (bool)intval($value) && $value<= 250);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)) && value <= 250);',
          )),
          'thumb_fill_color' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '186',
             'config_section_id' => '41',
             'name' => 'thumb_fill_color',
             'value' => '009900',
             'presentation' => 'color',
             'description' => 'Thumbnail background color',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'thumb_height' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '95',
             'config_section_id' => '41',
             'name' => 'thumb_height',
             'value' => '100',
             'presentation' => 'hidden',
             'description' => 'Thumbnail photo height',
             'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
          )),
          'thumb_width' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '94',
             'config_section_id' => '41',
             'name' => 'thumb_width',
             'value' => '100',
             'presentation' => 'hidden',
             'description' => 'Thumbnail photo width',
             'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
          )),
          'top_list_gender_separate' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '184',
             'config_section_id' => '41',
             'name' => 'top_list_gender_separate',
             'value' => true,
             'presentation' => 'checkbox',
             'description' => 'Divide photos on top rated list by  owner gender ',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'view_height' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '90',
             'config_section_id' => '41',
             'name' => 'view_height',
             'value' => 530,
             'presentation' => 'integer',
             'description' => 'Photo height on view page',
             'php_validation' => 'return ( (bool)intval($value) && $value<=530);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)) && value <= 530);',
          )),
          'view_width' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '89',
             'config_section_id' => '41',
             'name' => 'view_width',
             'value' => 530,
             'presentation' => 'integer',
             'description' => 'Photo width on view page',
             'php_validation' => 'return ( (bool)intval($value) && $value<=530);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)) && value <= 530);',
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
      'watermark' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '42',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'watermark',
           'label' => 'Watemark Settings',
           'parent_section_id' => '40',
           'config_section_id' => '42',
        )),
         'sub_sections' => 
        array (
          'additional' => 
          SK_Inner_Config_Section::__set_state(array(
             'section_id' => '43',
             'section_info' => 
            SK_ConfigDtoObject::__set_state(array(
               'section' => 'additional',
               'label' => 'Additional settings',
               'parent_section_id' => '42',
               'config_section_id' => '43',
            )),
             'sub_sections' => 
            array (
            ),
             'configs' => 
            array (
              'padding' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '88',
                 'config_section_id' => '43',
                 'name' => 'padding',
                 'value' => 4,
                 'presentation' => 'integer',
                 'description' => 'Watermark padding',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
              'pos' => 
              SK_ConfigDtoObject::__set_state(array(
                 'config_id' => '87',
                 'config_section_id' => '43',
                 'name' => 'pos',
                 'value' => '2',
                 'presentation' => 'select',
                 'description' => 'Select watermark position',
                 'php_validation' => NULL,
                 'js_validation' => NULL,
              )),
            ),
             'config_values' => 
            array (
              'pos' => 
              array (
                0 => 
                array (
                  'config_id' => '87',
                  'value' => '1',
                  'label' => 'bottom right',
                  'name' => 'pos',
                ),
                1 => 
                array (
                  'config_id' => '87',
                  'value' => '2',
                  'label' => 'bottom left',
                  'name' => 'pos',
                ),
                2 => 
                array (
                  'config_id' => '87',
                  'value' => '3',
                  'label' => 'top left',
                  'name' => 'pos',
                ),
                3 => 
                array (
                  'config_id' => '87',
                  'value' => '4',
                  'label' => 'top right',
                  'name' => 'pos',
                ),
              ),
            ),
          )),
        ),
         'configs' => 
        array (
          'bg_color' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '82',
             'config_section_id' => '42',
             'name' => 'bg_color',
             'value' => '#FF6600',
             'presentation' => 'hidden',
             'description' => NULL,
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'img' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '83',
             'config_section_id' => '42',
             'name' => 'img',
             'value' => '751',
             'presentation' => 'hidden',
             'description' => NULL,
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'txt' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '84',
             'config_section_id' => '42',
             'name' => 'txt',
             'value' => 'SkaDate',
             'presentation' => 'varchar',
             'description' => NULL,
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'txt_color' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '85',
             'config_section_id' => '42',
             'name' => 'txt_color',
             'value' => '#000000',
             'presentation' => 'hidden',
             'description' => NULL,
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'txt_size' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '86',
             'config_section_id' => '42',
             'name' => 'txt_size',
             'value' => '5',
             'presentation' => 'select',
             'description' => NULL,
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'watermark' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '81',
             'config_section_id' => '42',
             'name' => 'watermark',
             'value' => '0',
             'presentation' => 'select',
             'description' => NULL,
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
          'txt_size' => 
          array (
            0 => 
            array (
              'config_id' => '86',
              'value' => '1',
              'label' => '1',
              'name' => 'txt_size',
            ),
            1 => 
            array (
              'config_id' => '86',
              'value' => '2',
              'label' => '2',
              'name' => 'txt_size',
            ),
            2 => 
            array (
              'config_id' => '86',
              'value' => '3',
              'label' => '3',
              'name' => 'txt_size',
            ),
            3 => 
            array (
              'config_id' => '86',
              'value' => '4',
              'label' => '4',
              'name' => 'txt_size',
            ),
            4 => 
            array (
              'config_id' => '86',
              'value' => '5',
              'label' => '5',
              'name' => 'txt_size',
            ),
          ),
          'watermark' => 
          array (
            0 => 
            array (
              'config_id' => '81',
              'value' => '1',
              'label' => 'Text',
              'name' => 'watermark',
            ),
            1 => 
            array (
              'config_id' => '81',
              'value' => '0',
              'label' => 'Disable',
              'name' => 'watermark',
            ),
            2 => 
            array (
              'config_id' => '81',
              'value' => '2',
              'label' => 'Image',
              'name' => 'watermark',
            ),
          ),
        ),
      )),
    ),
     'configs' => 
    array (
    ),
     'config_values' => 
    array (
    ),
  )),
  'forum' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '44',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'forum',
       'label' => 'Forum settings',
       'parent_section_id' => '0',
       'config_section_id' => '44',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'ban_period' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '98',
         'config_section_id' => '44',
         'name' => 'ban_period',
         'value' => '10800',
         'presentation' => 'select',
         'description' => 'Ban period',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
      'last_topic_count' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '122',
         'config_section_id' => '44',
         'name' => 'last_topic_count',
         'value' => 4,
         'presentation' => 'integer',
         'description' => 'Number of topics in "Latest in Forum Topics"',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
      'post_count_on_page' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '102',
         'config_section_id' => '44',
         'name' => 'post_count_on_page',
         'value' => 15,
         'presentation' => 'integer',
         'description' => 'Number of posts per page',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
      'profile_post_count' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '123',
         'config_section_id' => '44',
         'name' => 'profile_post_count',
         'value' => 4,
         'presentation' => 'integer',
         'description' => 'Number of posts in "Forum posts by profile"',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
      'replace_topic_expr_time' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '121',
         'config_section_id' => '44',
         'name' => 'replace_topic_expr_time',
         'value' => 48,
         'presentation' => 'integer',
         'description' => 'Replace topic expiration time(in hours)',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
      'show_page_count' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '103',
         'config_section_id' => '44',
         'name' => 'show_page_count',
         'value' => 12,
         'presentation' => 'integer',
         'description' => 'Pages range in topic list, post list navigation ',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
      'topic_count_on_page' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '104',
         'config_section_id' => '44',
         'name' => 'topic_count_on_page',
         'value' => 11,
         'presentation' => 'integer',
         'description' => 'Number of topics per page',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
      'topic_text_truncate' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '118',
         'config_section_id' => '44',
         'name' => 'topic_text_truncate',
         'value' => 122,
         'presentation' => 'integer',
         'description' => 'Topic text truncate width (in last topic list)',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
      'topic_title_truncate' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '117',
         'config_section_id' => '44',
         'name' => 'topic_title_truncate',
         'value' => 40,
         'presentation' => 'integer',
         'description' => 'Topic title truncate width (in last topic list)',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
    ),
     'config_values' => 
    array (
      'ban_period' => 
      array (
        0 => 
        array (
          'config_id' => '98',
          'value' => '3600',
          'label' => '1 hour',
          'name' => 'ban_period',
        ),
        1 => 
        array (
          'config_id' => '98',
          'value' => '10800',
          'label' => '3 hours',
          'name' => 'ban_period',
        ),
        2 => 
        array (
          'config_id' => '98',
          'value' => '21600',
          'label' => '6 hours',
          'name' => 'ban_period',
        ),
        3 => 
        array (
          'config_id' => '98',
          'value' => '43200',
          'label' => '12 hours',
          'name' => 'ban_period',
        ),
        4 => 
        array (
          'config_id' => '98',
          'value' => '86400',
          'label' => '24 hours',
          'name' => 'ban_period',
        ),
        5 => 
        array (
          'config_id' => '98',
          'value' => '2592000',
          'label' => '1 month',
          'name' => 'ban_period',
        ),
        6 => 
        array (
          'config_id' => '98',
          'value' => '2592000000',
          'label' => 'forever',
          'name' => 'ban_period',
        ),
      ),
    ),
  )),
  'badwords' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '46',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'badwords',
       'label' => 'Badwords For',
       'parent_section_id' => '0',
       'config_section_id' => '46',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'blog' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '125',
         'config_section_id' => '46',
         'name' => 'blog',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Blog',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'chat' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '265',
         'config_section_id' => '46',
         'name' => 'chat',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Chat',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'classifieds' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '289',
         'config_section_id' => '46',
         'name' => 'classifieds',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Classifieds',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'comment' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '124',
         'config_section_id' => '46',
         'name' => 'comment',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Comment',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'event' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '129',
         'config_section_id' => '46',
         'name' => 'event',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Event',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'forum' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '126',
         'config_section_id' => '46',
         'name' => 'forum',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Forum',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'group' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '194',
         'config_section_id' => '46',
         'name' => 'group',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Groups',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'mailbox' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '290',
         'config_section_id' => '46',
         'name' => 'mailbox',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Mailbox',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'music' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '316',
         'config_section_id' => '46',
         'name' => 'music',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Music',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'photo' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '127',
         'config_section_id' => '46',
         'name' => 'photo',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Photo',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'profile' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '288',
         'config_section_id' => '46',
         'name' => 'profile',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Profile',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'shoutbox' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '250',
         'config_section_id' => '46',
         'name' => 'shoutbox',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Shoutbox',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'tag' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '314',
         'config_section_id' => '46',
         'name' => 'tag',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Tag',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'video' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '128',
         'config_section_id' => '46',
         'name' => 'video',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Video',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'chuppo' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '47',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'chuppo',
       'label' => 'Chuppo',
       'parent_section_id' => '0',
       'config_section_id' => '47',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'chat_skin' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '130',
         'config_section_id' => '47',
         'name' => 'chat_skin',
         'value' => 'chuppo',
         'presentation' => 'varchar',
         'description' => 'Chat skin',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'chat_swf' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '131',
         'config_section_id' => '47',
         'name' => 'chat_swf',
         'value' => 'chuppochat',
         'presentation' => 'varchar',
         'description' => 'Chat swf',
         'php_validation' => '',
         'js_validation' => '',
      )),
      'enable_chuppo_chat' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '139',
         'config_section_id' => '47',
         'name' => 'enable_chuppo_chat',
         'value' => 0,
         'presentation' => 'checkbox',
         'description' => 'Enable ChuppoChat on site',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'enable_chuppo_im' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '140',
         'config_section_id' => '47',
         'name' => 'enable_chuppo_im',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Enable ChuppoIM and Useron on site',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'enable_chuppo_recorder' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '141',
         'config_section_id' => '47',
         'name' => 'enable_chuppo_recorder',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Enable ChuppoRecorder on site',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'im_skin' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '132',
         'config_section_id' => '47',
         'name' => 'im_skin',
         'value' => 'chuppo',
         'presentation' => 'varchar',
         'description' => 'Im skin',
         'php_validation' => '',
         'js_validation' => '',
      )),
      'im_swf' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '133',
         'config_section_id' => '47',
         'name' => 'im_swf',
         'value' => 'im',
         'presentation' => 'varchar',
         'description' => 'Im swf',
         'php_validation' => '',
         'js_validation' => '',
      )),
      'player_swf' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '134',
         'config_section_id' => '47',
         'name' => 'player_swf',
         'value' => 'player_274_silver',
         'presentation' => 'varchar',
         'description' => 'Player swf',
         'php_validation' => '',
         'js_validation' => '',
      )),
      'recorder_swf' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '135',
         'config_section_id' => '47',
         'name' => 'recorder_swf',
         'value' => 'recorder_silver',
         'presentation' => 'varchar',
         'description' => 'Recorder swf',
         'php_validation' => '',
         'js_validation' => '',
      )),
      'useron_skin' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '136',
         'config_section_id' => '47',
         'name' => 'useron_skin',
         'value' => 'chuppo',
         'presentation' => 'varchar',
         'description' => 'Useron skin',
         'php_validation' => '',
         'js_validation' => '',
      )),
      'useron_swf' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '137',
         'config_section_id' => '47',
         'name' => 'useron_swf',
         'value' => 'useronim',
         'presentation' => 'varchar',
         'description' => 'Useron swf',
         'php_validation' => '',
         'js_validation' => '',
      )),
      'vhost' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '138',
         'config_section_id' => '47',
         'name' => 'vhost',
         'value' => 'rtmp://skadatedemo.chuppomedia.com/',
         'presentation' => 'varchar',
         'description' => 'Virtual host for chuppo service',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'services' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '51',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'services',
       'label' => 'Services',
       'parent_section_id' => '0',
       'config_section_id' => '51',
    )),
     'sub_sections' => 
    array (
      'autocrop' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '52',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'autocrop',
           'label' => 'Autocrop settings',
           'parent_section_id' => '51',
           'config_section_id' => '52',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'enabled' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '146',
             'config_section_id' => '52',
             'name' => 'enabled',
             'value' => 0,
             'presentation' => 'checkbox',
             'description' => NULL,
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'password' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '148',
             'config_section_id' => '52',
             'name' => 'password',
             'value' => NULL,
             'presentation' => 'checkbox',
             'description' => NULL,
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'username' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '147',
             'config_section_id' => '52',
             'name' => 'username',
             'value' => NULL,
             'presentation' => 'checkbox',
             'description' => NULL,
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
    ),
     'configs' => 
    array (
    ),
     'config_values' => 
    array (
    ),
  )),
  'classifieds' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '54',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'classifieds',
       'label' => 'Classifieds',
       'parent_section_id' => '0',
       'config_section_id' => '54',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'allow_payment' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '280',
         'config_section_id' => '54',
         'name' => 'allow_payment',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Allow to post payment details',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'currency' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '159',
         'config_section_id' => '54',
         'name' => 'currency',
         'value' => '$',
         'presentation' => 'select',
         'description' => 'Default currency',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'items_count_on_page' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '156',
         'config_section_id' => '54',
         'name' => 'items_count_on_page',
         'value' => 15,
         'presentation' => 'integer',
         'description' => 'Items count on Items List page',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
      'show_pages_count' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '157',
         'config_section_id' => '54',
         'name' => 'show_pages_count',
         'value' => 20,
         'presentation' => 'integer',
         'description' => 'Show pages count on Items List',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
      'view_fullsize' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '158',
         'config_section_id' => '54',
         'name' => 'view_fullsize',
         'value' => 750,
         'presentation' => 'integer',
         'description' => 'Photo size on view photo fullsize',
         'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
      )),
    ),
     'config_values' => 
    array (
      'currency' => 
      array (
        0 => 
        array (
          'config_id' => '159',
          'value' => '$',
          'label' => 'USD',
          'name' => 'currency',
        ),
      ),
    ),
  )),
  'cls_wanted' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '55',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'cls_wanted',
       'label' => 'Classifieds wanted',
       'parent_section_id' => '0',
       'config_section_id' => '55',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'allow_bids' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '161',
         'config_section_id' => '55',
         'name' => 'allow_bids',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Allow bids',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'allow_comments' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '160',
         'config_section_id' => '55',
         'name' => 'allow_comments',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Allow comments',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'cls_offer' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '56',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'cls_offer',
       'label' => 'Classifieds offer',
       'parent_section_id' => '0',
       'config_section_id' => '56',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'allow_bids' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '163',
         'config_section_id' => '56',
         'name' => 'allow_bids',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Allow bids',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'allow_comments' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '162',
         'config_section_id' => '56',
         'name' => 'allow_comments',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Allow comments',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'seo-sitemap' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '57',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'seo-sitemap',
       'label' => 'SEO Sitemap',
       'parent_section_id' => '0',
       'config_section_id' => '57',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'doPingGoogle' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '167',
         'config_section_id' => '57',
         'name' => 'doPingGoogle',
         'value' => 0,
         'presentation' => 'text',
         'description' => '',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'doPingYahoo' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '168',
         'config_section_id' => '57',
         'name' => 'doPingYahoo',
         'value' => 0,
         'presentation' => 'text',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'isUpdated' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '169',
         'config_section_id' => '57',
         'name' => 'isUpdated',
         'value' => 1,
         'presentation' => 'text',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'updateTimestamp' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '170',
         'config_section_id' => '57',
         'name' => 'updateTimestamp',
         'value' => 1363340559,
         'presentation' => 'text',
         'description' => '',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'yahoo_appId' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '173',
         'config_section_id' => '57',
         'name' => 'yahoo_appId',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'email' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '60',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'email',
       'label' => 'Email Settings',
       'parent_section_id' => '0',
       'config_section_id' => '60',
    )),
     'sub_sections' => 
    array (
      'smtp' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '61',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'smtp',
           'label' => 'Smtp Settings',
           'parent_section_id' => '60',
           'config_section_id' => '61',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'connection_prefix' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '193',
             'config_section_id' => '61',
             'name' => 'connection_prefix',
             'value' => 'ssl',
             'presentation' => 'select',
             'description' => 'Secure prefix',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'enable' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '187',
             'config_section_id' => '61',
             'name' => 'enable',
             'value' => false,
             'presentation' => 'checkbox',
             'description' => 'Enable',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'host' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '188',
             'config_section_id' => '61',
             'name' => 'host',
             'value' => '',
             'presentation' => 'varchar',
             'description' => 'Host',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'password' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '190',
             'config_section_id' => '61',
             'name' => 'password',
             'value' => '',
             'presentation' => 'varchar',
             'description' => 'Password',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'port' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '192',
             'config_section_id' => '61',
             'name' => 'port',
             'value' => '',
             'presentation' => 'varchar',
             'description' => 'Port',
             'php_validation' => '',
             'js_validation' => '',
          )),
          'user' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '189',
             'config_section_id' => '61',
             'name' => 'user',
             'value' => '',
             'presentation' => 'varchar',
             'description' => 'User',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
          'connection_prefix' => 
          array (
            0 => 
            array (
              'config_id' => '193',
              'value' => 'ssl',
              'label' => 'ssl',
              'name' => 'connection_prefix',
            ),
            1 => 
            array (
              'config_id' => '193',
              'value' => 'tls',
              'label' => 'tls',
              'name' => 'connection_prefix',
            ),
            2 => 
            array (
              'config_id' => '193',
              'value' => 'no',
              'label' => 'no',
              'name' => 'connection_prefix',
            ),
          ),
        ),
      )),
    ),
     'configs' => 
    array (
    ),
     'config_values' => 
    array (
    ),
  )),
  'flash_123_chat' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '62',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'flash_123_chat',
       'label' => '123 Chat',
       'parent_section_id' => '0',
       'config_section_id' => '62',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'enable_123_chat' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '202',
         'config_section_id' => '62',
         'name' => 'enable_123_chat',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Enable 123Chat on site',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'antibruteforce' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '63',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'antibruteforce',
       'label' => NULL,
       'parent_section_id' => '0',
       'config_section_id' => '63',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'lock_stamp' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '206',
         'config_section_id' => '63',
         'name' => 'lock_stamp',
         'value' => 0,
         'presentation' => 'integer',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'lock_time' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '209',
         'config_section_id' => '63',
         'name' => 'lock_time',
         'value' => 2,
         'presentation' => 'integer',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'try_count' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '205',
         'config_section_id' => '63',
         'name' => 'try_count',
         'value' => 100,
         'presentation' => 'integer',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'try_number' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '208',
         'config_section_id' => '63',
         'name' => 'try_number',
         'value' => 0,
         'presentation' => 'integer',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'mobile' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '64',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'mobile',
       'label' => 'Skadate Mobile',
       'parent_section_id' => '0',
       'config_section_id' => '64',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'allow_registration' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '255',
         'config_section_id' => '64',
         'name' => 'allow_registration',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Allow new members registration',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'mobile_directory' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '211',
         'config_section_id' => '64',
         'name' => 'mobile_directory',
         'value' => 'm',
         'presentation' => 'varchar',
         'description' => 'Directory where mobile version is located',
         'php_validation' => 'return !preg_match("/[^a-zA-Z0-9_]/", $value);',
         'js_validation' => 'return (value.trim()&&!(/[^a-zA-Z0-9_]/.test(value) ) );',
      )),
      'redirect_to_mv' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '210',
         'config_section_id' => '64',
         'name' => 'redirect_to_mv',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Redirect mobile users to mobile version',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'Music' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '65',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'Music',
       'label' => 'Music',
       'parent_section_id' => '0',
       'config_section_id' => '65',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'allow_upload_ambed_music_files' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '216',
         'config_section_id' => '65',
         'name' => 'allow_upload_ambed_music_files',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Allow members to embed music files',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'allow_upload_music_files' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '215',
         'config_section_id' => '65',
         'name' => 'allow_upload_music_files',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Allow members to upload music files',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'display_music_list_limit' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '214',
         'config_section_id' => '65',
         'name' => 'display_music_list_limit',
         'value' => 10,
         'presentation' => 'integer',
         'description' => 'Number of songs  per page in music lists ',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'show_share_details' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '218',
         'config_section_id' => '65',
         'name' => 'show_share_details',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Show music sharing details',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'upload_music_files_limit' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '212',
         'config_section_id' => '65',
         'name' => 'upload_music_files_limit',
         'value' => 15,
         'presentation' => 'integer',
         'description' => 'Maximum number of music files per profile',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'upload_music_file_size_limit' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '213',
         'config_section_id' => '65',
         'name' => 'upload_music_file_size_limit',
         'value' => 10,
         'presentation' => 'float',
         'description' => 'Music file size limit (in Mb) ',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'user_activity' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '66',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'user_activity',
       'label' => 'Latest Activity',
       'parent_section_id' => '0',
       'config_section_id' => '66',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'items_count' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '219',
         'config_section_id' => '66',
         'name' => 'items_count',
         'value' => 10,
         'presentation' => 'integer',
         'description' => 'Number of last activities to be shown',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  '123_wm' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '71',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => '123_wm',
       'label' => 'IM (123WebMessenger)',
       'parent_section_id' => '0',
       'config_section_id' => '71',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'enable_123wm' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '220',
         'config_section_id' => '71',
         'name' => 'enable_123wm',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Enable',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
      'enable_123wm' => 
      array (
        0 => 
        array (
          'config_id' => '220',
          'value' => 'default',
          'label' => 'default',
          'name' => 'enable_123wm',
        ),
        1 => 
        array (
          'config_id' => '220',
          'value' => '123',
          'label' => '123 WebMessenger',
          'name' => 'enable_123wm',
        ),
      ),
    ),
  )),
  'photo_auth' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '68',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'photo_auth',
       'label' => 'Photo Authentication',
       'parent_section_id' => '0',
       'config_section_id' => '68',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'icon' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '222',
         'config_section_id' => '68',
         'name' => 'icon',
         'value' => 
        SK_ConfigDtoObject::__set_state(array(
           'rand' => 5792,
           'ext' => 'png',
        )),
         'presentation' => 'checkbox',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'fb_invite' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '75',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'fb_invite',
       'label' => 'Facebook Invite',
       'parent_section_id' => '0',
       'config_section_id' => '75',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'appId' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '237',
         'config_section_id' => '75',
         'name' => 'appId',
         'value' => NULL,
         'presentation' => 'varchar',
         'description' => 'ApplicationId',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'secret' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '238',
         'config_section_id' => '75',
         'name' => 'secret',
         'value' => NULL,
         'presentation' => 'checkbox',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'facebook_connect' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '80',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'facebook_connect',
       'label' => 'Facebook App Settings',
       'parent_section_id' => '0',
       'config_section_id' => '80',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'allow_synchronize' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '249',
         'config_section_id' => '80',
         'name' => 'allow_synchronize',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Allow profile synchronization',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'api_secret' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '248',
         'config_section_id' => '80',
         'name' => 'api_secret',
         'value' => '',
         'presentation' => 'varchar',
         'description' => 'API Secret',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'app_id' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '247',
         'config_section_id' => '80',
         'name' => 'app_id',
         'value' => '',
         'presentation' => 'varchar',
         'description' => 'APP ID',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'enabled' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '246',
         'config_section_id' => '80',
         'name' => 'enabled',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Enabled',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'enable_invite' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '317',
         'config_section_id' => '80',
         'name' => 'enable_invite',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Allow Invite',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'fields_required' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '263',
         'config_section_id' => '80',
         'name' => 'fields_required',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Profiles registered via Facebook Connect must fill in required fields',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'sex_aliasing' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '270',
         'config_section_id' => '80',
         'name' => 'sex_aliasing',
         'value' => 
        SK_ConfigDtoObject::__set_state(array(
           'male' => '2',
           'female' => '1',
        )),
         'presentation' => 'hidden',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'referrals' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '81',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'referrals',
       'label' => 'Referrals',
       'parent_section_id' => '0',
       'config_section_id' => '81',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'comission_type' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '251',
         'config_section_id' => '81',
         'name' => 'comission_type',
         'value' => 'amount',
         'presentation' => 'select',
         'description' => 'Comission type for referral subscriptions',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'registration_amount' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '254',
         'config_section_id' => '81',
         'name' => 'registration_amount',
         'value' => 0.1,
         'presentation' => 'float',
         'description' => 'Amount per referred user',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'subscription_amount' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '252',
         'config_section_id' => '81',
         'name' => 'subscription_amount',
         'value' => 0.1,
         'presentation' => 'float',
         'description' => 'Amount per subscription',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'subscription_percent' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '253',
         'config_section_id' => '81',
         'name' => 'subscription_percent',
         'value' => 5,
         'presentation' => 'float',
         'description' => 'Percent per subscription',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
      'comission_type' => 
      array (
        0 => 
        array (
          'config_id' => '251',
          'value' => 'amount',
          'label' => 'Amount',
          'name' => 'comission_type',
        ),
        1 => 
        array (
          'config_id' => '251',
          'value' => 'percent',
          'label' => 'Percent',
          'name' => 'comission_type',
        ),
      ),
    ),
  )),
  'configs' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '82',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'configs',
       'label' => NULL,
       'parent_section_id' => '0',
       'config_section_id' => '82',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'clear_config_cache' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '256',
         'config_section_id' => '82',
         'name' => 'clear_config_cache',
         'value' => 0,
         'presentation' => 'checkbox',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'coupon_codes' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '84',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'coupon_codes',
       'label' => 'Settings',
       'parent_section_id' => '0',
       'config_section_id' => '84',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'allow_recurring' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '269',
         'config_section_id' => '84',
         'name' => 'allow_recurring',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Allow using coupon codes for recurring membership plans',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'allow_reuse' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '268',
         'config_section_id' => '84',
         'name' => 'allow_reuse',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Allow re-using the same coupon code',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'newsfeed' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '85',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'newsfeed',
       'label' => 'Newsfeed Settings',
       'parent_section_id' => '0',
       'config_section_id' => '85',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'allow_comments' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '278',
         'config_section_id' => '85',
         'name' => 'allow_comments',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Allow Comments',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'allow_likes' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '277',
         'config_section_id' => '85',
         'name' => 'allow_likes',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Allow Likes',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'comments_count' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '275',
         'config_section_id' => '85',
         'name' => 'comments_count',
         'value' => 3,
         'presentation' => 'integer',
         'description' => 'Count of comments (Number of item comments shown by default in expanded mode)',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'display_count' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '279',
         'config_section_id' => '85',
         'name' => 'display_count',
         'value' => 10,
         'presentation' => 'integer',
         'description' => 'Items to show in Newsfeed component',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'features_expanded' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '276',
         'config_section_id' => '85',
         'name' => 'features_expanded',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Comments and likes boxes are expanded',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'share' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '87',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'share',
       'label' => 'Share',
       'parent_section_id' => '0',
       'config_section_id' => '87',
    )),
     'sub_sections' => 
    array (
      'facebook_share' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '88',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'facebook_share',
           'label' => 'Facebook share',
           'parent_section_id' => '87',
           'config_section_id' => '88',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'app_id' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '315',
             'config_section_id' => '88',
             'name' => 'app_id',
             'value' => '',
             'presentation' => 'varchar',
             'description' => 'App ID',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'color_scheme' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '297',
             'config_section_id' => '88',
             'name' => 'color_scheme',
             'value' => 'light',
             'presentation' => 'select',
             'description' => 'Color Scheme',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'enabled' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '298',
             'config_section_id' => '88',
             'name' => 'enabled',
             'value' => '1',
             'presentation' => 'checkbox',
             'description' => 'Enabled',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'font' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '299',
             'config_section_id' => '88',
             'name' => 'font',
             'value' => 'arial',
             'presentation' => 'select',
             'description' => 'Font',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'layout_style' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '300',
             'config_section_id' => '88',
             'name' => 'layout_style',
             'value' => 'button_count',
             'presentation' => 'select',
             'description' => 'Layout Style',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'send_button' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '301',
             'config_section_id' => '88',
             'name' => 'send_button',
             'value' => NULL,
             'presentation' => 'checkbox',
             'description' => 'Send button',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'show_faces' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '302',
             'config_section_id' => '88',
             'name' => 'show_faces',
             'value' => '1',
             'presentation' => 'checkbox',
             'description' => 'Show faces',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'verb_to_display' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '303',
             'config_section_id' => '88',
             'name' => 'verb_to_display',
             'value' => 'like',
             'presentation' => 'select',
             'description' => 'Verb to display',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'width' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '304',
             'config_section_id' => '88',
             'name' => 'width',
             'value' => '50',
             'presentation' => 'integer',
             'description' => 'Width',
             'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
          )),
        ),
         'config_values' => 
        array (
          'color_scheme' => 
          array (
            0 => 
            array (
              'config_id' => '297',
              'value' => 'light',
              'label' => 'light',
              'name' => 'color_scheme',
            ),
            1 => 
            array (
              'config_id' => '297',
              'value' => 'dark',
              'label' => 'dark',
              'name' => 'color_scheme',
            ),
          ),
          'font' => 
          array (
            0 => 
            array (
              'config_id' => '299',
              'value' => 'arial',
              'label' => 'arial',
              'name' => 'font',
            ),
            1 => 
            array (
              'config_id' => '299',
              'value' => 'trebuchet ms',
              'label' => 'trebuchet ms',
              'name' => 'font',
            ),
            2 => 
            array (
              'config_id' => '299',
              'value' => 'verdana',
              'label' => 'verdana',
              'name' => 'font',
            ),
            3 => 
            array (
              'config_id' => '299',
              'value' => 'lucida grande',
              'label' => 'lucida grande',
              'name' => 'font',
            ),
            4 => 
            array (
              'config_id' => '299',
              'value' => 'segoe ui',
              'label' => 'segoe ui',
              'name' => 'font',
            ),
            5 => 
            array (
              'config_id' => '299',
              'value' => 'tahoma',
              'label' => 'tahoma',
              'name' => 'font',
            ),
          ),
          'layout_style' => 
          array (
            0 => 
            array (
              'config_id' => '300',
              'value' => 'standart',
              'label' => 'standart',
              'name' => 'layout_style',
            ),
            1 => 
            array (
              'config_id' => '300',
              'value' => 'button_count',
              'label' => 'button_count',
              'name' => 'layout_style',
            ),
            2 => 
            array (
              'config_id' => '300',
              'value' => 'box_count',
              'label' => 'box_count',
              'name' => 'layout_style',
            ),
          ),
          'verb_to_display' => 
          array (
            0 => 
            array (
              'config_id' => '303',
              'value' => 'like',
              'label' => 'like',
              'name' => 'verb_to_display',
            ),
            1 => 
            array (
              'config_id' => '303',
              'value' => 'recommend',
              'label' => 'recommend',
              'name' => 'verb_to_display',
            ),
          ),
        ),
      )),
      'google_share' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '89',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'google_share',
           'label' => 'Google share',
           'parent_section_id' => '87',
           'config_section_id' => '89',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'annotation' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '310',
             'config_section_id' => '89',
             'name' => 'annotation',
             'value' => 'inline',
             'presentation' => 'select',
             'description' => 'Annotation',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'enabled' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '311',
             'config_section_id' => '89',
             'name' => 'enabled',
             'value' => '1',
             'presentation' => 'checkbox',
             'description' => 'Enabled',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'size' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '312',
             'config_section_id' => '89',
             'name' => 'size',
             'value' => 'medium',
             'presentation' => 'select',
             'description' => 'Size',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'width' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '313',
             'config_section_id' => '89',
             'name' => 'width',
             'value' => '200',
             'presentation' => 'integer',
             'description' => 'Width',
             'php_validation' => 'return !preg_match("/[^0-9]/", $value);',
             'js_validation' => 'return (value.trim()&&!(/[^0-9 ]/.test(value)));',
          )),
        ),
         'config_values' => 
        array (
          'annotation' => 
          array (
            0 => 
            array (
              'config_id' => '310',
              'value' => 'none',
              'label' => 'none',
              'name' => 'annotation',
            ),
            1 => 
            array (
              'config_id' => '310',
              'value' => 'inline',
              'label' => 'inline',
              'name' => 'annotation',
            ),
            2 => 
            array (
              'config_id' => '310',
              'value' => 'bubble',
              'label' => 'bubble',
              'name' => 'annotation',
            ),
          ),
          'size' => 
          array (
            0 => 
            array (
              'config_id' => '312',
              'value' => 'tall',
              'label' => 'Tall (60px)',
              'name' => 'size',
            ),
            1 => 
            array (
              'config_id' => '312',
              'value' => 'medium',
              'label' => 'Medium (20px)',
              'name' => 'size',
            ),
            2 => 
            array (
              'config_id' => '312',
              'value' => 'standart',
              'label' => 'Standard (24px)',
              'name' => 'size',
            ),
            3 => 
            array (
              'config_id' => '312',
              'value' => 'small',
              'label' => 'Small (15px)',
              'name' => 'size',
            ),
          ),
        ),
      )),
      'twitter_share' => 
      SK_Inner_Config_Section::__set_state(array(
         'section_id' => '90',
         'section_info' => 
        SK_ConfigDtoObject::__set_state(array(
           'section' => 'twitter_share',
           'label' => 'Twitter share',
           'parent_section_id' => '87',
           'config_section_id' => '90',
        )),
         'sub_sections' => 
        array (
        ),
         'configs' => 
        array (
          'enabled' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '307',
             'config_section_id' => '90',
             'name' => 'enabled',
             'value' => '1',
             'presentation' => 'checkbox',
             'description' => 'Enabled',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'large_button' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '308',
             'config_section_id' => '90',
             'name' => 'large_button',
             'value' => NULL,
             'presentation' => 'checkbox',
             'description' => 'Large button',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'opt_out' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '305',
             'config_section_id' => '90',
             'name' => 'opt_out',
             'value' => '1',
             'presentation' => 'checkbox',
             'description' => 'Opt-out of tailoring Twitter',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
          'show_count' => 
          SK_ConfigDtoObject::__set_state(array(
             'config_id' => '306',
             'config_section_id' => '90',
             'name' => 'show_count',
             'value' => '1',
             'presentation' => 'checkbox',
             'description' => 'Show count',
             'php_validation' => NULL,
             'js_validation' => NULL,
          )),
        ),
         'config_values' => 
        array (
        ),
      )),
    ),
     'configs' => 
    array (
    ),
     'config_values' => 
    array (
    ),
  )),
  'slideshow' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '92',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'slideshow',
       'label' => 'Slideshow settings',
       'parent_section_id' => '0',
       'config_section_id' => '92',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'effect' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '326',
         'config_section_id' => '92',
         'name' => 'effect',
         'value' => 'fade',
         'presentation' => 'select',
         'description' => 'Animation effect',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'interval' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '325',
         'config_section_id' => '92',
         'name' => 'interval',
         'value' => 'long',
         'presentation' => 'select',
         'description' => 'Animation interval',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'navigation' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '324',
         'config_section_id' => '92',
         'name' => 'navigation',
         'value' => 1,
         'presentation' => 'checkbox',
         'description' => 'Enable slides navigation',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
      'effect' => 
      array (
        0 => 
        array (
          'config_id' => '326',
          'value' => 'fade',
          'label' => 'Fade',
          'name' => 'effect',
        ),
        1 => 
        array (
          'config_id' => '326',
          'value' => 'slide',
          'label' => 'Slide',
          'name' => 'effect',
        ),
      ),
      'interval' => 
      array (
        0 => 
        array (
          'config_id' => '325',
          'value' => 'long',
          'label' => 'Long',
          'name' => 'interval',
        ),
        1 => 
        array (
          'config_id' => '325',
          'value' => 'medium',
          'label' => 'Medium',
          'name' => 'interval',
        ),
        2 => 
        array (
          'config_id' => '325',
          'value' => 'short',
          'label' => 'Short',
          'name' => 'interval',
        ),
      ),
    ),
  )),
  'google' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '93',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'google',
       'label' => 'Google Application Settings',
       'parent_section_id' => '0',
       'config_section_id' => '93',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'client_id' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '328',
         'config_section_id' => '93',
         'name' => 'client_id',
         'value' => NULL,
         'presentation' => 'varchar',
         'description' => 'Client ID',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'client_secret' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '329',
         'config_section_id' => '93',
         'name' => 'client_secret',
         'value' => NULL,
         'presentation' => 'varchar',
         'description' => 'Client Secred',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'enable' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '339',
         'config_section_id' => '93',
         'name' => 'enable',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Enable',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'security' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '94',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'security',
       'label' => 'Site security',
       'parent_section_id' => '0',
       'config_section_id' => '94',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'count_email_list' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '330',
         'config_section_id' => '94',
         'name' => 'count_email_list',
         'value' => 0,
         'presentation' => 'hidden',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'count_ip_list' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '331',
         'config_section_id' => '94',
         'name' => 'count_ip_list',
         'value' => 0,
         'presentation' => 'hidden',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'count_spam_attempt' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '332',
         'config_section_id' => '94',
         'name' => 'count_spam_attempt',
         'value' => 0,
         'presentation' => 'hidden',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'enable' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '341',
         'config_section_id' => '94',
         'name' => 'enable',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Enable Country blocker',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'enable_block_ip' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '333',
         'config_section_id' => '94',
         'name' => 'enable_block_ip',
         'value' => false,
         'presentation' => 'checkbox',
         'description' => 'Enable StopForumSpam IP blocker',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'time_next_update' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '335',
         'config_section_id' => '94',
         'name' => 'time_next_update',
         'value' => 0,
         'presentation' => 'hidden',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'time_reset_spam_attempt' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '336',
         'config_section_id' => '94',
         'name' => 'time_reset_spam_attempt',
         'value' => 0,
         'presentation' => 'hidden',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
      'time_update_database' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '337',
         'config_section_id' => '94',
         'name' => 'time_update_database',
         'value' => 0,
         'presentation' => 'hidden',
         'description' => NULL,
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
  'cloudflare' => 
  SK_Inner_Config_Section::__set_state(array(
     'section_id' => '96',
     'section_info' => 
    SK_ConfigDtoObject::__set_state(array(
       'section' => 'cloudflare',
       'label' => 'CloudFlare',
       'parent_section_id' => '0',
       'config_section_id' => '96',
    )),
     'sub_sections' => 
    array (
    ),
     'configs' => 
    array (
      'enable' => 
      SK_ConfigDtoObject::__set_state(array(
         'config_id' => '342',
         'config_section_id' => '96',
         'name' => 'enable',
         'value' => true,
         'presentation' => 'checkbox',
         'description' => 'Enable CloudFlare',
         'php_validation' => NULL,
         'js_validation' => NULL,
      )),
    ),
     'config_values' => 
    array (
    ),
  )),
);
?>