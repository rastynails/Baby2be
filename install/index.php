<?php

error_reporting(E_ALL);

/**
 * Installation step abstraction.
 */
abstract class SkaDate7Install_Step
{

    public $header_js;


    public $onready_js;


    private $title;


    private $legend;


    abstract public function render();


    abstract public function process( array $post_data );


    abstract public static function is_completed();


    public function title( $value = null )
    {
        if ( isset($value) ) {
            $this->title = $value;
        }
        else {
            $title = htmlspecialchars($this->title, ENT_NOQUOTES);
            return "SkaDate9 Software Installation - $title";
        }
    }


    public function legend( $value = null )
    {
        if ( isset($value) ) {
            $this->legend = $value;
        }
        else {
            return htmlspecialchars($this->legend, ENT_NOQUOTES);
        }
    }

}

/**
 * Step 1: Site Roots.
 */
class SkaDate7Install_SiteRoots extends SkaDate7Install_Step
{
    /**
     * Get a default root directory.
     *
     * @return string
     */
    private function default_dir_root() {
        return preg_replace('~/install/index\.php$~', '/', $_SERVER['SCRIPT_FILENAME']);
    }

    /**
     * Step rendering.
     *
     * @return string
     */
    public function render()
    {
        $this->title('Site Roots');
        $this->legend('Home Directory/URL');

        $dir_root = isset(SkaDate7Install::$sess->dir_root)
            ? SkaDate7Install::$sess->dir_root
            : self::default_dir_root();

        $site_url = isset(SkaDate7Install::$sess->site_url) ? SkaDate7Install::$sess->site_url : '';

        $this->onready_js =
<<<EOT
var site_url_input = \$('input[name=site_url]');
if (!site_url_input.val()) {
    site_url_input.val(window.location.href.replace(/\/install(?:\/)?(?:index\.php(?:\\?[^$]+)?)?$/i, '/'));
}
EOT;

        return <<<EOT
<table cellspacing="1">
<tbody>
    <tr>
        <td class="left">
            <strong>Home Directory</strong><br />
            Specify a directory where the<br />
            SkaDate9 software script is installed.
        </td>
        <td class="right">
            <input name="dir_root" type="text" size="46" value="$dir_root" />
        </td>
    </tr>
    <tr>
        <td class="left">
            <strong>Home URL</strong><br />
            Specify your SkaDate9 script URL.
        </td>
        <td class="right">
            <input name="site_url" type="text" size="46" value="$site_url" />
        </td>
    </tr>
</tbody>
</table>
EOT;
    }

    /**
     * Step processing.
     *
     * @return boolean
     */
    public function process( array $post_data )
    {
        if ( !isset($post_data['dir_root']) ) {
            throw new Exception('undefined param `dir_root`', 0);
        }
        if ( !isset($post_data['site_url']) ) {
            throw new Exception('undefined param `site_url`', 0);
        }

        $test_file = $post_data['dir_root'].'internals'.DIRECTORY_SEPARATOR.'API'.DIRECTORY_SEPARATOR.'Component.class.php';

        if ( !file_exists($test_file) ) {
            return false;
        }
        SkaDate7Install::$sess->dir_root = $post_data['dir_root'];

        if ( !preg_match('~^http(?:s)?://[\w\-\.\~/]+/~i', $post_data['site_url']) ) {
            return false;
        }
        SkaDate7Install::$sess->site_url = $post_data['site_url'];

        return true;
    }

    /**
     * Says whether the step is completed.
     *
     * @return boolean
     */
    public static function is_completed() {
        return (isset(SkaDate7Install::$sess->dir_root) && isset(SkaDate7Install::$sess->site_url));
    }

}

/**
 * Step 2: RWX 777 Directories.
 */
class SkaDate7Install_RWXDirectories extends SkaDate7Install_Step
{
    const CONFIG_FILE_PATH = 'internals/config.php';

    /**
     * The list of directories and files which
     * are required to have an RWX 777 permissions.
     *
     * @var array
     */
    private static $rwx_required =
        array(
            'external_c/',
            'internal_c/',
            'userfiles/',
            'internals/config.php'
        );

    /**
     * The list of files and folders which is not copied during the files setup.
     *
     * @var array
     */
    private static $copy_exceptions = array('skadate9.sql');

