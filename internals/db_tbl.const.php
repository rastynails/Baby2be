<?php

define('TBL_CONFIG', DB_TBL_PREFIX.'config');
define('TBL_CONFIG_SECTION', DB_TBL_PREFIX.'config_section');
define('TBL_CONFIG_VALUE', DB_TBL_PREFIX.'config_value');

define('TBL_LANG', DB_TBL_PREFIX.'lang');
define('TBL_LANG_KEY', DB_TBL_PREFIX.'lang_key');
define('TBL_LANG_SECTION', DB_TBL_PREFIX.'lang_section');
define('TBL_LANG_VALUE', DB_TBL_PREFIX.'lang_value');
define('TBL_LANG_TO_COUNTRY', DB_TBL_PREFIX.'lang_to_country');

define('TBL_PROFILE_BLOCK_LIST', DB_TBL_PREFIX . 'profile_block_list');
define('TBL_PROFILE_BOOKMARK_LIST', DB_TBL_PREFIX . 'profile_bookmark_list');
define('TBL_PROFILE_FRIEND_LIST', DB_TBL_PREFIX . 'profile_friend_list');

define( 'TBL_COOKIES_LOGIN', DB_TBL_PREFIX.'cookies_login');

define('TBL_PROF_FIELD', DB_TBL_PREFIX . 'profile_field');
define('TBL_PROFILE_FIELD', DB_TBL_PREFIX . 'profile_field');
define('TBL_PROFILE_FIELD_REVIEW', DB_TBL_PREFIX . 'profile_field_review');
define('TBL_PROF_FIELD_VALUE', DB_TBL_PREFIX . 'profile_field_values');
define('TBL_PROF_FIELD_MATCH_LINK', DB_TBL_PREFIX . 'link_profile_field_match');
define('TBL_PROF_FIELD_PAGE_LINK', DB_TBL_PREFIX . 'link_profile_field_page');
define('TBL_PROF_FIELD_SECTION', DB_TBL_PREFIX . 'profile_field_section');
define('TBL_PROF_FIELD_PAGE', DB_TBL_PREFIX . 'profile_field_page');

define('TBL_PROFILE_ONLINE', DB_TBL_PREFIX . 'profile_online');

define('TBL_USER_STATUS', DB_TBL_PREFIX . 'user_status');


define('TBL_PROFILE_FIELD_DEPENDENCE', DB_TBL_PREFIX . 'profile_field_dependence');
define('TBL_PROFILE_EXTEND', DB_TBL_PREFIX . 'profile_extended');

define('TBL_LOCATION_COUNTRY', DB_TBL_PREFIX . 'location_country');
define('TBL_LOCATION_STATE', DB_TBL_PREFIX . 'location_state');
define('TBL_LOCATION_CITY', DB_TBL_PREFIX . 'location_city');
define('TBL_LOCATION_ZIP', DB_TBL_PREFIX . 'location_zip');
define('TBL_LOCATION_REGION', DB_TBL_PREFIX . 'location_region');
define('TBL_LOCATION_COUNTRY_IP', DB_TBL_PREFIX . 'location_country_ip');



define('TBL_FIN_PAYMENT_PROVIDERS', DB_TBL_PREFIX . 'fin_payment_provider');
define('TBL_FIN_PAYMENT_PROVIDER_FIELD', DB_TBL_PREFIX . 'fin_payment_provider_field');
define('TBL_FIN_PAYMENT_PROVIDER_FIELD_POSSIBLE_VALUE', DB_TBL_PREFIX . 'fin_payment_provider_field_possible_value');
define('TBL_FIN_PAYMENT_PROVIDER_PLAN', DB_TBL_PREFIX . 'link_payment_provider_membership_plan');
define('TBL_FIN_SALE_INFO', DB_TBL_PREFIX . 'fin_sale_info');
define('TBL_FIN_SALE', DB_TBL_PREFIX . 'fin_sale');
define('TBL_FIN_REFUND', DB_TBL_PREFIX . 'fin_refund');

define('TBL_SMS_PAYMENT', DB_TBL_PREFIX . 'sms_payment');
define('TBL_SMS_SERVICE', DB_TBL_PREFIX . 'sms_service');
define('TBL_SMS_SERVICE_FIELD', DB_TBL_PREFIX . 'sms_service_field');
define('TBL_SMS_PROVIDER', DB_TBL_PREFIX . 'sms_provider');
define('TBL_SMS_PROVIDER_FIELD', DB_TBL_PREFIX . 'sms_provider_field');

