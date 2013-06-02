<?php
//if ( !$file_key )
	//fail_error( 'undefined <code>$file_key</code>' );

// define sidebar menu items
$sidebar_menu_items = array
(
	array
	(
		'key'	=> 'management',
		'name'	=> 'Management',
		'items' => array
		(
			'profiles' => array
			(
				'href' => URL_ADMIN.'profiles.php',
				'label' => 'Profiles',
				'icon' => 'profiles'
			),
			'reports' => array
			(
				'href' => URL_ADMIN.'reports.php',
				'label' => 'Member Reports',
				'icon' => 'reports'
			),
			'finance' => array
			(
				'href' => URL_ADMIN.'transaction_list.php',
				'label' => 'Finance',
				'icon' => 'finance'
			),

			'ads' => array
			(
				'href' => URL_ADMIN.'ads_binding.php',
				'label' => 'Advertisement',
				'icon' => 'ads'
			),

			'mass_mailing' => array(
				'href'	=> URL_ADMIN.'mass_mailing.php',
				'label' => 'General Mail settings',
				'icon' => 'mass_mailing'
			),
			/*
			'events' => array
			(
				'href'	=> URL_ADMIN.'events.php',
				'label'	=> 'Events',
				'icon' => 'events'
			),

			'press' => array
			(
				'href'	=>	URL_ADMIN.'press_categories.php',
				'label'	=>	'Press',
				'icon' => 'press'
			),
			*/
			'polls' => array
			(
				'href'	=> URL_ADMIN.'polls.php',
				'label'	=> 'Polls',
				'icon' => 'polls'
			),

			'badwords' => array
			(
				'href'	=>	URL_ADMIN. 'badwords.php',
				'label'	=>	'Badwords',
				'icon' => 'badwords'
			),

			'administration' => array
			(
				'href' 	=> URL_ADMIN.'administration.php',
				'label' => 'Administrators',
				'icon' => 'moderators'
			),

			'affiliate'	=>	array
			(
				'href'	=>	URL_ADMIN.'affiliate_list.php',
				'label'	=>	'Affiliates',
				'icon' => 'affiliates'
			),
			'services'	=> array
			(
				'href'	=> URL_ADMIN.'services.php',
				'label'	=> 'Addons',
				'icon' => 'advanced_services'
			),
			/*
			'link_dir'	=> array
			(
				'href'	=> URL_ADMIN.'link_dir_categories.php',
				'label'	=> 'Links Directory',
				'icon' => 'links_directory'
			),*/
			'profile_message'	=> array
			(
				'href'	=> URL_ADMIN.'profile_message.php',
				'label'	=> 'Profile Messages Filter',
				'icon' => 'filter'
			),
			'blogs'	=> array
			(
				'href'	=> URL_ADMIN.'blogs_conf.php',
				'label'	=> 'Blogs',
				'icon' => 'blogs'
			),

			'newsfeed'	=> array
			(
				'href'	=> URL_ADMIN.'newsfeed_conf.php',
				'label'	=> 'Newsfeed',
				'icon' => 'newsfeed'
			),

			'referrals'	=> array
			(
				'href'	=> URL_ADMIN.'referral_list.php',
				'label'	=> 'Referrals',
				'icon' => 'referrals'
			),
			'mail_scheduler' => array
			(
				'href'	=> URL_ADMIN.'mail_scheduler.php',
				'label'	=> 'Mail Scheduler',
				'icon' => 'scheduler'
			),
			'sitemap' => array(
				'href'	=>URL_ADMIN.'sitemap.php',
				'label'	=> 'SEO',
				'icon'	=> 'blogs'
			)
		)
	),

	array
	(
		'key'	=> 'configuration',
		'name'	=> 'Configuration',
		'items' => array
		(
			'site' => array(
				'href'	=>	URL_ADMIN.'site.php?unit=official',
				'label'	=>	'Global Configuration',
				'icon'	=> 'global_configuration'
			),

                    'security' => array
                    (
                        'href'=> URL_ADMIN.'security.php',
                        'label' => 'Site security',
                        'icon' => 'security'
                    ),

			'languages' => array(
				'href'	=>	URL_ADMIN.'languages.php',
				'label'	=>	'Languages',
				'icon'	=> 'languages'
			),

			'index_page' => array(
				'href'	=> URL_ADMIN.'index_page.php',
				'label'	=> 'Template/Index Page',
				'icon'	=> 'index_page'
			),

			'caching'	=>	array(
				'href'	=> URL_ADMIN.'caching.php',
				'label'	=> 'Site Caching',
				'icon'	=> 'site_caching'
			),

			'prof_field_list' => array(
				'href'	=>	URL_ADMIN.'profile_field_list.php',
				'label'	=>	'Profile Fields',
				'icon'	=>	'profile_fields'
			),

			'forum'	=> array(
				'href'	=> URL_ADMIN.'forum_management.php',
				'label'	=> 'Forum',
				'icon' => 'forum_menu'
			),

			'classifieds'	=> array(
				'href'	=> URL_ADMIN.'classifieds.php',
				'label'	=> 'Classifieds',
				'icon' => 'cls_menu'
			),

			'groups'	=> array(
				'href'	=> URL_ADMIN.'groups.php',
				'label'	=> 'Groups',
				'icon' => 'profiles'
			),

			'features' => array(
				'href' => URL_ADMIN.'features.php',
				'label' => 'Site Features',
				'icon'	=>	'site_features'
			),

			'membership_types_list' => array(
				'href' => URL_ADMIN.'membership_type_list.php',
				'label' => 'Memberships',
				'icon'	=>	'membership_types'
			),

			'user_points' => array(
                'href' => URL_ADMIN.'point_packages.php',
                'label' => 'User Credits',
                'icon'  => 'points'
            ),

			'payment_providers' => array(
				'href' => URL_ADMIN.'payment_providers.php',
				'label' => 'Payment Gateways',
				'icon'	=>	'payment_providers'
			),

			'navigation' => array(
				'href' => URL_ADMIN.'nav_menu.php',
				'label' => 'Navigation',
				'icon'	=> 'navigation'
			),

			'config_media' => array(
				'href'	=> URL_ADMIN.'config_media.php',
				'label'	=> 'Video',
				'icon'	=> 'multimedia'
			),

			'chat' => array(
				'href'	=> URL_ADMIN.'chat.php',
				'label'	=> 'Chat',
				'icon'	=> 'chat'
			),

			'config_reg_invite' => array(
				'href'	=> URL_ADMIN.'config_reg_invite.php',
				'label'	=> 'Registration/Invitation',
				'icon'	=> 'reg_inv'
			),

			'config_photo' => array(
				'href' => URL_ADMIN.'config_photo.php',
				'label' => 'Photo',
				'icon'	=> 'photo'
			),

            'music' => array(
				'href'	=>URL_ADMIN.'music.php',
				'label'	=> 'Music',
				'icon'	=> 'audio'
			),

            'games' => array(
				'href'	=>URL_ADMIN.'games.php',
				'label'	=> 'Games',
				'icon'	=> 'games'
			),

            'latest-activity' => array(
				'href'	=>URL_ADMIN.'latest-activity.php',
				'label'	=> 'Latest Activity',
				'icon'	=> 'sidebar_menu_item profile_fields'
			),

			'gifts' => array(
				'href' => URL_ADMIN.'virtual_gifts.php',
				'label' => 'Virtual Gifts',
				'icon'	=> 'gifts'
			),

			'email_settings' => array(
				'href'	=> URL_ADMIN.'email_settings.php',
				'label'	=> 'SMTP settings',
				'icon'	=> 'email_settings'
			),

    		'rest_username'  => array(
                'href'  => URL_ADMIN.'rest_username.php',
                'label' => 'Restricted Usernames',
			    'icon'   => 'rest_username'
            ),

            'fbconnect'  => array(
                'href'  => URL_ADMIN.'fbconnect_fields.php',
                'label' => 'Facebook',
                'icon'   => 'fbconnect'
            ),


            'share' => array(
                    'href'  => URL_ADMIN.'share.php',
                    'label' => 'Share',
                    'icon'  => 'share'
            ),

            'google' => array(
                    'href'  => URL_ADMIN.'google.php',
                    'label' => 'Google',
                    'icon'  => 'google'
            ),

            'splash_screen' => array(
                    'href'  => URL_ADMIN.'site.php?unit=splash_screen',
                    'label' => 'Splash Screen',
                    'icon'  => 'splash_screen'
            )
            )
	)
);
//--
if(!preg_match('/auth.php/', $_SERVER['PHP_SELF']))
{
	if ($admin_id = getAdminId())
	{
		$_query = sql_placeholder("SELECT `file_key` FROM `?#TBL_LINK_ADMIN_DOCUMENT` WHERE `admin_id`=?", $admin_id );
		foreach (MySQL::fetchArray($_query) as $admin_section)
			$_admin_sections[] = $admin_section['file_key'];
		$_admin_sections = ($_admin_sections)?$_admin_sections:array();

	}
	//--
$sidebar_menu = '';
foreach( $sidebar_menu_items as $menu_info )
{
	$sidebar_menu .= '<div class="sidebar_menu_union_'.$menu_info['key'].'">'.$menu_info['name'].'</div>';
	$sidebar_menu .= '<div class="sidebar_menu_union_'.$menu_info['key'].'_cont"><ul>';
	foreach ( $menu_info['items'] as $key => $item )
	{
            //printArr($_admin_sections);
			if( getAdminId() && !in_array($key, $_admin_sections))
				continue;
		$sidebar_menu .= '
<li class="sidebar_menu_item '.$item['icon'].'"><a href="'.$item['href'].'"';
		if( $file_key == $key )
			$sidebar_menu .= ' class="sidebar_menu_active_link"';
		$sidebar_menu .= '>'.$item['label'].'</a></li>';
	}
	$sidebar_menu .= '</ul></div>';
}


// define tabs menu items
$tabs_menu_items = array
(
	'languages' => array
	(
            'lang_tree' => array
            (
                'href' => URL_ADMIN.'languages.php',
                'label'	=> 'Edit Language',
                'help_manual' => array
                (
                    'href' => 'http://www.skadate.com/ca/docs/introduction_configuration_languages#edit-language',
                    'label' => 'Help'
                )
		),

            'search' => array
            (
                'href' => URL_ADMIN.'language_search.php',
                'label'	=> 'Search',
                'help_manual' => array
                (
                    'href' => 'http://www.skadate.com/ca/docs/introduction_configuration_languages#search',
                    'label' => 'Help'
                )
		),

            'langs_packages' => array
            (
                'href' => URL_ADMIN . 'langs_manage.php',
                'label'	=> 'Language Packages',
                'help_manual' => array
                (
                    'href' => 'http://www.skadate.com/ca/docs/introduction_configuration_languages#languages1',
                    'label' => 'Help'
                )
		)
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
			'href'	=> URL_ADMIN.'profile_field_list.php',
			'label'	=> 'All',
            'help_manual' => array(
                    'href' => 'http://www.skadate.com/ca/docs/introduction_configuration_profile_fields',
                    'label' => 'Help'
                )
		),

		'join' => array(
			'href'	=> URL_ADMIN.'profile_field_list.php?f_page=join',
			'label'	=> 'Join',
            'help_manual' => array(
                    'href' => 'http://www.skadate.com/ca/docs/introduction_configuration_profile_fields',
                    'label' => 'Help'
                )
		),

		'edit' => array(
			'href'	=> URL_ADMIN.'profile_field_list.php?f_page=edit',
			'label'	=> 'Edit',
            'help_manual' => array(
                    'href' => 'http://www.skadate.com/ca/docs/introduction_configuration_profile_fields',
                    'label' => 'Help'
                )
		),

		'view' => array(
			'href'	=> URL_ADMIN.'profile_field_list.php?f_page=view',
			'label'	=> 'View',
            'help_manual' => array(
                    'href' => 'http://www.skadate.com/ca/docs/introduction_configuration_profile_fields',
                    'label' => 'Help'
                )
		),

		'search' => array(
			'href'	=> URL_ADMIN.'profile_field_list.php?f_page=search',
			'label'	=> 'Search',
            'help_manual' => array(
                    'href' => 'http://www.skadate.com/ca/docs/introduction_configuration_profile_fields',
                    'label' => 'Help'
                )
		)

	),

	'prof_field' => array
	(
		'main' => array(
			'label'	=> 'Profile Fields'
		)
	),

	'navigation' => array
	(
		'nav_menu' => array(
			'href' => URL_ADMIN.'nav_menu.php',
			'label' => 'Menu Items'
		),

		'nav_doc' => array(
			'href' => URL_ADMIN.'nav_doc.php',
			'label' => 'Documents'
		),

		'nav_settings' => array(
			'href' => URL_ADMIN.'nav_settings.php',
			'label' => 'Additional Settings'
		)
	),

	'ads' => array
	(
		'binding' => array(
			'href' => URL_ADMIN.'ads_binding.php',
			'label' => 'Binding'
		),

		'template_set' => array(
			'href' => URL_ADMIN.'ads_template_set.php',
			'label' =>	'Template Sets'
		),

		'template' => array(
			'href' => URL_ADMIN.'ads_template.php',
			'label' =>	'Templates'
		)

	),

	'affiliate' => array
	(
		'affiliate' => array(
			'href'	=>	URL_ADMIN.'affiliate_list.php',
			'label'	=>	'Affiliates'
		),

		'settings' => array(
			'href'	=>	URL_ADMIN.'affiliate_configure.php',
			'label'	=>	'Settings'
		)
	),

	'payment_providers' => array
	(
		'payment_providers' => array(
			'href'	=>	URL_ADMIN.'payment_providers.php',
			'label'	=>	'Payment Gateways'
		),
        'sms_billing' => array(
            'href'  =>  URL_ADMIN.'sms_billing.php',
            'label' =>  'SMS Billing'
        )

	),

	'profiles'	=> array
	(
		'statistic'	=> array(
			'href'	=> URL_ADMIN.'profiles.php',
			'label'	=> 'Statistics',
		)

	),

	'finance'	=> array
	(
		'finance'	=> array(
			'href'	=> URL_ADMIN.'transaction_list.php',
			'label'	=> 'Finance',
		),
        'point_payments' => array(
            'href'  =>  URL_ADMIN. 'point_payments.php',
            'label' =>  'Credits Payments'
        ),
        'sms_transactions' => array(
            'href'  =>  URL_ADMIN.'sms_transactions.php',
            'label' =>  'SMS Payments'
        )
	),

	'mass_mailing'	=> array
	(
		'mass_mailing'	=> array
		(
			'href'	=> URL_ADMIN.'mass_mailing.php',
			'label'	=> 'Mass Mailing',
            ),
            'mailbox' => array
            (
                'href' => URL_ADMIN . 'site.php?unit=mailbox',
                'label' => 'Mail settings'
		)
	),

	'index_page' => array
	(
		'index_page' => array(
			'href' => URL_ADMIN.'index_page.php',
			'label' => 'Index Page Builder'
		),
        'home_page' => array(
            'href' => URL_ADMIN.'home_page.php',
            'label' => 'Home Page Builder'
        ),
		'templates' => array(
			'href' => URL_ADMIN.'templates.php',
			'label' => 'Choose Template'
		),
        'slideshow' => array(
            'href' => URL_ADMIN.'slideshow.php',
            'label' => 'Index Page Slideshow'
        )
	),

	'caching'	=> array(
            'caching' => array(
                'href' => URL_ADMIN.'caching.php',
                'label'	=> 'Site Caching'
            ),
            'cloudflair' => array(
                'href' => URL_ADMIN.'caching.php?unit=cloudflair',
                'label'	=> 'CloudFlare'
            )
	),

	'events'	=> array
	(
		'list'	=> array(
			'href'	=> URL_ADMIN.'events.php',
			'label'	=> 'Events List'
		)
	),

	'polls'	=> array
	(
		'polls'	=> array(
			'href'	=> URL_ADMIN.'polls.php',
			'label'	=> 'Polls'
		),
	),

	'gifts'	=> array
	(
		'gifts'	=> array(
			'href'	=> URL_ADMIN.'virtual_gifts.php',
			'label'	=> 'Gift Templates'
		),
		'categories' => array(
            'href' => URL_ADMIN.'virtual_gift_categories.php',
            'label' => 'Categories'
		)
	),

	'membership_types_list'	=> array
	(
		'membership_types_list'	=> array(
			'href'	=> URL_ADMIN.'membership_type_list.php',
			'label'	=> 'Membership Types'
		),
		'service_list' => array(
            'href' => URL_ADMIN.'service_list.php',
            'label' => 'Membership Services'
        )
	),

	'service_list'	=> array
	(
		'service_list'	=> array(
			'href'	=> URL_ADMIN.'service_list.php',
			'label'	=> 'Services'
		)
	),

	'features'	=> array
	(
		'features'	=> array(
			'href'	=> URL_ADMIN.'features.php',
			'label'	=> 'Features'
		),

		'dating'    => array(
            'href'  => URL_ADMIN.'features.php?dating',
            'label' => 'Dating Features'
        )
	),

	'config_media'	=> array
	(
		'config_media'	=> array(
			'href'	=> URL_ADMIN.'config_media.php',
			'label'	=> 'Video'
		)
	),

	'chat'	=> array
	(
		'configs'	=> array(
			'href'		=> URL_ADMIN.'chat.php',
			'label'		=> 'Chat Configuration'
		)
	),

	'config_reg_invite'	=> array
	(
		'config_reg_invite'	=> array
		(
			'href'	=> URL_ADMIN.'config_reg_invite.php',
			'label'	=> 'Configuration',
		),
		'send_reg_invite'	=> array
		(
			'href'	=> URL_ADMIN.'send_reg_invite.php',
			'label'	=> 'Send invitation'
		)
	),

	'friends_network'	=> array
	(
		'friends_network'	=> array
		(
			'href'	=> URL_ADMIN.'friends_network.php',
			'label'	=> 'Friends Network',
		)
	),

	'config_photo'	=> array
	(
		'config_photo'	=> array
		(
			'href'	=> URL_ADMIN.'config_photo.php',
			'label'	=> 'Photo',
		),

		'photo_verification'    => array
        (
            'href'  => URL_ADMIN.'photo_verification_settings.php',
            'label' => 'Verification',
        )
	),

	'reports'	=> array
	(
		'reports_list'	=> array
		(
			'href'	=> URL_ADMIN.'reports.php',
			'label'	=> 'Reports List',
		)
	),
	'site'	=> array
	(
		'official'	=> array
		(
			'href'	=> URL_ADMIN.'site.php?unit=official',
			'label'	=> 'Site Settings',
		),

		'admin'	=> array
		(
			'href'	=> URL_ADMIN.'site.php?unit=admin',
			'label'	=> 'Admin Settings'
		),
                'access' => array
		(
                    'href' => URL_ADMIN.'site.php?unit=access',
                    'label' => 'Site access'
		),
		'additional' => array
		(
			'href'	=> URL_ADMIN.'site.php?unit=additional',
			'label'	=> 'Additional Settings'
		),
		'automode'	=> array
		(
			'href'	=> URL_ADMIN.'site.php?unit=automode',
			'label'	=> 'Auto Mode'
		)
	),
	'services'	=> array
	(
		'autocrop'	=> array
		(
			'href'	=> URL_ADMIN.'services.php?service=autocrop',
			'label'	=> 'FaceJuggle'
		),
		'chat_services'	=> array
		(
			'href'	=> URL_ADMIN.'chat_services.php',
			'label'	=> 'Chats'
		),
		'mobile'	=> array
		(
			'href'	=> URL_ADMIN.'mobile.php',
			'label'	=> 'SkaDate ME'
		),

		'123flashchat'	=> array
		(
			'href'	=> URL_ADMIN.'services.php?service=123flashchat',
			'label'	=> '123 Flash Chat'
		),
		/*'facebook'	=> array
		(
			'href'	=> URL_ADMIN.'services.php?service=facebook',
			'label'	=> 'Facebook'
		),*/
	),
	'forum'	=> array
	(
		'management'	=> array
		(
			'href'	=> URL_ADMIN.'forum_management.php',
			'label'	=> 'Manage'
		),
		'settings'	=> array
		(
			'href'	=> URL_ADMIN.'forum_settings.php',
			'label'	=> 'Settings'
		)
	),
	'classifieds'	=> array
	(
		'classifieds_settings'	=> array
		(
			'href'	=> URL_ADMIN.'classifieds.php',
			'label'	=> 'General Settings'
		),
		'classifieds_wanted'	=> array
		(
			'href'	=> URL_ADMIN.'classifieds_wanted.php',
			'label'	=> 'Wanted Settings'
		),
		'classifieds_offer'	=> array
		(
			'href'	=> URL_ADMIN.'classifieds_offer.php',
			'label'	=> 'Offer Settings'
		)

	),
	'groups'	=> array
	(
		'groups'	=> array
		(
			'href'	=> URL_ADMIN.'groups.php',
			'label'	=> 'Groups List'
            ),
            'group_settings' => array
            (
                'href' => URL_ADMIN . 'site.php?unit=groups',
                'label' => 'Group Settings'
		)
	),

	'link_dir'	=> array
	(
		'link_dir_catgs'	=> array
		(
			'href'	=> URL_ADMIN.'link_dir_categories.php',
			'label'	=> 'Categories'
		),
		'link_dir_links'	=> array
		(
			'href'	=> URL_ADMIN.'link_dir_links.php',
			'label'	=> 'Links'
		)
	),
	'profile_message' => array
	(
		'messages'	=> array
		(
			'href'	=> URL_ADMIN.'profile_message.php',
			'label'	=> 'Messages'
		),
		'keywords'	=> array
		(
			'href'	=> URL_ADMIN.'profile_message_keyword.php',
			'label'	=> 'Spam Filter'
		)
	),
	'blogs'	=> array
	(
		'blogs_conf'	=> array
		(
			'href'	=> URL_ADMIN.'blogs_conf.php',
			'label'	=> 'Blogs Configs'
		)
	),
	'newsfeed'	=> array
	(
		'newsfeed_conf'	=> array
		(
			'href'	=> URL_ADMIN.'newsfeed_conf.php',
			'label'	=> 'Newsfeed Settings'
		)
	),
	'referrals'	=> array
	(
		'referral_list'	=> array
		(
			'href'	=> URL_ADMIN.'referral_list.php',
			'label'	=> 'Referrer List'
		),
		'settings'   => array
        (
            'href'  => URL_ADMIN.'referral_settings.php',
            'label' => 'Settings'
        ),
	),
	'mail_scheduler' => array
	(
		'scheduler' => array
		(
			'href' => URL_ADMIN.'mail_scheduler.php',
			'label' => 'Scheduler'
		),
		'mail_template'	=> array
		(
			'href'	=> URL_ADMIN.'mail_scheduler_tpl.php',
			'label'	=> 'Mail templates'
		),
		'mail_setting'	=> array
		(
			'href'	=> URL_ADMIN.'mail_scheduler_setting.php',
			'label'	=> 'Settings'
		)
	),
	'administration' => array
	(
		'admin_list' => array
		(
			'href'  => URL_ADMIN.'administration.php',
			'label' => 'Administrators'
		),
	),
	'sitemap' => array(
		'sitemap' => array(
			'href' => URL_ADMIN.'sitemap.php',
			'label' => 'SiteMap'
		),
		'seo' => array
		(
                    'href' => URL_ADMIN.'site.php?unit=seo',
                    'label' => 'SEO/Tracking'
		)
	),
    'badwords' => array
    (
        'badwords' => array(
            'href'	=>	URL_ADMIN. 'badwords.php',
            'label'	=>	'Badwords'
		)
    ),
    'share' => array
    (
        'facebook_share' => array(
            'href' => URL_ADMIN.'share.php?share=facebook_share',
            'label' => 'Facebook'
        ),
        'twitter_share' => array(
            'href' => URL_ADMIN.'share.php?share=twitter_share',
            'label' => 'Twitter'
        ),
        'google_share' => array(
            'href' => URL_ADMIN.'share.php?share=google_share',
            'label' => 'g +1' )

    ),
    'user_points' => array
    (
        'point_packages' => array(
            'href'  =>  URL_ADMIN. 'point_packages.php',
            'label' =>  'Packages'
        ),
        'spend_credits' => array(
            'href'  =>  URL_ADMIN. 'spend_credits.php',
            'label' =>  'Spending Credits'
        ),
        'earn_credits' => array(
            'href'  =>  URL_ADMIN. 'earn_credits.php',
            'label' =>  'Earning Credits'
        ),
    ),

    'rest_username'  => array(
        'rest_username'   =>  array(
            'href'  => URL_ADMIN.'rest_username.php',
            'label' => 'Restricted Usernames'
        )
    ),

    'fbconnect'  => array(
        'fbconnect_fields'   =>  array(
            'href'  => URL_ADMIN.'fbconnect_fields.php',
            'label' => 'Profile fields'
        ),

        'fbconnect_settings'   =>  array(
            'href'  => URL_ADMIN.'fbconnect_settings.php',
            'label' => 'Settings'
        )
    ),

    'security' => array(
        'security' => array(
            'href' => URL_ADMIN.'security.php',
            'label' => 'Site security'
        ),
        'status' => array
        (
            'href' => URL_ADMIN . 'security.php?unit=status',
            'label' => 'SFS IP blocking'
        ),
        'country' => array
        (
            'href' => URL_ADMIN . 'security.php?unit=country',
            'label' => 'Country blocking'
        ),
        'blocked_ip' => array
        (
            'href' => URL_ADMIN.'security.php?unit=blocked_ip',
            'label' => 'Blocked IPs list'
        )
    ),

    'google'  => array(
        'google_settings'   =>  array(
            'href'  => URL_ADMIN.'google.php',
            'label' => 'Google Application'
        )
    ),

    'splash_screen'  => array(
        'splash_screen'   =>  array(
            'href'  => URL_ADMIN.'site.php?unit=splash_screen',
            'label' => 'Splash Screen'
        )
    ),

);