    /**
     * Recursivele copy files creating directory structure.
     *
     * @param string $source_folder
     * @param string $dest_folder
     * @param integer $level
     */
    private static function copy_files( $source_folder, $dest_folder, $level = 0 )
    {
        $source_dir = new DirectoryIterator($source_folder);
        $dest_dir = new DirectoryIterator($dest_folder);

        $php_self = basename(__FILE__);

        foreach ( $source_dir as $file )
        {
            if ( $file->isDot() || in_array($file->getFilename(), self::$copy_exceptions) || (
                !$level && $file->getFilename() == $php_self
            ) ) {
                continue;
            }

            if ( $file->isDir() ) {
                $full_path = $dest_folder . $file->getFilename();
                if ( !is_dir($full_path) && !mkdir($full_path, 0777) ) {
                    throw new Exception('can\'t create directory `'.$full_path.'`', 1);
                }
                @chmod( $full_path, 0777 );

                self::copy_files(
                    $source_folder . $file->getFilename() . DIRECTORY_SEPARATOR,
                    $full_path . DIRECTORY_SEPARATOR,
                    $level+1
                );
            }
            elseif ( $file->isFile() ) {
                $full_dest_path = $dest_folder . $file->getFilename();
                if ( !file_exists($full_dest_path) ) {
                    $full_source_path = $source_folder . $file->getFilename();
                    if ( !copy($full_source_path, $full_dest_path) ) {
                        throw new Exception(
                            'can\'t copy file `'.$full_source_path.'` destination: `'.dirname($full_dest_path).'`'
                            , 2);
                    }
                }
            }
        }

        if ( !$level ) {
            SkaDate7Install::$sess->setup_files_copied = true;
        }
    }

    /**
     * Test whether a files are copied using self::copy_files() function.
     *
     * @return boolean
     */
    private static function files_copied() {
        return (isset(SkaDate7Install::$sess->setup_files_copied) && SkaDate7Install::$sess->setup_files_copied);
    }

    /**
     * Get a directories which are still not writable.
     *
     * @return array
     */
    public static function rwx_required()
    {
        $rwx_required = array();

        foreach ( self::$rwx_required as $filename )
        {
            if ( !is_writable(SkaDate7Install::$sess->dir_root . $filename) )
            {
                $rwx_required[] = $filename;
            }
        }

        return $rwx_required;
    }

    /**
     * Step rendering.
     *
     * @return string
     */
    public function render()
    {
        $this->title('RWX Required Directories');
        $this->legend('Writable Directories and Files');

        $rwx_required = self::rwx_required();

        $configNotWritable = in_array(self::CONFIG_FILE_PATH, $rwx_required);

        $html_content = '';
        if ( !count($rwx_required) ) {
            if ( $this->process(array()) ) { // if the folders is already writable copying files immediately
                SkaDate7Install::step(); // redirect user & exit
            }
            else {
                trigger_error('step processing error', E_USER_ERROR);
            }
        }
        elseif ( !file_exists(SkaDate7Install::$sess->dir_root . self::CONFIG_FILE_PATH) ) {
            $html_content =
<<<EOT
<p>
Please create an empty file with name `\config.php` in directory `internals/` and set it
<code title="read-write-execute" style="cursor: help">rwx</code> permissions to 777.
</p>
EOT;
        }
        else {

            $html_content =
<<<EOT
<p>Set <code title="read-write-execute" style="cursor: help">rwx</code> permissions to 777 for the following directories and files:</p>
EOT;

            if ( $configNotWritable ) {
            $html_content .=
<<<EOT
<p>
Return `internals/\config.php` file <code title="read-write-execute" style="cursor: help">rwx</code> permissions to 644 after the installation has been completed
</p>
EOT;
            }
            $html_content .='<ul style="line-height: 18px">';

            foreach ( $rwx_required as $directory ) {
                $html_content .= '<li><code>'.htmlspecialchars($directory, ENT_NOQUOTES).'</code></li>';
            }

            $html_content .=
<<<EOT
</ul>
EOT;
        }

        return $html_content;
    }

    /**
     * Step processing.
     *
     * @return boolean
     */
    public function process( array $post_data )
    {
        if ( count(self::rwx_required()) ) {
            return false;
        }

        // copying setup files
        self::copy_files('./', '../');

        return true;
    }

    /**
     * Says whether the step is completed.
     *
     * @return boolean
     */
    public static function is_completed() {
        return (!count(self::rwx_required()) && self::files_copied());
    }
}

/**
 * Step 3: MySQL Connection.
 */
class SkaDate7Install_MySQLConnection extends SkaDate7Install_Step
{
    /**
     * Default database table prefix.
     */
    const DEFAULT_TBL_PREFIX = 'skadate_';

