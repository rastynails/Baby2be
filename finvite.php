<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';
require DIR_SITE_ROOT . 'facebook'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'facebook.php';

//-----------

if(!empty($_GET['close']) && $_GET['close'] == 'y')
	exit('<html><body onload="window.close();"></body></html>');


$appId = SK_Config::section('fb_invite')->appId;
$secret = SK_Config::section('fb_invite')->secret;

$action = SITE_URL.'finvite.php?close=y';
$type = SK_Config::section('site')->Section('official')->site_name;
$actionText = SK_Language::text('txt.fb_invite_title');
$content = SK_Language::text('txt.fb_invite_txt');
$joinUrl = SK_Navigation::getDocument('join_profile')->url;
//-----------

$facebook = new Facebook(array(
  'appId'  => $appId,
  'secret' => $secret,
  'cookie' => true,
));

$uid = $facebook->getUser();
$loginUrl = false;

if (empty($uid))
{
    $loginUrl = $facebook->getLoginUrl();
}
?>


<html>
  <body>
    <div id="fb-root"></div>
    <?php if($loginUrl) echo "<script>top.location.href='{$loginUrl}';</script>";?>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script>
      FB.init({appId: '<?php echo $appId; ?>', xfbml: true});
    </script>

<div id="fb-selector-c">
<fb:serverFbml style="width: 755px;">
<script type="text/fbml">
<fb:fbml>
    <fb:request-form
	style="width: 755px;"
	action="<?php echo $action; ?>"
        method='POST'
        type='<?php echo $type; ?>'
        content='<?php echo $content ?>
            <fb:req-choice url="<?php echo $joinUrl ?>"
                label="Yes" />'
        <fb:multi-friend-selector style="width: 755px;"
            actiontext="<?php echo $actionText ?>">
    </fb:request-form>
</fb:fbml>
</script>
</fb:serverFbml>
</div>
<style>
#fb-selector-c iframe { width: 755px; }
</style>

  </body>
</html>
