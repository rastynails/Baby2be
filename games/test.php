<?php

require_once('config.php');
require_once('common.php');


echo "Looking for files......<br />\n";

if(!file_exists($filesPath)) {
	echo "The folder $filesPath does not exist. Please check the filesPath parameter.<br />\n";
	exit;
} else {
	echo "Files found.<br />\n";
}


echo "Trying to create files......<br />\n";

if(!touch("$filesPath/file")) {
	echo "Files cannot be created inside the folder $filesPath. Please make sure that the web server can write files to this folder.<br />\n";
	exit;
} else {
	echo "Create file succeeded.<br />\n";
}


echo "Trying to open file......<br />\n";

$file = fopen("$filesPath/file", 'r+');

if($file === false) {
	echo "Cannot open file in read write mode. Please make sure that the web server have full access to the files in the folder at $filesPath.<br />\n";
	exit;
} else {
	echo "Open succeeded.<br />\n";
}


echo "Trying to write to file......<br />\n";

if(!fwrite($file, 'text')) {
	echo "Cannot write file. Please make sure that the web server have full access to the files in the folder at $filesPath.<br />\n";
	exit;
} else {
	echo "Write succeeded.<br />\n";
}


echo "Trying to read from file......<br />\n";

if(fseek($file, 0, SEEK_SET) == -1 || ($output = fread($file, 4)) === false) {
	echo "Cannot read file. Please make sure that the web server have full access to the files in the folder at $filesPath.<br />\n";
	exit;
} else {
	echo "Read succeeded.<br />\n";
}

fclose($file);


echo "Trying to delete file......<br />\n";

if(!unlink("$filesPath/file")) {
	echo "Cannot delete file. Please make sure that the web server have full access to the files in the folder at $filesPath.<br />\n";
	exit;
} else {
	echo "Delete succeeded.<br />\n";
}


echo "<br />EVERYTHING SEEMS TO WORK FINE<br />\n";

?>