    /**
     * Fields 'name' => $value list.
     *
     * @var array
     */
    private static $fields =
        array(
            'db_host'   => 'localhost',
            'db_user'   => null,
            'db_pass'   => null,
            'db_name'   => null,
            'db_prefix' => 'skadate_'
        );

    /**
     * Check the MySQL connection.
     *
     * @param array $connection_data
     */
    private static function check_connection( array $connection_data = null )
    {
        static $connection;

        if ( !isset($connection) )
        {
            if ( !isset($connection_data) ) {
                $connection_data = array();
                foreach ( self::$fields as $field => $def_val ) {
                    $connection_data[$field] = SkaDate7Install::$sess->$field;
                }
            }

            define('DB_HOST', $connection_data['db_host']);
            define('DB_USER', $connection_data['db_user']);
            define('DB_PASS', $connection_data['db_pass']);
            define('DB_NAME', $connection_data['db_name']);
            define('DB_TBL_PREFIX', $connection_data['db_prefix']);

            require_once SkaDate7Install::$sess->dir_root.'internals'.DIRECTORY_SEPARATOR.'API'.DIRECTORY_SEPARATOR.'MySQL.class.php';
            require_once SkaDate7Install::$sess->dir_root.'internals'.DIRECTORY_SEPARATOR.'API'.DIRECTORY_SEPARATOR.'QueryLogger.class.php';
            require_once SkaDate7Install::$sess->dir_root.'internals'.DIRECTORY_SEPARATOR.'API'.DIRECTORY_SEPARATOR.'Profiler.class.php';

            $connection = SK_MySQL::connect();
        }

        return $connection;
    }

    /**
     * Executes an SQL dump file.
     *
     * @param string $sql_file path to file
     */
    private static function SQL_import( $sql_file )
    {
        if ( !($fd = @fopen($sql_file, 'rb')) ) {
            trigger_error('SQL dump file `'.$sql_file.'` not found', E_USER_ERROR);
        }

        $entry_tbl_prefix = SkaDate7Install::$sess->db_prefix;

        $prefix_changed = ($entry_tbl_prefix != self::DEFAULT_TBL_PREFIX);

        if ( $prefix_changed ) {
            $str_search = array(
                'DROP TABLE IF EXISTS `'.self::DEFAULT_TBL_PREFIX,
                'CREATE TABLE `'.self::DEFAULT_TBL_PREFIX,
                'CREATE TABLE IF NOT EXISTS `'.self::DEFAULT_TBL_PREFIX,
                'INSERT INTO `'.self::DEFAULT_TBL_PREFIX
            );
            $str_replace = array(
                'DROP TABLE IF EXISTS `'.$entry_tbl_prefix,
                'CREATE TABLE `'.$entry_tbl_prefix,
                'CREATE TABLE IF NOT EXISTS `'.$entry_tbl_prefix,
                'INSERT INTO `'.$entry_tbl_prefix
            );
        }

        self::check_connection(); // connecting MySQL database

        $line_no = 0;
        $query = '';
        while ( false !== ($line = fgets($fd, 10240)) )
        {
            $line_no++;

            if ( !strlen(($line = trim($line)))
                || $line{0} == '#' || $line{0} == '-'
                || preg_match('~^/\*\!.+\*/;$~siu', $line) ) {
                continue;
            }

            $query .= $line;

            if ( $line{strlen($line)-1} != ';' ) {
                continue;
            }

            if ( $prefix_changed ) {
                $query = str_replace($str_search, $str_replace, $query);
            }

            try {
                SK_MySQL::query($query);
            }
            catch ( SK_MySQL_Exception $e ) {
                trigger_error(
                    "SQL import error while executing line #$line_no '$line'.<br />.\n".
                    '<b>MySQL Error:</b> '.mysql_error().'</code>'
                    , E_USER_WARNING
                );
            }

            $query = '';
        }

        fclose($fd);

        return true;
    }

    /**
     * Step rendering.
     *
     * @return string
     */
    public function render()
    {
        $this->title('Database connection');
        $this->legend('MySQL connection');

        foreach ( self::$fields as $field => $def_val ) {
            $$field = isset($_POST[$field]) ? htmlspecialchars($_POST[$field]) : $def_val;
        }

        $html_content =
<<<EOT
<table cellspacing="1">
    <tbody>
        <tr>
            <td class="left">
                <strong>Host name</strong><br />
                Database host name
            </td>
            <td><input name="db_host" type="text" value="$db_host" /></td>
        </tr>
        <tr>
            <td class="left">
                <strong>Username</strong><br />
                Database user
            </td>
            <td><input name="db_user" type="text" value="$db_user" /></td>
        </tr>
        <tr>
            <td class="left">
                <strong>Password</strong><br />
                Database Password
            </td>
            <td><input name="db_pass" type="text" value="$db_pass" /></td>
        </tr>
        <tr>
            <td class="left">
                <strong>Database name</strong><br />
                Database name
            </td>
            <td><input name="db_name" type="text" value="$db_name" /></td>
        </tr>
        <tr>
            <td class="left">
                <strong>Prefix</strong><br />
                Database tables prefix
            </td>
            <td><input name="db_prefix" type="text" value="$db_prefix" /></td>
        </tr>
    </tbody>
</table>
EOT;
        return $html_content;
    }