define('TBL_ADS_TEMPLATE', DB_TBL_PREFIX . 'ads_template');
define('TBL_ADS_TEMPLATE_SET', DB_TBL_PREFIX . 'ads_template_set');
define('TBL_ADS_POSITION', DB_TBL_PREFIX . 'ads_position');
define('TBL_LINK_ADS_PAGE_POSITION', DB_TBL_PREFIX . 'link_ads_page_position');
define('TBL_LINK_ADS_BINDING_TPL', DB_TBL_PREFIX . 'link_ads_binding_template');
define('TBL_LINK_ADS_BINDING_COUNTRY', DB_TBL_PREFIX . 'link_ads_binding_country');
define('TBL_LINK_ADS_BINDING_TPL_SET', DB_TBL_PREFIX . 'link_ads_binding_template_set');
define('TBL_GAME', DB_TBL_PREFIX . 'game');
define('TBL_NOVEL_GAME', DB_TBL_PREFIX . 'novel_game');
define('TBL_NOVEL_GAME_RESULTS', DB_TBL_PREFIX . 'novel_game_results');




define( 'TBL_PROFILE_EMAIL_VERIFY_CODE', DB_TBL_PREFIX.'profile_email_verification_code' );

define( 'TBL_SEARCH_CRITERION', DB_TBL_PREFIX.'search_criterion' );



define('TBL_MEMBERSHIP', DB_TBL_PREFIX . 'membership');
define('TBL_MEMBERSHIP_CLAIM', DB_TBL_PREFIX . 'membership_claim');
define('TBL_MEMBERSHIP_TYPE', DB_TBL_PREFIX . 'membership_type');
define('TBL_MEMBERSHIP_SERVICE', DB_TBL_PREFIX . 'membership_service');
define('TBL_MEMBERSHIP_SERVICE_TRACK', DB_TBL_PREFIX.'membership_service_track');
define('TBL_MEMBERSHIP_TYPE_PLAN', DB_TBL_PREFIX . 'membership_type_plan');
define('TBL_MEMBERSHIP_LINK_MEMBERSHIP_TYPE_SERVICE', DB_TBL_PREFIX . 'link_membership_type_service');
define('TBL_MEMBERSHIP_LINK_SERVICE_LIMIT', DB_TBL_PREFIX.'link_membership_service_limit');
define('TBL_COUPON_CODES', DB_TBL_PREFIX.'coupon_codes');
define('TBL_COUPON_TRACK', DB_TBL_PREFIX.'coupon_track');
define('TBL_LINK_MENU_ITEM_MEMBERSHIP', DB_TBL_PREFIX . 'link_menu_item_membership_type');
define('TBL_AFFILIATE', DB_TBL_PREFIX . 'affiliate' );
define('TBL_AFFILIATE_BANNER', DB_TBL_PREFIX . 'affiliate_banner');
define('TBL_AFFILIATE_PLAN', DB_TBL_PREFIX . 'affiliate_plan' );
define('TBL_AFFILIATE_REGISTRATION', DB_TBL_PREFIX . 'affiliate_registration' );
define('TBL_AFFILIATE_SUBSCRIPTION', DB_TBL_PREFIX . 'affiliate_subscription' );
define('TBL_AFFILIATE_TRAFFIC', DB_TBL_PREFIX . 'affiliate_traffic' );
define('TBL_AFFILIATE_VISIT', DB_TBL_PREFIX . 'affiliate_visit' );
define('TBL_AFFILIATE_SITE', DB_TBL_PREFIX . 'affiliate_site' );
define('TBL_AFFILIATE_PAYMENT', DB_TBL_PREFIX . 'affiliate_payment' );
define('TBL_AFFILIATE_EMAIL_VERIFY_CODE', DB_TBL_PREFIX . 'affiliate_email_verification_code' );
define('TBL_SCHEDULER', DB_TBL_PREFIX . 'scheduler');
define('TBL_SCHEDULER_STATEMENT', DB_TBL_PREFIX . 'scheduler_statement');
define('TBL_SCHEDULER_FIELD', DB_TBL_PREFIX . 'scheduler_field');
define('TBL_LINK_SCHEDULER_STATEMENT_FIELD', DB_TBL_PREFIX . 'link_scheduler_statement_field');
define('TBL_SCHEDULER_MAIL_TPL', DB_TBL_PREFIX . 'scheduler_mail_tpl');

define('TBL_LINK_SCHEDULER_STATEMENT_MAIL', DB_TBL_PREFIX . 'link_scheduler_statement_mail');

define('TBL_FEATURE', DB_TBL_PREFIX . 'feature');

