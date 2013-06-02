<?php

// directory where compiled css, images and javascript files are located
define('DIR_EXTERNAL_C', DIR_SITE_ROOT.'external_c'.DIRECTORY_SEPARATOR);
define('URL_EXTERNAL_C', SITE_URL.'external_c/');

define('URL_STATIC', SITE_URL.'static/');

define('URL_CAPTCHA', SITE_URL.'captcha/');
define('DIR_CAPTCHA', DIR_SITE_ROOT.'captcha'.DIRECTORY_SEPARATOR);

// directory where components compiled php resources are located
define('DIR_INTERNAL_C', DIR_SITE_ROOT.'internal_c'.DIRECTORY_SEPARATOR);
define('DIR_COMPONENTS_C', DIR_INTERNAL_C.'components'.DIRECTORY_SEPARATOR);
define('DIR_FORMS_C', DIR_INTERNAL_C.'forms'.DIRECTORY_SEPARATOR);
define('DIR_LANGS_C', DIR_INTERNAL_C.'lang'.DIRECTORY_SEPARATOR);

// components cache directory
define('DIR_SMARTY_CACHE', DIR_INTERNAL_C.'cache'.DIRECTORY_SEPARATOR);

// SkaDate APIs folder
define('DIR_API', DIR_INTERNALS.'API'.DIRECTORY_SEPARATOR);

// Applications classes folder
define('DIR_APPS', DIR_INTERNALS.'Apps'.DIRECTORY_SEPARATOR);

// Component folders
define('DIR_COMPONENTS', DIR_SITE_ROOT.'components'.DIRECTORY_SEPARATOR);
define('DIR_HTTPDOCS', DIR_INTERNALS.'httpDocs'.DIRECTORY_SEPARATOR);
define('URL_COMPONENTS', SITE_URL.'components/');

// Forms & Fields folders
define('DIR_FORMS', DIR_INTERNALS.'Forms'.DIRECTORY_SEPARATOR);
define('DIR_FORM_FIELDS', DIR_FORMS.'fields'.DIRECTORY_SEPARATOR);
define('DIR_FORM_FIELD_TYPES', DIR_FORM_FIELDS.'types'.DIRECTORY_SEPARATOR);
define('DIR_FORM_ACTIONS', DIR_FORMS.'actions'.DIRECTORY_SEPARATOR);

define('DIR_CHECKOUT', DIR_SITE_ROOT.'checkout'.DIRECTORY_SEPARATOR);
define('DIR_AFFILIATE', DIR_SITE_ROOT.'affiliate'.DIRECTORY_SEPARATOR);
define('DIR_AFFILIATE_INC', DIR_AFFILIATE.'inc'.DIRECTORY_SEPARATOR);

// Core files
define('DIR_CORE', DIR_INTERNALS.'Core'.DIRECTORY_SEPARATOR);

define('DIR_LAYOUT', DIR_SITE_ROOT.'layout'.DIRECTORY_SEPARATOR);
define('DIR_SMARTY', DIR_INTERNALS.'Smarty'.DIRECTORY_SEPARATOR);

// Backward compatibility API classes and functions directory
define('DIR_SK6_INC', DIR_INTERNALS.'SK6_inc'.DIRECTORY_SEPARATOR);

// Navigation modules
define('DIR_NAV_MODULES', DIR_INTERNALS.'nav_mods'.DIRECTORY_SEPARATOR);

//Admin folders
define('DIR_ADMIN', DIR_SITE_ROOT.'admin'.DIRECTORY_SEPARATOR);
define('DIR_ADMIN_INC', DIR_ADMIN.'inc'.DIRECTORY_SEPARATOR);

//Admin URLs
define('URL_ADMIN', SITE_URL.'admin/');
define('URL_ADMIN_CSS', URL_ADMIN.'css/');
define('URL_ADMIN_JS', URL_ADMIN.'js/');
define('URL_ADMIN_IMG', URL_ADMIN.'img/');

// Member Area
define('URL_MEMBER', SITE_URL.'member/');

// Images, photos & layout URL
define('URL_LAYOUT', SITE_URL.'layout/');
define('URL_LAYOUT_IMG', URL_LAYOUT.'img/');

define('URL_SMILE_IMG', SITE_URL.'static/smiles/');
define('URL_FLAG_IMG', SITE_URL.'static/flags/');

define('DIR_USERFILES', DIR_SITE_ROOT.'userfiles'.DIRECTORY_SEPARATOR);
define('URL_USERFILES', SITE_URL.'userfiles/');

define('DIR_TMP_USERFILES', DIR_USERFILES.'tmp'.DIRECTORY_SEPARATOR);
define('URL_TMP_USERFILES', URL_USERFILES.'tmp/');

