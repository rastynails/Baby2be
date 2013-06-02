<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$popup = new component_UserSelectorPopup();

if ( isset($_GET['error']) )
{
    $popup->setError();
    SK_Layout::getInstance()->display($popup);
    exit;
}

//setting parameters
$authcode= $_GET["code"];

$clientId = SK_Config::section('google')->client_id;
$clientSecret = SK_Config::section('google')->client_secret;

$redirectUri = SITE_URL . 'google/oauth.php';

$fields = array(
    'code' => urlencode($authcode),
    'client_id'=>  urlencode($clientId),
    'client_secret'=>  urlencode($clientSecret),
    'redirect_uri'=>  urlencode($redirectUri),
    'grant_type'=>  urlencode('authorization_code')
);

//url-ify the data for the POST

$fieldsString='';

foreach( $fields as $key => $value )
{
    $fieldsString .= $key . '=' . $value . '&';
}

$fieldsString = rtrim($fieldsString, '&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL,'https://accounts.google.com/o/oauth2/token');
curl_setopt($ch,CURLOPT_POST,5);
curl_setopt($ch,CURLOPT_POSTFIELDS, $fieldsString);

// Set so curl_exec returns the result instead of outputting it.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//to trust any ssl certificates
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);

//extracting access_token from response string
$response=  json_decode($result);

if ( empty($response->access_token) )
{
    $authUrl = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query(array(
        'response_type' => 'code',
        'client_id' => $clientId,
        'redirect_uri' => $redirectUri,
        'state' => 'contacts',
        'scope' => 'https://www.google.com/m8/feeds/'
    ));

    SK_HttpRequest::redirect($authUrl);
}

$accessToken= $response->access_token;
//passing accesstoken to obtain contact details
$resultCount = 100;
$jsonResponse =  file_get_contents('https://www.google.com/m8/feeds/contacts/default/full?max-results=' . $resultCount . '&oauth_token=' . $accessToken . '&alt=json');
$response = json_decode($jsonResponse, true);

$out = array();
$list = $response['feed']['entry'];

$defaultImage = app_ProfilePhoto::defaultPhotoUrl(0, app_ProfilePhoto::PHOTOTYPE_THUMB);

$contexId = uniqid('ci');
$jsArray = array();

foreach ( $list as $item )
{
    if ( empty($item['gd$email'][0]['address']) )
    {
        continue;
    }

    $address = $item['gd$email'][0]['address'];
    $image = $item['link'][1]['type'] != 'image/*' ? $defaultImage : $item['link'][1]['href'] . '?oauth_token=' . $accessToken;
    $title = empty($item['title']['$t']) ? $address : $item['title']['$t'];
    $uniqId = uniqid('cii');

    $out[] = array(
        'title' => $title,
        'image' => $image,
        'address' => $address,
        'uniqId' => $uniqId,
        'fields' => empty($item['title']['$t']) ? '' : $address,
        'avatar' => array(
            'title' => $title,
            'src' => $image
        )
    );
}

$data = array (
    0 => array ( 'title' => 'grey rabbit', 'image' => 'http://sandbox.skadate.com/support/layout/themes/black/img/sex_0_no_photo_thumb.jpg', 'address' => 'grey_rabbit@list.ru', 'uniqId' => 'cii511c7d02b056c', 'fields' => 'grey_rabbit@list.ru', 'avatar' => array ( 'title' => 'grey rabbit', 'src' => 'http://sandbox.skadate.com/support/layout/themes/black/img/sex_0_no_photo_thumb.jpg', ), ),
    1 => array ( 'title' => 'greyexpert@gmail.com', 'image' => 'https://www.google.com/m8/feeds/photos/media/grey1986%40gmail.com/18ec99398fa64b20?oauth_token=ya29.AHES6ZQQbvAUPmTuvh2FxQxxf4ePVWaOgQRbwoHPks365n3Hau3iIQ', 'address' => 'greyexpert@gmail.com', 'uniqId' => 'cii511c7d02b2c7c', 'fields' => '', 'avatar' => array ( 'title' => 'greyexpert@gmail.com', 'src' => 'https://www.google.com/m8/feeds/photos/media/grey1986%40gmail.com/18ec99398fa64b20?oauth_token=ya29.AHES6ZQQbvAUPmTuvh2FxQxxf4ePVWaOgQRbwoHPks365n3Hau3iIQ', ), ),
    2 => array ( 'title' => 'romeoadmin@gmail.com', 'image' => 'http://sandbox.skadate.com/support/layout/themes/black/img/sex_0_no_photo_thumb.jpg', 'address' => 'romeoadmin@gmail.com', 'uniqId' => 'cii511c7d02b538b', 'fields' => '', 'avatar' => array ( 'title' => 'romeoadmin@gmail.com', 'src' => 'http://sandbox.skadate.com/support/layout/themes/black/img/sex_0_no_photo_thumb.jpg', ), ),
    3 => array ( 'title' => 'chyprina@i.ua', 'image' => 'http://sandbox.skadate.com/support/layout/themes/black/img/sex_0_no_photo_thumb.jpg', 'address' => 'chyprina@i.ua', 'uniqId' => 'cii511c7d02b7a9c', 'fields' => '', 'avatar' => array ( 'title' => 'chyprina@i.ua', 'src' => 'http://sandbox.skadate.com/support/layout/themes/black/img/sex_0_no_photo_thumb.jpg', ), ),
    4 => array ( 'title' => 'Камбалин Серегей', 'image' => 'https://www.google.com/m8/feeds/photos/media/grey1986%40gmail.com/31fac48c0c3e80ed?oauth_token=ya29.AHES6ZQQbvAUPmTuvh2FxQxxf4ePVWaOgQRbwoHPks365n3Hau3iIQ', 'address' => 'grey1986@gmail.com', 'uniqId' => 'cii511c7d02ba1bc', 'fields' => 'grey1986@gmail.com', 'avatar' => array ( 'title' => 'Камбалин Серегей', 'src' => 'https://www.google.com/m8/feeds/photos/media/grey1986%40gmail.com/31fac48c0c3e80ed?oauth_token=ya29.AHES6ZQQbvAUPmTuvh2FxQxxf4ePVWaOgQRbwoHPks365n3Hau3iIQ', ), ),
    5 => array ( 'title' => 'Азат', 'image' => 'http://sandbox.skadate.com/support/layout/themes/black/img/sex_0_no_photo_thumb.jpg', 'address' => 'amangaziev_85@mail.ru', 'uniqId' => 'cii511c7d02bc8c4', 'fields' => 'amangaziev_85@mail.ru', 'avatar' => array ( 'title' => 'Азат', 'src' => 'http://sandbox.skadate.com/support/layout/themes/black/img/sex_0_no_photo_thumb.jpg', ), ),
    6 => array ( 'title' => 'irina.kambalina@gmail.com', 'image' => 'http://sandbox.skadate.com/support/layout/themes/black/img/sex_0_no_photo_thumb.jpg', 'address' => 'irina.kambalina@gmail.com', 'uniqId' => 'cii511c7d02befcd', 'fields' => '', 'avatar' => array ( 'title' => 'irina.kambalina@gmail.com', 'src' => 'http://sandbox.skadate.com/support/layout/themes/black/img/sex_0_no_photo_thumb.jpg', ), ) );

$popup->setData($data);

SK_Layout::getInstance()->display($popup);