define('TBL_DOCUMENT', DB_TBL_PREFIX . 'document');
define('TBL_PROFILE', DB_TBL_PREFIX.'profile');
define('TBL_PROFILE_PHOTO', DB_TBL_PREFIX.'profile_photo');
define('TBL_PROFILE_MUSIC', DB_TBL_PREFIX.'profile_music');
define('TBL_PROFILE_MUSIC_VIEW', DB_TBL_PREFIX.'profile_music_view' );
define('TBL_PROFILE_VIDEO', DB_TBL_PREFIX.'profile_video');
define('TBL_PROFILE_VIDEO_FRAME', DB_TBL_PREFIX.'profile_video_frame' );
define('TBL_PROFILE_MUSIC_FRAME', DB_TBL_PREFIX.'profile_music_frame' );
define('TBL_PROFILE_VIDEO_RATE', DB_TBL_PREFIX.'profile_video_rate' );
define('TBL_PROFILE_VIDEO_VIEW', DB_TBL_PREFIX.'profile_video_view' );
define('TBL_TMP_VIDEOFILE', DB_TBL_PREFIX.'tmp_videofile' );
define('TBL_VIDEO_CATEGORY', DB_TBL_PREFIX.'video_category');
define('TBL_FORUM_GROUP', DB_TBL_PREFIX.'forum_group' );
define('TBL_FORUM', DB_TBL_PREFIX.'forum' );
define('TBL_FORUM_TOPIC', DB_TBL_PREFIX.'forum_topic' );
define('TBL_FORUM_POST', DB_TBL_PREFIX.'forum_post' );
define('TBL_FORUM_PROFILE_NOTIFY', DB_TBL_PREFIX.'forum_profile_notify' );
define('TBL_FORUM_BANNED_PROFILE', DB_TBL_PREFIX.'forum_banned_profile_list' );
define('TBL_MAILBOX_CONVERSATION', DB_TBL_PREFIX.'mailbox_conversation');
define('TBL_MAILBOX_MESSAGE', DB_TBL_PREFIX.'mailbox_message');
define('TBL_MAILBOX_SCAM_KEYWORD', DB_TBL_PREFIX.'mailbox_scam_keyword');
define('TBL_MAIL_TEMPLATE', DB_TBL_PREFIX.'mail_template');
define('TBL_REPORT', DB_TBL_PREFIX.'report');
define('TBL_MENU_ITEM', DB_TBL_PREFIX . 'menu_item');
define('TBL_MENU', DB_TBL_PREFIX . 'menu');
define('TBL_REST_USERNAME', DB_TBL_PREFIX . 'restrict_username');


define('TBL_PROFILE_SENT_MATCHES', DB_TBL_PREFIX.'link_profile_sent_matches');

define('TBL_PROFILE_PREFERENCE', DB_TBL_PREFIX.'profile_preference');
define('TBL_PROFILE_PREFERENCE_DATA', DB_TBL_PREFIX.'profile_preference_data');


define('TBL_CHAT_ROOM', DB_TBL_PREFIX.'chat_room');
define('TBL_CHAT_USER', DB_TBL_PREFIX.'chat_user');
define('TBL_CHAT_MESSAGE', DB_TBL_PREFIX.'chat_message');

define('TBL_IM_SESSION', DB_TBL_PREFIX.'im_session');
define('TBL_IM_MESSAGE', DB_TBL_PREFIX.'im_message');

define('TBL_SHOUTBOX', DB_TBL_PREFIX.'shoutbox');

define('TBL_LINK_CHUPPO_GENDER', DB_TBL_PREFIX.'link_chuppo_gender');


define('TBL_ADMIN', DB_TBL_PREFIX.'admin');
define('TBL_LINK_ADMIN_DOCUMENT', DB_TBL_PREFIX.'link_admin_document');

define('TBL_MAIL', DB_TBL_PREFIX.'mail');


define('TBL_PROFILE_REGISTER_INVITE_CODE', DB_TBL_PREFIX.'profile_registration_invite_code');

define('TBL_REFERRAL', DB_TBL_PREFIX.'referral');
define('TBL_REFERRAL_RELATION', DB_TBL_PREFIX.'referral_relation');
define('TBL_REFERRAL_INVITE', DB_TBL_PREFIX.'referral_invite');
define('TBL_REFERRAL_PAYMENT', DB_TBL_PREFIX.'referral_payment');


define('TBL_TMP_PR_LIST', DB_TBL_PREFIX.'tmp_profile_list');
define('TBL_LINK_PR_LIST_PR', DB_TBL_PREFIX.'tmp_link_profile_list_profile');