define('URL_FLASH_MEDIA_PLAYER', SITE_URL.'flashplayer/');
define('URL_CHECKOUT', SITE_URL.'checkout/');
define('URL_SMS_PROVIDERS', URL_CHECKOUT.'SMS/');
define('URL_AFFILIATE', SITE_URL.'affiliate/');
define('URL_AFFIILATE_IMG', URL_AFFILIATE.'img/');
define('URL_AFFILIATE_CSS', URL_AFFILIATE.'css/');
define('URL_AFFILIATE_JS', URL_AFFILIATE.'js/');

define('URL_FIELD_RESPONDER', SITE_URL.'field_responder.php');

define('DIR_CONTACT_GRABBER', DIR_INTERNALS.'ContactGrabber'.DIRECTORY_SEPARATOR);

define('DIR_SERVICES', DIR_SITE_ROOT.'Services'.DIRECTORY_SEPARATOR);
define('DIR_SERVICE_AUTOCROP', DIR_SERVICES.'autocrop'.DIRECTORY_SEPARATOR);

//123 Chat folder
define('DIR_123CHAT', DIR_SERVICES.'123_chat'.DIRECTORY_SEPARATOR);
define('DIR_123WM', DIR_SERVICES.'123_wm'.DIRECTORY_SEPARATOR.'client'.DIRECTORY_SEPARATOR);
//123 Chat url
define('URL_123CHAT', SITE_URL.'Services/123_chat/');
define('URL_123WM', SITE_URL.'Services/123_wm/');

//RSS Feed Url
define('SKALFA_MAIN_FEED', 'http://www.skadate.com/blog/feed');
define('SKADATE_SPOTLIGHT_SIZE', 'http://www.skadate.com/spotlightsize.inc');
define('SKADATE_SPOTLIGHT_FEED', 'http://www.skadate.com/spotlight.php');

define('MAX_SQL_BIGINT', '0xffffffffffffffff');
define('MAIL_SEND_LIMIT', 200);
define('SEARCH_RESULT_MAX', 500);
define('SEARCH_RESULT_EMPTY_TIMEOUT', 86400);

define('TMP_USERFILES_REMOVE_TIMEOUT', 86400);
define('TMP_USERFILES_DELETE_LIMIT', 100);

// Max quality of uploaded images
define('UPLOAD_IMG_QUALITY', 95);

define('FEATURE_BLOG', 'blog');
define('FEATURE_EVENT', 'event');
define('FEATURE_PROFILE', 'profile');
define('FEATURE_PHOTO', 'photo');
define('FEATURE_MUSIC', 'music');
define('FEATURE_FORUM', 'forum');
define('FEATURE_VIDEO', 'video');
define('FEATURE_COMMENT', 'comment');
define('FEATURE_MAILBOX', 'mailbox');
define('FEATURE_NEWS', 'news');
define('FEATURE_CLASSIFIEDS', 'classifieds');
define('FEATURE_GROUP', 'group');
define('FEATURE_SHOUTBOX', 'shoutbox');
define('FEATURE_USER_ACTIVITY', 'user_activity');
define('FEATURE_CHAT', 'chat');
define('FEATURE_NEWSFEED', 'newsfeed');
define('FEATURE_TAGS', 'tag');

define('DIR_PHPMAILER', DIR_INTERNALS . 'phpmailer' . DIRECTORY_SEPARATOR);

define('ENTITY_TYPE_BLOG_POST_ADD', 'blog_post_add');
define('ENTITY_TYPE_NEWS_POST_ADD', 'news_post_add');
define('ENTITY_TYPE_FRIEND_ADD',  'friend_add');
define('ENTITY_TYPE_PROFILE_AVATAR_CHANGE', 'profile_avatar_change');
define('ENTITY_TYPE_PROFILE_EDIT', 'profile_edit');
define('ENTITY_TYPE_PROFILE_JOIN', 'profile_join');
define('ENTITY_TYPE_PROFILE_COMMENT', 'profile_comment');
define('ENTITY_TYPE_USER_COMMENT', 'user_comment');
define('ENTITY_TYPE_POST_CLASSIFIEDS_ITEM', 'post_classifieds_item');
define('ENTITY_TYPE_EVENT_ATTEND', 'event_attend');
define('ENTITY_TYPE_EVENT_ADD', 'event_add');
define('ENTITY_TYPE_GROUP_ADD', 'group_add');
define('ENTITY_TYPE_GROUP_JOIN', 'group_join');
define('ENTITY_TYPE_PHOTO_UPLOAD', 'photo_upload');
define('ENTITY_TYPE_MEDIA_UPLOAD', 'media_upload');
define('ENTITY_TYPE_MUSIC_UPLOAD', 'music_upload');
define('ENTITY_TYPE_FORUM_ADD_TOPIC', 'forum_add_topic');

define('DIR_UTILS', DIR_SITE_ROOT.'utils'.DIRECTORY_SEPARATOR);