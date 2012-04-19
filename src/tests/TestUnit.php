<?php
/**
 * This file implements tools for unit tests
 * 
 * PHP versions 4 and 5
 *
 * LICENSE:
 * 
 * This file is part of PhotoShow.
 *
 * PhotoShow is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhotoShow is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Franck Royer <royer.franck@gmail.com>
 * @copyright 2012 Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

/**
 * Unit test tools
 *
 * I used that for some debug. It's incomplete and I guess
 * It would be better to have a proper framework for unit 
 * test on PHP website. Anyway, it does not harm anyone for now
 *
 * @category  Website
 * @package   Photoshow
 * @license   http://www.gnu.org/licenses/
 */

class TestUnit extends PHPUnit_Framework_TestCase
{
    // The config file use for test
    public static $config_file;

    /**************************************************/
    /* Functions are here to do a setup for all tests */
    /**************************************************/

    /**
     * General setupbeforeClasse
     * @author Franck Royer
     */
    public static function setUpBeforeClass()
    {
        self::include_all();
        $GLOBALS['config_file'] = realpath(dirname(__FILE__))."/test_config.php";
        self::prepare_files();
        self::init_config();
        self::create_accounts();
    }

    /**
     * General setup
     * @author Franck Royer
     */
    protected function setUp()
    {
        //always reset the session before a method
        session_unset();
    }

    function __construct()
    {
        require_once('PHPUnit'.DIRECTORY_SEPARATOR.'Framework'.DIRECTORY_SEPARATOR.'TestCase.php');
        parent::__construct();
    }
    function __destruct()
    {
        self::clean_files();
    }


    /**
     * includes all PhotoShow classes
     * TODO: autoload
     * @author Franck Royer
     */

    public static function include_all()
    {
        $toinclude = array( 
            realpath(dirname(__FILE__)."/../classes/HTMLObject.php"),
            realpath(dirname(__FILE__)."/../classes/Page.php"),
            realpath(dirname(__FILE__)."/../classes/File.php"),
            realpath(dirname(__FILE__)."/../classes/Account.php"),
            realpath(dirname(__FILE__)."/../classes/Group.php"),
            realpath(dirname(__FILE__)."/../classes/Menu.php"),
            realpath(dirname(__FILE__)."/../classes/GuestToken.php"),
            realpath(dirname(__FILE__)."/../classes/CurrentUser.php"),
            realpath(dirname(__FILE__)."/../classes/Video.php"),
            realpath(dirname(__FILE__)."/../classes/Cleaning.php"),
            realpath(dirname(__FILE__)."/../classes/Settings.php")
        );

        foreach ( $toinclude as $class_file ){
            if(!require_once($class_file)){
                throw new Exception("Cannot find ".$class_file." file");
            }
        }
    }

    /**
     * Load the config
     */
    public static function init_config(){
        Settings::init(false,$GLOBALS['config_file']);
        try {
            CurrentUser::init();
        } catch(Exception $e){
            // Yes I know, no account file found
        }
    }

    /**
     * Function to prepare the files
     */
    public static function prepare_files(){
        if (!include($GLOBALS['config_file'])){
            throw new Exception("Cannot include config file!\n");
        }

        if (!file_exists($config->photos_dir)){
            mkdir($config->photos_dir, 0777, true);
        }

        if (!file_exists($config->ps_generated)){
            mkdir($config->ps_generated, 0777, true);
        }
        if (!file_exists($config->photos_dir."/subfolder")){
            mkdir($config->photos_dir."/subfolder", 0777, true);
        }
        if (!file_exists($config->photos_dir."/tokenfolder")){
            mkdir($config->photos_dir."/tokenfolder", 0777, true);
        }
        //TODO download a photo file
        //TODO download a video file
    }

    /**
     * prepare test accounts
     */
    public static function create_accounts(){
        // Create admin account

        // First account is always admin
        if ( !Account::exists("testadmin") ){
            if( !Account::create("testadmin","testadminpassword","testadminpassword") ){
                throw new Exception ("Cannot create admin account");
            }
        }
        // Create normal account
        if ( !Account::exists("testuser") ){
            if( !Account::create("testuser","testpassword","testpassword") ){
                throw new Exception ("Cannot create testuser account");
            }
        }
    }

    /**
     * Function to clean the files
     */
    public static function clean_files(){
        if (!include($GLOBALS['config_file'])){
            throw new Exception("Cannot include config file!\n");
        }

        $parent_folder= dirname($config->photos_dir);
        if (!file_exists($parent_folder)){
            return;
        }

        if (!preg_match('/.*tmp\/?$/', $parent_folder)){
            echo ("Folder is not named tmp, I am not taking the risk to delete it");
            return;
        }
        self::rrmdir($parent_folder);
    }

    /**
     * Recursively delete a folder
     */
    protected function rrmdir($dir) {
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file))
                self::rrmdir($file);
            else
                unlink($file);
        }
        rmdir($dir);
    }

    /*****************************************************/
    /* Functions used to setup scenarios for other tests */
    /*                                                   */ 
    /* Such functions  must be added here and write only */
    /* unit test functions in XxxTest classes.           */
    /*****************************************************/


    /**
     * log you as an admin
     * 
     */
    public function login_as_admin(){
        if( !CurrentUser::login("testadmin","testadminpassword") ){
            throw new Exception ("Cannot login as testadmin");
        }
    }

    /**
     * log you as a testuser
     * 
     */
    public function login_as_user($login = "testuser", $password = "testpassword"){
        if( !CurrentUser::login($login, $password) ){
            throw new Exception ("Cannot login as ".$login);
        }
    }

    /**
     * create a token and give you the ouput
     * actually it's a bit of cheating
     * if a token already exist for the given path we return it
     * otherwise, we create a new one
     */
    public function create_token($path=NULL){

        // default path is the token folder
        if (!isset($path)){
            $path = Settings::$photos_dir."/tokenfolder";
        }

        // do we already have a token ?
        $tokens = GuestToken::find_for_path(File::a2r($path), true);
        if (!empty($tokens)){
            return $tokens[0]['key'];
        }

        // No token found, Creating a token to allow guest view for the given path
        $key = Guesttoken::generate_key();
        if ( !GuestToken::create($path,$key) ){
            throw new Exception ("Cannot create token for path ".$path."\n");
        }
        return $key;
    }


    /**
     * Delete the token file
     */
    public static function delete_tokens_file(){
        if (file_exists(CurrentUser::$tokens_file)){
            unlink(CurrentUser::$tokens_file);
        }
    }
}
?>