define('TBL_BLOCK_IP', DB_TBL_PREFIX.'block_ip');

define('TBL_CAPTCHA', DB_TBL_PREFIX.'tmp_captcha');

define('TBL_TMP_USERFILE', DB_TBL_PREFIX.'tmp_userfile');

define( 'TBL_BLOG_POST', DB_TBL_PREFIX. 'blog_post' );

define( 'TBL_BLOG_POST_COMMENT', DB_TBL_PREFIX. 'blog_post_comment' );
define( 'TBL_EVENT_COMMENT', DB_TBL_PREFIX. 'event_comment' );
define( 'TBL_PHOTO_COMMENT', DB_TBL_PREFIX. 'photo_comment' );
define( 'TBL_VIDEO_COMMENT', DB_TBL_PREFIX. 'video_comment' );
define( 'TBL_MUSIC_COMMENT', DB_TBL_PREFIX. 'music_comment' );
define( 'TBL_PROFILE_COMMENT', DB_TBL_PREFIX. 'profile_comment' );
define( 'TBL_GROUP_COMMENT', DB_TBL_PREFIX. 'group_comment' );

define( 'TBL_BLOG_POST_TAG',DB_TBL_PREFIX. 'blog_post_tag' );
define( 'TBL_PHOTO_TAG',DB_TBL_PREFIX. 'photo_tag' );
define( 'TBL_VIDEO_TAG',DB_TBL_PREFIX. 'video_tag' );
define( 'TBL_PROFILE_TAG',DB_TBL_PREFIX. 'profile_tag' );
define( 'TBL_EVENT_TAG',DB_TBL_PREFIX. 'event_tag' );

define( 'TBL_BLOG_POST_RATE', DB_TBL_PREFIX. 'blog_post_rate' );
define( 'TBL_PHOTO_RATE', DB_TBL_PREFIX. 'photo_rate' );
define( 'TBL_VIDEO_RATE', DB_TBL_PREFIX. 'video_rate' );
define( 'TBL_MUSIC_RATE', DB_TBL_PREFIX. 'music_rate' );
define( 'TBL_PROFILE_RATE', DB_TBL_PREFIX. 'profile_rate' );

define( 'TBL_TAG', DB_TBL_PREFIX.'tag' );
define( 'TBL_EVENT', DB_TBL_PREFIX. 'event' );
define( 'TBL_EVENT_PROFILE', DB_TBL_PREFIX. 'event_profile' );
define( 'TBL_EVENT_SPEED_DATING_PROFILE', DB_TBL_PREFIX. 'event_speed_dating_profile' );
define( 'TBL_EVENT_SPEED_DATING', DB_TBL_PREFIX. 'event_speed_dating' );

define( 'TBL_PROFILE_COMPONENT', DB_TBL_PREFIX.'profile_component' );
define( 'TBL_PROFILE_VIEW_COMPONENT', DB_TBL_PREFIX.'profile_view_component' );
define( 'TBL_CUSTOM_HTML', DB_TBL_PREFIX.'custom_html' );

define( 'TBL_USER_ACTIVITY', DB_TBL_PREFIX.'user_activity' );

define( 'TBL_PHOTO_VIEW', DB_TBL_PREFIX. 'profile_photo_view' );

define( 'TBL_TMP_PHOTO', DB_TBL_PREFIX. 'tmp_photo' );

define( 'TBL_ADMIN_NOTES', DB_TBL_PREFIX.'admin_notes');

define( 'TBL_PROFILE_VIEW_HISTORY', DB_TBL_PREFIX.'profile_view_history');

define( 'TBL_SITE_MODERATORS', DB_TBL_PREFIX.'site_moderators');

define( 'TBL_BADWORD', DB_TBL_PREFIX.'badword' );
define( 'TBL_SMILE', DB_TBL_PREFIX.'smile' );
define( 'TBL_THEME', DB_TBL_PREFIX . 'theme' );
define( 'TBL_PROFILE_CHUPPO_ID', DB_TBL_PREFIX.'chuppo' );
define( 'TBL_CHUPPO_IM_SESSION', DB_TBL_PREFIX.'chuppo_im_session' );
define( 'TBL_PROFILE_NOTE', DB_TBL_PREFIX.'profile_note' );

define( 'TBL_HOTLIST', DB_TBL_PREFIX . 'hotlist');

