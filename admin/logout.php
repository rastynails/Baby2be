<?php

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

logoutAdmin();

echo "You have been logged out. <a href=\"".SITE_URL."\">Go to site</a>";
?>