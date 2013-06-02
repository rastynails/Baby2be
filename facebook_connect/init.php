<?php

define('FBC_DIR_MAIN', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('FBC_DIR_INTERNALS', FBC_DIR_MAIN . 'internals' . DIRECTORY_SEPARATOR);
define('FBC_DIR_CLASSES', FBC_DIR_INTERNALS . 'classes' . DIRECTORY_SEPARATOR);
define('FBC_DIR_BOL', FBC_DIR_INTERNALS . 'bol' . DIRECTORY_SEPARATOR);
define('FBC_DIR_PLATFORM', FBC_DIR_INTERNALS . 'facebook-platform' . DIRECTORY_SEPARATOR);


require_once FBC_DIR_BOL . 'base_dao.php';
require_once FBC_DIR_PLATFORM . 'facebook.php';
require_once FBC_DIR_CLASSES . 'connect_details.php';

require_once FBC_DIR_BOL . 'field.php';
require_once FBC_DIR_BOL . 'field_dao.php';
require_once FBC_DIR_BOL . 'auth.php';
require_once FBC_DIR_BOL . 'auth_dao.php';

require_once FBC_DIR_BOL . 'service_base.php';
require_once FBC_DIR_BOL . 'service.php';
require_once FBC_DIR_BOL . 'admin_service.php';
require_once FBC_DIR_BOL . 'auth_service.php';

require_once FBC_DIR_CLASSES . 'converter_base.php';
require_once FBC_DIR_CLASSES . 'converters.php';

require_once FBC_DIR_CLASSES . 'auth_adapter.php';

require_once FBC_DIR_CLASSES . 'profile.php';




