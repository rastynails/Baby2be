<?php
if(empty($_POST))
{
	exit;
}
if($_POST['key'] == 'dzhasdiweasdlkas121312qdhiu2daczxc_save_config')
{
	//undefined
	$fc_json = str_replace('\"','"',$_POST['fc_json']);
	$config = '<?php 
	$config=\''. $fc_json.'\';
?>';
	$save = @file_put_contents('123flashchat_config.php',$config);
	if($save)
	{
		echo 'saved';
		exit;
	}
	else
	{
		echo 'error';
		exit;
	}
}
else if($_POST['key'] == 'WEDFVasdzz23s121312qdhiu2daczxc_load_config')
{
	include('123flashchat_config.php');
	echo $config;
	exit;
}

?>