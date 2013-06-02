<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Core
 *
 * Sets the default route to "welcome"
 */

$unRegExp = SK_ProfileFields::get('username')->regexp;
$reg = trim($unRegExp, '^$/');
$regEnd = trim($reg, '^/');

$config['_default'] = 'index';
$config['members'] = 'profile_list/show/featured/1';
$config['members/(online|new|matches|featured|bookmarks)'] = 'profile_list/show/$1/1';
$config['members/(online|new|matches|featured|bookmarks)/([0-9]+)'] = 'profile_list/show/$1/$2';
$config['members/search_list/([0-9]+)'] = 'profile_list/search_list/$1/1';
$config['members/search_list/([0-9]+)/([0-9]+)'] = 'profile_list/search_list/$1/$2';
$config['mailbox'] = 'mailbox/conversations/inbox';
$config['mailbox/(inbox|sent)'] = 'mailbox/conversations/$1/1';
$config['mailbox/(inbox|sent)/([0-9]+)'] = 'mailbox/conversations/$1/$2';
$config['mailbox/conv'] = 'mailbox/conversations/inbox';
$config['mailbox/conv/([0-9]+)'] = 'mailbox/list_messages/$1';
$config['mailbox/conv/([0-9]+)/unread'] = 'mailbox/unread/$1';
$config['mailbox/conv/([0-9]+)/delete'] = 'mailbox/delete_confirm/$1';
$config['mailbox/conv/([0-9]+)/delconfirmed'] = 'mailbox/delete/$1';
$config['mailbox/new'] = 'mailbox/compose';
$config['mailbox/new/('.$regEnd.')'] = 'mailbox/compose/$1';
$config['profile'] = 'profile_view/view/__self__';
$config['profile/bookmark/('.$regEnd.')'] = 'profile_view/bookmark/$1';
$config['profile/unbookmark/('.$regEnd.')'] = 'profile_view/unbookmark/$1';
$config['profile/('.$regEnd.')'] = 'profile_view/view/$1';
$config['profile/('.$reg.')/photo/([0-9]+)'] = 'profile_photo/view/$1/$2';
$config['profile/('.$reg.')/photos'] = 'profile_photo/view_all/$1';
$config['home'] = 'memberhome/index';
$config['im/('.$regEnd.')'] = 'im/index/$1';
$config['imiframe/('.$reg.')/([0-9]+)'] = 'im/iframe/$1/$2';
$config['about'] = 'custom_doc/about';
$config['privacy'] = 'custom_doc/privacy';
$config['terms'] = 'custom_doc/terms';
$config['suspended'] = 'custom_doc/suspended';
$config['review'] = 'custom_doc/review';
$config['emailverify'] = 'emailverify/emailverify';
$config['emailverify/(\w+)'] = 'emailverify/emailverify/$1';
$config['profile-suspended'] = 'custom_doc/profileSuspended';