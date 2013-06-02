<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<title><?php echo SK_Language::text('%txt.site_suspended.title');	?></title>
		<style>p{ font-family: Verdana; font-size: 11px }</style>
	</head>
	
	<body>
		<p><center><h1>
		<?php
			echo SK_Language::text('%txt.site_suspended.msg');
		?></h1>
		</center></p>
	</body>
</html>