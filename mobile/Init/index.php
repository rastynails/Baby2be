<?php

//Pre-Requisites

require_once("../classes/essentials.class.php");

require_once('../../internals/config.php');

require_once('../classes/mysql.class.php');



$essence	=	new Essentials();

$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());



if(isset($_GET["param"]))

{

	if($_GET["param"]	==	"getCsC")

	{

		

	}

}

else

{

	$salt		=	$essence->getHashSalt();

	echo '{"Salt":"'.$salt.'"}';

}

?>