<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$form_name = isset($_GET['SK-Field-Owner-Form']) ? $_GET['SK-Field-Owner-Form'] : $_POST['SK-Field-Owner-Form'];
$field_name = isset($_GET['SK-Field-Name']) ? $_GET['SK-Field-Name'] : $_POST['SK-Field-Name'];

if ( $_GET['submitted'] == 1 && (! isset($_FILES) || (count($_FILES) == 0)) )
{
    $FATAL_ERROR = SK_Language::text('%forms._errors.max_filesize_exceeded'); // post_max_size exceeded;
}

if ( !$form_name || !preg_match('~^\w+$~i', $form_name) ) {
	$FATAL_ERROR = 'undefined form name';
}
else {
	$include_path = DIR_FORMS_C . $form_name . '.form.php';

	if ( !file_exists($include_path) || ( dirname($include_path) . DIRECTORY_SEPARATOR ) !== DIR_FORMS_C ) {
		$FATAL_ERROR = 'unrecognized form "'.$form_name.'"';
	}
	else {
		require $include_path;

		try {
			$field = $form->getField($field_name);
		}
		catch ( SK_FormException $e ) {
			$FATAL_ERROR = $e->getMessage();
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="<?php echo URL_STATIC; ?>jquery.js"></script>
<script type="text/javascript">
// <!--
<?php

if ( isset($FATAL_ERROR) ) {
	echo "window.sk_upload_error = ".json_encode($FATAL_ERROR).";\n"; // TODO: user language messages
}
elseif ( isset($_FILES[$field_name]) )
{
	try {
		$tmp_file = SK_TemporaryFile::catchFile($_FILES[$field_name], $field); 
		$preview_html = $field->preview($tmp_file);

		echo "window.sk_userfile_uniqid = '".$tmp_file->getUniqid()."';\n",
			 "window.sk_userfile_preview = ".json_encode($preview_html).";\n";
	}
	catch (Exception $e) {
		echo "window.sk_upload_error = ".json_encode($e->getMessage()).";\n"; // TODO: user language messages
	}
}

?>

$(function() {
	window.$upload_form = $('#file_upload_form');
	window.$userfile = $('#userfile_input');
});
// -->
</script>
</head>

<body style="margin: 0px; padding: 0px">

<form id="file_upload_form" action="<?php echo SITE_URL . 'file_uploader.php?SK-Field-Owner-Form='.$form_name.'&SK-Field-Name='.$field_name.'&submitted=1'; ?>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="SK-Field-Owner-Form" value="<?php echo $form->getName(); ?>" />
	<input type="hidden" name="SK-Field-Name" value="<?php echo $field->getName(); ?>" />
	<input type="file" name="<?php echo $field->getName(); ?>" id="userfile_input" size="14" style="font-size: 11px" />
</form>

</body>
</html>
