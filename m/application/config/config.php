<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base path of the web site. 
 */
$config['site_domain'] = substr(MOBILE_SITE_DOMAIN, strpos(MOBILE_SITE_DOMAIN, '://') + 3);

/**
 * Force a default protocol to be used by the site. 
 */
$config['site_protocol'] = 'http';

//$config['index_page'] = 'index.php';

$config['url_suffix'] = '';
$config['internal_cache'] = FALSE;
$config['output_compression'] = FALSE;
$config['global_xss_filtering'] = TRUE;
$config['enable_hooks'] = FALSE;
$config['log_threshold'] = 0;
$config['log_directory'] = APPPATH.'logs';
$config['display_errors'] = TRUE;
$config['render_stats'] = FALSE;
$config['extension_prefix'] = 'SKM_';
$config['modules'] = array();
$config['mset'] = 5;