define( 'TBL_CLASSIFIEDS_ITEM', DB_TBL_PREFIX.'classifieds_item' );
define( 'TBL_BLOG_POST_IMAGE', DB_TBL_PREFIX.'blog_post_image' );
define( 'TBL_CLASSIFIEDS_GROUP', DB_TBL_PREFIX.'classifieds_group' );
define( 'TBL_CLASSIFIEDS_BID', DB_TBL_PREFIX .'classifieds_bid' );
define( 'TBL_CLASSIFIEDS_COMMENT', DB_TBL_PREFIX.'classifieds_comment' );
define( 'TBL_CLASSIFIEDS_FILE', DB_TBL_PREFIX.'classifieds_file' );

define( 'TBL_POLL', DB_TBL_PREFIX.'poll' );
define( 'TBL_POLL_ANSWER', DB_TBL_PREFIX.'poll_answer' );
define( 'TBL_PROFILE_POLL_ANSWER', DB_TBL_PREFIX.'profile_poll_answer' );

define( 'TBL_GROUP', DB_TBL_PREFIX.'group' );
define( 'TBL_GROUP_MEMBER', DB_TBL_PREFIX.'group_member' );
define( 'TBL_GROUP_MODERATOR', DB_TBL_PREFIX.'group_moderator' );
define( 'TBL_GROUP_JOIN_CLAIM', DB_TBL_PREFIX.'group_join_claim' );
define( 'TBL_GROUP_INVITATION', DB_TBL_PREFIX.'group_invitation' );

define( 'TBL_PHOTO_ALBUMS', DB_TBL_PREFIX.'photo_albums' );
define( 'TBL_PHOTO_ALBUM_ITEMS', DB_TBL_PREFIX.'photo_album_items' );

define( 'TBL_INVITE_RESET', DB_TBL_PREFIX.'invite_reset' );

define( 'TBL_PHOTO_AUTHENTICATE', DB_TBL_PREFIX.'photo_authenticate' );

define( 'TBL_VIRTUAL_GIFT_TPL', DB_TBL_PREFIX.'virtual_gift_template' );
define( 'TBL_VIRTUAL_GIFT_CATEGORY', DB_TBL_PREFIX.'virtual_gift_category' );
define( 'TBL_VIRTUAL_GIFT', DB_TBL_PREFIX.'virtual_gift' );

define( 'TBL_USER_POINT_PACKAGE', DB_TBL_PREFIX . 'user_point_package');
define( 'TBL_USER_POINT_PACKAGE_SALE', DB_TBL_PREFIX . 'user_point_package_sale');
define( 'TBL_USER_POINT_PROVIDER_PACKAGE', DB_TBL_PREFIX . 'user_point_provider_package');
define( 'TBL_USER_POINT_BALANCE', DB_TBL_PREFIX . 'user_point_balance');
define( 'TBL_USER_POINT_ACTION', DB_TBL_PREFIX . 'user_point_action');
define( 'TBL_USER_POINT_ACTION_LOG', DB_TBL_PREFIX . 'user_point_action_log');
define( 'TBL_USER_POINT_ACTION_TRACK', DB_TBL_PREFIX . 'user_point_action_track');

define( 'TBL_CACHE', DB_TBL_PREFIX . 'cache');
define( 'TBL_RSS_WIDGET', DB_TBL_PREFIX . 'rss_widget');
define( 'TBL_TEXT_FORMATTER_IMAGE', DB_TBL_PREFIX . 'text_formatter_image');
define( 'TBL_ATTACHMENT', DB_TBL_PREFIX . 'attachment');

define( 'TBL_RESTORE_PASSWORD_KEY', DB_TBL_PREFIX . 'restore_password_key');

define('TBL_NEWSFEED_ACTION', DB_TBL_PREFIX.'newsfeed_action');
define('TBL_NEWSFEED_ACTION_FEED', DB_TBL_PREFIX.'newsfeed_action_feed');
define('TBL_NEWSFEED_FOLLOW', DB_TBL_PREFIX.'newsfeed_follow');
define('TBL_NEWSFEED_LIKE', DB_TBL_PREFIX.'newsfeed_like');
define('TBL_NEWSFEED_COMMENT', DB_TBL_PREFIX.'newsfeed_comment');

define('TBL_SLIDESHOW_SLIDE', DB_TBL_PREFIX.'slideshow_slide');

define('TBL_SECURITY_COUNTRIES', DB_TBL_PREFIX . 'security_countries');
define('TBL_SECURITY_EMAIL_LIST', DB_TBL_PREFIX . 'security_email_list');
define('TBL_SECURITY_IP_LIST', DB_TBL_PREFIX . 'security_ip_list');