if ( app_Features::isAvailable(65) )
{
    $tabs_menu_items['membership_types_list']['coupon_codes'] = array(
        'href' => URL_ADMIN . 'coupon_codes.php',
        'label' => 'Coupon Codes'
    );
}

if ( app_Cometchat::isActive() )
{
    $tabs_menu_items['services']['cometchat'] = array(
        'href'  => URL_ADMIN.'cometchat.php',
        'label' => 'Comet Chat'
    );
}

if (!isAdminAuthed(false) || $_SESSION['administration']['superadmin'] !== true) {
	unset($tabs_menu_items["site"]["admin"]);
}

$tabs_menu='';

if( $tabs_menu_items[$file_key] )
{
	foreach( $tabs_menu_items[$file_key] as $key => $item )
	{
		$tabs_menu.= '<div class="';

		if( $active_tab != $key && !@$item['active'] )
			$tabs_menu .= 'tab';
		else
			$tabs_menu .= 'active_tab';

		$tabs_menu.= '">
		<a href="'.$item['href'].'">'.$item['label'].'</a>';
		if( $active_tab == $key )
			$tabs_menu .= '<div class="active_tab_no_border"></div>';

		$tabs_menu .= '</div>';
	}
}

//trick: 8aa
if(!empty($_GET['fu']))
{
	redirect(URL_ADMIN."profile_list.php?search_username={$_GET['fu']}");
}
//----

$tabs_menu.= '<div id="empty_tab"></div>';
global $frontend;
$frontend->assign( 'sidebar_menu', $sidebar_menu );

$frontend->assign( 'tabs_menu', $tabs_menu );

require_once 'inc.help_manual.php';

}
