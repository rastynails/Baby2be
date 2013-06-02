<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';
require_once DIR_SITE_ROOT . 'facebook_connect' . DIRECTORY_SEPARATOR . 'init.php';

$requestIds = empty($_GET['request_ids']) ? array() : explode(',', $_GET['request_ids']);

$facebook = FBC_Service::getInstance()->getFaceBook();

$inviters = array();
foreach ( $requestIds as $rid )
{
    try
    {
        $request = $facebook->api('/' . $rid);
    }
    catch ( Exception $e )
    {
        continue;
    }

    $data = empty($request['data']) ? array() : json_decode($request['data'], true);

    if ( !empty($data['userId']) )
    {
        $inviters[] = $data['userId'];
    }
}

$uniqInviters = array_unique($inviters);
$joinData = array(
    'inviters' => $uniqInviters,
    'requestIds' => $requestIds
);

$_SESSION['%%facebook_invite_data%%'] = $joinData;

if ( !empty($_GET['redirect']) )
{
    SK_HttpRequest::redirect(SITE_URL);
}

?>

<html>
<head>

<script type='text/javascript'>
    window.location.href = <?php echo json_encode(SITE_URL); ?>;
</script>

</head>

<body>
</body>
</html>