    /**
     * Step processing.
     *
     * @return boolean
     */
    public function process( array $post_data )
    {
        // entry checking
        foreach ( self::$fields as $field => $def_val ) {
            if ( !isset($post_data[$field]) ) {
                throw new Exception('undefined param `'.$field.'`');
            }
        }

        if ( self::check_connection($post_data) ) {
            foreach ( self::$fields as $field => $def_val ) {
                SkaDate7Install::$sess->$field = $post_data[$field];
            }

            if ( self::SQL_import('./skadate9.sql') ) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    /**
     * Says whether the step is completed.
     *
     * @return boolean
     */
    public static function is_completed()
    {
        return (
            isset(SkaDate7Install::$sess->db_host)
            && isset(SkaDate7Install::$sess->db_user)
            && isset(SkaDate7Install::$sess->db_pass)
            && isset(SkaDate7Install::$sess->db_name)
            && isset(SkaDate7Install::$sess->db_prefix)
        );
    }

}

/**
 * Step 4: Config File.
 */
class SkaDate7Install_Finish extends SkaDate7Install_Step
{
    /**
     * Get the config filename.
     *
     * @return string
     */
    public static function filename() {
        return SkaDate7Install::$sess->dir_root.'internals'.DIRECTORY_SEPARATOR.'config.php';
    }

    /**
     * Write config.php file.
     */
    private static function write_config_file()
    {
        $config_vars =
            array(
                'dir_root', 'site_url',
                'db_host', 'db_user', 'db_pass', 'db_name', 'db_prefix'
            );

        foreach ( $config_vars as $var ) {
            $$var = addslashes(SkaDate7Install::$sess->$var);
        }

        if ( !($fd = @fopen(self::filename(), 'wb')) ) {
            return false;
        }

        $passwordSalt = uniqid();

        fwrite($fd, <<<EOT
<?php

// Site Roots
define('DIR_SITE_ROOT', '$dir_root');
define('SITE_URL', '$site_url');

define('SK_PASSWORD_SALT', '$passwordSalt');

// Development mode
define('DEV_MODE', false);
define('DEV_FORCE_COMPILE', false);
define('DEV_PROFILER', false);

// MySQL Connection
define('DB_HOST', '$db_host');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
define('DB_NAME', '$db_name');
define('DB_TBL_PREFIX', '$db_prefix');

EOT
        );

        fclose($fd);

        return true;
    }

    /**
     * Test the config file.
     *
     * @return boolean
     */
    private static function test()
    {
        $config_file = self::filename();

        if ( !file_exists($config_file) || !(
            $fd = @fopen($config_file, 'rb')
        ) ) {
            return false;
        }

        // reading file
        $contents = '';
        while (!feof($fd)) {
            $contents .= fread($fd, 8192);
        }
        fclose($fd);

        // matches single or double quoted strings
        $value_str = '(?:' . '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"' . '|' . '\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'' . ')';
        // getting constant definition lines
        preg_match_all("~define\s*\(\s*(?:\"|')([A-Z_]+)(?:\"|')\s*,\s*($value_str)\s*\);~", $contents, $matches);

        $const_required =
            array(
                'DIR_SITE_ROOT',
                'SITE_URL',
                'DB_HOST',
                'DB_USER',
                'DB_PASS',
                'DB_NAME',
                'DB_TBL_PREFIX'
            );

        $const_defined = array_flip($matches[1]);

        foreach ( $const_required as $const ) {
            if ( !isset($const_defined[$const]) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Step rendering.
     *
     * @return string
     */
    public function render()
    {
        $this->title('Installation complete');
        $this->legend('Congratulation');

        $config_file = self::filename();

        $this->process(array());

        $html_content =
<<<EOT
<p>
    Software installation complete
</p>
<input type="hidden" name="installation_complete" value="1">
EOT;
        return $html_content;
    }

    /**
     * Step processing.
     *
     * @return boolean
     */
    public function process( array $post_data )
    {
        if ( self::write_config_file() )
        {
            // generating language cache
            require_once SkaDate7Install::$sess->dir_root . 'internals' . DIRECTORY_SEPARATOR . 'Header.inc.php';
            SK_LanguageEdit::generateCache();

            SK_Config::section('site.admin')->set('admin_password', app_Passwords::hashPassword('76Ag590'));

            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Says whether the step is completed.
     *
     * @return boolean
     */
    public static function is_completed() {
        return isset($_POST['installation_complete']) && self::test();
    }

}


/**
 * SkaDate Installation class.
 */
class SkaDate7Install
{
    /**
     * Refence to an installation session.
     *
     * @var Object
     */
    public static $sess;

    /**
     * SkaDate7 installation steps.
     *
     * @var array
     */
    private static $install_steps =
        array(
            1 => 'SiteRoots',
            2 => 'RWXDirectories',
            3 => 'MySQLConnection',
            4 => 'Finish'
        );

    /**
     * Get current step name.
     *
     * @return string
     */
    public static function getCurrentStep()
    {
        foreach ( self::$install_steps as $step_no => $step_name ) {
            if ( !call_user_func(array("SkaDate7Install_$step_name", 'is_completed')) ) {
                return $step_name;
            }
        }
    }

    /**
     * Get previous step name.
     *
     * @return string
     */
    public static function getPreviousStep()
    {

    }

    /**
     * Process installation.
     */
    public static function process()
    {
        session_start();

        self::$sess = &$_SESSION['SkaDate7_Install'];

        $curr_step = self::getCurrentStep();

        if ( !isset($curr_step) ) {

            // setup is completed
            header('Location: '.SkaDate7Install::$sess->site_url);
            exit();
        }

        $step_class = 'SkaDate7Install_'.$curr_step;

        $step_instance = new $step_class;

        if ( ($_SERVER['REQUEST_METHOD'] == 'POST')
            && $step_instance->process($_POST)
        ) {
            self::step(); // script exits
        }

        self::display($step_instance);
    }

    /**
     * Display instalation step.
     *
     * @param SkaDate7Install_Step $Step
     */
    public static function display( SkaDate7Install_Step $Step )
    {
        $curr_step = str_replace('SkaDate7Install_', '', get_class($Step));

        $color = '#1C66A3';
        foreach ( self::$install_steps as $step_no => $step_name )
        {
            if ( $step_name != $curr_step ) {
                $str = "<span style=\"color: $color\">$step_name</span>";
            }
            else {
                $str = "<b style=\"font-size: 11px; color: #005AA3\">[$curr_step]</b>";
                $color = '#A3A3A3';
            }

            if ( isset($bread_crumb) )
                $bread_crumb .= "&nbsp; &raquo; &nbsp;$str";
            else
                $bread_crumb = $str;
        }

        $content = $Step->render();

        $prev_step = self::getPreviousStep();

        $back_button_extra = $prev_step
? <<<EOT
onclick="window.location.href = '?step=$prev_step'"
EOT
: 'disabled="disabled"';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title><?php echo $Step->title() ?></title>
    <link rel="stylesheet" type="text/css" href="../admin/css/admin.css" />
    <script type="text/javascript" src="../static/jquery.js"></script>
    <script type="text/javascript">
    <?php echo $Step->header_js ?>
    var $ = jQuery.noConflict();
    $(function () {
        <?php echo $Step->onready_js ?>
    });
    </script>
</head>
<body>
    <div style="margin-top: 10px; font-size: 11px; text-align: center">
    <?php echo $bread_crumb; ?>
    </div>

    <form method="post">
    <table class="skadate7_install" style="margin: 20px auto">
        <thead>
            <tr>
                <td class="corner_left_top"></td>
                <td colspan="2" class="top_side"><?php echo $Step->legend() ?></td>
                <td class="corner_right_top"></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4" class="center_side"><?php echo $content ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td class="corner_left_bottom"></td>
                <td class="bottom_side" style="text-align: left">
                    <input type="button" value="&laquo; Back" <?php echo $back_button_extra ?> style="display: none" />
                </td>
                <td class="bottom_side" style="text-align: right">
                    <input type="submit" value="Next &raquo;" />
                </td>
                <td class="corner_right_bottom"></td>
            </tr>
        </tfoot>
    </table>
    </form>
</body>
</html>
<?php
        exit();
    }

    /**
     * Redirects user to process next step.
     */
    public static function step() {
        header('Location: '.$_SERVER['REQUEST_URI']);
        exit();
    }

}

// processing instalation
SkaDate7Install::process();

?>
