<?php
// define tabs menu items
$doc_url = 'https://www.skalfa.com/ca/docs/';

$help_manual_items = array
(
	'languages' => array
	(
            'lang_tree' => array(
                'href' => $doc_url . 'introduction_configuration_languages#edit-language',
                'label' => 'Help'
            ),

            'search' => array(
                'href' => $doc_url . 'introduction_configuration_languages#search',
                'label' => 'Help'
            ),

            'langs' => array(
                'href' => $doc_url . 'introduction_configuration_languages#languages1',
                'label' => 'Help'
            ),

            'tools' => array(
                'href' => $doc_url . 'introduction_configuration_languages#tools',
                'label' => 'Help'
            ),

            'langs_packages' => array(
                'href' => $doc_url . 'introduction_configuration_languages#tools',
                'label' => 'Help'
            ),
	),

	'press' => array
	(
		'categories' => array(
			'href'	=>	URL_ADMIN.'press_categories.php',
			'label'	=>	'Categories'
		),

		'post_list' => array(
			'href'	=>	URL_ADMIN.'press_post_list.php',
			'label'	=>	'Posts'
		),

		'settings' => array(
			'href'	=>	URL_ADMIN.'press_settings.php',
			'label'	=>	'Settings'
		)
	),

	'prof_field_list' => array
	(
		'all' => array(
            'href' => $doc_url . 'introduction_configuration_profile_fields',
            'label' => 'Help'
		),

		'join' => array(
            'href' => $doc_url . 'introduction_configuration_profile_fields',
            'label' => 'Help'
		),

		'edit' => array(
            'href' => $doc_url . 'introduction_configuration_profile_fields',
            'label' => 'Help'
		),

		'view' => array(
            'href' => $doc_url . 'introduction_configuration_profile_fields',
            'label' => 'Help'
		),

		'search' => array(
            'href' => $doc_url . 'introduction_configuration_profile_fields',
            'label' => 'Help'
		),
        'main' => array(
			'href' => $doc_url . 'introduction_configuration_profile_fields',
            'label' => 'Help'
		),
        'new' => array(
			'href' => $doc_url . 'introduction_configuration_profile_fields#how-to-create-a-new-profile-field',
            'label' => 'Help'
		)

	),

	'navigation' => array
	(
		'nav_menu' => array(
			'href' => $doc_url . 'introduction_configuration_navigation#menu-items',
            'label' => 'Help'
		),

		'nav_doc' => array(
			'href' => $doc_url . 'introduction_configuration_navigation#documents',
            'label' => 'Help'
		),

		'nav_settings' => array(
			'href' => $doc_url . 'introduction_configuration_navigation#additional-settings',
            'label' => 'Help'
		)
	),

	'ads' => array
	(
            'binding' => array(
                'href' => $doc_url . 'introduction_management_advertisement#binding',
                'label' => 'Help'
            ),

            'template_set' => array(
                'href' => $doc_url . 'introduction_management_advertisement',
                'label' => 'Help'
            ),

            'template' => array(
                'href' => $doc_url . 'introduction_management_advertisement#templates',
                'label' => 'Help'
            )

	),

	'affiliate' => array
	(
		'affiliate' => array(
			'href' => $doc_url . 'introduction_management_affiliates#affiliates-area',
            'label' => 'Help'
		),

		'settings' => array(
			'href' => $doc_url . 'introduction_management_affiliates#settings',
            'label' => 'Help'
		)
	),

	'payment_providers' => array
	(
            'payment_providers' => array(
                'href' => $doc_url . 'introduction_configuration_providers',
                'label' => 'Help'
            ),

            'sms_billing' => array(
                'href' => $doc_url . 'introduction_configuration_providers#sms-billing',
                'label' => 'Help'
            ),
	),

	'profiles'	=> array
	(
		'statistic'	=> array(
            'href' => $doc_url . 'introduction_management_profiles#statistics',
            'label' => 'Help'
		),

		'rest_username'	=> array(
            'href' => $doc_url . 'introduction_management_profiles#restricted-usernames',
            'label' => 'Help'
		)
	),

	'finance'	=> array
	(
            'finance' => array(
                'href' => $doc_url . 'introduction_management_finance',
                'label' => 'Help'
            ),

            'point_payments' => array(
                'href' => $doc_url . 'introduction_management_finance#credits-payments',
                'label' => 'Help'
            ),
            
            'sms_transactions' => array(
                'href' => $doc_url . 'introduction_management_finance#sms-payments',
                'label' => 'Help'
            )
	),

	'mass_mailing'	=> array
	(
            'mass_mailing' => array
            (
                'href' => $doc_url . 'introduction_management_mass_mailing',
                'label' => 'Help'
            ),
            'mailbox'=> array
            (
                'href' => $doc_url . 'introduction_management_mass_mailing#mail-settings',
                'label' => 'Help'
            ),
	),

	'index_page' => array
	(
            'index_page' => array(
                'href' => $doc_url . 'introduction_configuration_template#index-page-builder',
                'label' => 'Help'
            ),

            'home_page' => array(
                'href' => $doc_url . 'introduction_configuration_template#index-page-builder-memberhome-page-builder',
                'label' => 'Help'
            ),

            'templates' => array(
                'href' => $doc_url . 'introduction_configuration_template#choose-template',
                'label' => 'Help'
            ),

            'slideshow' => array(
                'href' => $doc_url . 'introduction_configuration_template#slideshow',
                'label' => 'Help'
            )
	),

	'caching' => array(
            'caching'=>	array(
                'href' => $doc_url . 'introduction_configuration_site_caching',
                'label' => 'Help'
            ),
            'cloudflair'=>	array(
                'href' => $doc_url . 'introduction_configuration_site_caching#cloudflair',
                'label' => 'Help'
            )
	),

	/* 'events'	=> array
	(
		'list'	=> array(
			'href' => $doc_url . 'introduction_configuration_providers',
            'label' => 'Help'
		)
	), */

	'polls'	=> array
	(
		'polls'	=> array(
			'href' => $doc_url . 'introduction_management_polls',
            'label' => 'Help'
		),
	),

	'vkiss'	=> array
	(
		'vkiss'	=> array(
			'href' => $doc_url . 'introduction_configuration_winks',
            'label' => 'Help'
		)
	),

	'membership_types_list'	=> array
	(
            'membership_types_list' => array(
                'href' => $doc_url . 'introduction_configuration_types',
                'label' => 'Help'
            ),

            'edit_membership_type' => array(
                'href' => $doc_url . 'introduction_configuration_types',
                'label' => 'Help'
            ),
		
            'service_list' => array(
                'href' => $doc_url . 'introduction_configuration_types#membership-services',
                'label' => 'Help'
            ),

            'coupon_codes' => array(
                'href' => $doc_url . 'introduction_configuration_types#coupon-codes',
                'label' => 'Help'
            )
	),

        'user_points' => array(
            'point_packages' => array(
                'href' => $doc_url . 'introduction_configuration_user_credits',
                'label' => 'Help'
            ),

            'spend_credits' => array(
                'href' => $doc_url . 'introduction_configuration_user_credits',
                'label' => 'Help'
            ),

            'earn_credits' => array(
                'href' => $doc_url . 'introduction_configuration_user_credits',
                'label' => 'Help'
            )
        ),
	'features'	=> array
	(
            'features' => array(
                'href' => $doc_url . 'introduction_configuration_features',
                'label' => 'Help'
            ),

            'dating' => array(
                'href' => $doc_url . 'introduction_configuration_features',
                'label' => 'Help'
            )
	),

	'config_media'	=> array
	(
		'config_media'	=> array(
			'href' => $doc_url . 'introduction_configuration_video',
            'label' => 'Help'
		)
	),

	'chat'	=> array
	(
		'configs'	=> array(
			'href' => $doc_url . 'introduction_configuration_chat',
            'label' => 'Help'
		)
	),

	'config_reg_invite'	=> array
	(
		'config_reg_invite'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_invitation#configuration',
            'label' => 'Help'
		),
		'send_reg_invite'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_invitation#send-invitation',
            'label' => 'Help'
		)
	),

	/* 'friends_network'	=> array
	(
		'friends_network'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_providers',
            'label' => 'Help'
		)
	), */

	'config_photo'	=> array
	(
            'config_photo' => array
            (
                'href' => $doc_url . 'introduction_configuration_photo',
                'label' => 'Help'
            ),

            'photo_verification' => array(
                'href' => $doc_url . 'introduction_configuration_photo#verification',
                'label' => 'Help'
            )
	),

        'music' => array(
            'music' => array(
                'href' => $doc_url . 'introduction_configuration_music',
                'label' => 'Help'
            )
        ),

        'games' => array(
            'games' => array(
                'href' => $doc_url . 'introduction_configuration_games',
                'label' => 'Help'
            )
        ),

        'latest-activity' => array(
            'settings' => array(
                'href' => $doc_url . 'introduction_configuration_latest_activity',
                'label' => 'Help'
            )
        ),

        'gifts' => array(
            'gifts' => array(
                'href' => $doc_url . 'introduction_configuration_virtual_gifts',
                'label' => 'Help'
            ),
            'categories' => array(
                'href' => $doc_url . 'introduction_configuration_virtual_gifts',
                'label' => 'Help'
            )
        ),

	'reports'	=> array
	(
		'reports_list'	=> array
		(
			'href' => $doc_url . 'introduction_management_reports',
            'label' => 'Help'
		)
	),
	'site'	=> array
	(
		'official'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_global_configuration#site-settings',
            'label' => '&nbsp;'
		),

		'site_status'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_global_configuration#site-status',
            'label' => '&nbsp;'
		),

		'admin'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_global_configuration#admin-settings',
            'label' => '&nbsp;'
		),

            'access' => array
            (
                'href' => $doc_url . 'introduction_configuration_global_configuration#site-access',
                'label' => '&nbsp;'
            ),

		'additional' => array
		(
			'href' => $doc_url . 'introduction_configuration_global_configuration#additional-settings',
            'label' => '&nbsp;'
		),
		'automode'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_global_configuration#auto-mode',
            'label' => '&nbsp;'
		)
	),
	'blocked_ip'	=> array
	(
		'main'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_blocked',
            'label' => 'Help'
		),
		'list'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_blocked',
            'label' => 'Help'
		)
	),

        'rest_username' => array(
            'rest_username' => array(
                'href' => $doc_url . 'introduction_configuration_restricted',
                'label' => 'Help'
            )
        ),

	'services'	=> array
	(
            'autocrop' => array(
                'href' => $doc_url . 'introduction_management_advanced_services#face-juggle',
                'label' => 'Help'
            ),

            'chat_services' => array(
                'href' => $doc_url . 'introduction_management_advanced_services#chats',
                'label' => 'Help'
            ),

            'mobile' => array(
                'href' => $doc_url . 'introduction_management_advanced_services#skadate-me',
                'label' => 'Help'
            ),

            '123flashchat' => array(
                'href' => $doc_url . 'introduction_management_advanced_services#flash-chat',
                'label' => 'Help'
            )
	),
	'forum'	=> array
	(
		'management'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_forum#manage',
            'label' => 'Help'
		),
		'settings'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_forum#settings',
            'label' => 'Help'
		)
	), 
	 'classifieds'	=> array
	(
		'classifieds_settings'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_classifieds#general-settings',
                        'label' => 'Help'
		),
		'classifieds_wanted'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_classifieds#wanted-settings',
                        'label' => 'Help'
		),
		'classifieds_offer'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_classifieds#offer-settings',
                        'label' => 'Help'
		)
	), 
	'groups' => array
	(
            'groups' => array(
                'href' => $doc_url . 'introduction_configuration_groups',
                'label' => 'Help'
            ),
            'group_settings' => array(
                'href' => $doc_url . 'introduction_configuration_groups#group-settings',
                'label' => 'Help'
            )
	),
	/* 'link_dir'	=> array
	(
		'link_dir_catgs'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_providers',
            'label' => 'Help'
		),
		'link_dir_links'	=> array
		(
			'href' => $doc_url . 'introduction_configuration_providers',
            'label' => 'Help'
		)
	), */
	'profile_message' => array
	(
		'messages'	=> array
		(
			'href' => $doc_url . 'introduction_management_profile_message_filter',
            'label' => 'Help'
		),
		'keywords'	=> array
		(
			'href' => $doc_url . 'introduction_management_profile_message_filter',
            'label' => 'Help'
		)
	),
	'blogs'	=> array
	(
		'blogs_conf'	=> array
		(
			'href' => $doc_url . 'introduction_management_blogs',
            'label' => 'Help'
		)
	),

        'newsfeed' => array(
            'newsfeed_conf' => array(
                'href' => $doc_url . 'introduction_management_newsfeed',
                'label' => 'Help'
            )

        ),
    
	'referrals'	=> array
	(
            'referral_list' => array(
                'href' => $doc_url . 'introduction_management_referrals',
                'label' => 'Help'
            ),

            'settings' => array(
                'href' => $doc_url . 'introduction_management_referrals#settings',
                'label' => 'Help'
            )
	),
    
	'mail_scheduler' => array
	(
		'scheduler' => array
		(
			'href' => $doc_url . 'introduction_management_activity_scheduler',
            'label' => 'Help'
		),
		'mail_template'	=> array
		(
			'href' => $doc_url . 'introduction_management_activity_scheduler#create-mail-template',
            'label' => 'Help'
		),
		'mail_setting'	=> array
		(
			'href' => $doc_url . 'introduction_management_activity_scheduler#configure-the-settings',
            'label' => 'Help'
		)
	),
	'administration' => array
	(
		'admin_list' => array
		(
			'href' => $doc_url . 'introduction_management_moderators',
            'label' => 'Help'
		),
	),
	'sitemap' => array(
            'sitemap' => array(
                'href' => $doc_url . 'introduction_management_site_map',
                'label' => 'Help'
            ),
            'seo' => array(
                'href' => $doc_url . 'introduction_management_site_map#seotracking',
                'label' => 'Help'
            )
	),
        'badwords' => array
        (
            'badwords' => array(
                            'href' => $doc_url . 'introduction_management_badwords',
                'label' => 'Help'
                    )
        ),
       'email_settings' => array
        (
            'email_settings' => array(
                            'href' => $doc_url . 'introduction_configuration_email_settings?s=email settings',
                'label' => 'Help'
                    )
        ),

        'fbconnect' => array(
            'fbconnect_fields' => array(
                'href' => $doc_url . 'introduction_configuration_facebook',
                'label' => 'Help'
            ),

            'fbconnect_settings' => array(
                'href' => $doc_url . 'introduction_configuration_facebook',
                'label' => 'Help'
            )
        ),

        'share' => array(
            'facebook_share' => array(
                'href' => $doc_url . 'introduction_configuration_share#facebook',
                'label' => 'Help'
            ),

            'twitter_share' => array(
                'href' => $doc_url . 'introduction_configuration_share#twitter',
                'label' => 'Help'
            ),

            'google_share' => array(
                'href' => $doc_url . 'introduction_configuration_share#g-1',
                'label' => 'Help'
            )
        ),
    
        'splash_screen' => array(
            'splash_screen' => array(
                'href' => $doc_url . 'introduction_configuration_splash_screen',
                'label' => '&nbsp;'
            )
        ),
    
        'security' => array(
            'security' => array(
                'href' => $doc_url . 'introduction_configuration_site_security',
                'label' => '&nbsp;'
            ),
            'status' => array(
                'href' => $doc_url . 'introduction_configuration_site_security#sfs-ip-blocking',
                'label' => '&nbsp;'
            ),
            'country' => array(
                'href' => $doc_url . 'introduction_configuration_site_security#country-blocking',
                'label' => '&nbsp;'
            ),
            'blocked_ip' => array(
                'href' => $doc_url . 'introduction_configuration_site_security#blocked-ip',
                'label' => '&nbsp;'
            ),
        ),

        'google' => array(
            'google_settings' => array(
                'href' => $doc_url . 'introduction_configuration_google',
                'label' => '&nbsp;'
            ),
        )
);

$help_manual_link = '';

if( $help_manual_items[$file_key][$active_tab] )
{
    $manual_href = $help_manual_items[$file_key][$active_tab]['href'];
    $manual_label = $help_manual_items[$file_key][$active_tab]['label'];

    $help_manual_link = "<a class='help_manual' href='" . $manual_href . "'></a>";
}
else
{
    $manual_href = $doc_url . 'no_content';
    $manual_label = 'Help';

    $help_manual_link = "<a class='help_manual' href='" . $manual_href . "'></a>";
}

$frontend->assign( 'help_manual_link', $help_manual_link );
?>
