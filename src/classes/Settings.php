<?php
/**
 * This file implements the class Settings.
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
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright 2011 Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

/**
 * Settings
 *
 * Reads all of the settings files and stores them.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

class Settings extends Page
{

	/// Directory where the photos are stored
	static public $photos_dir;
	
	/// Directory where the thumbs are stored
	static public $thumbs_dir;
	
	/// Directory where the configuration files are stored
	static public $conf_dir;

	/// File where the admin settings are stored
	static public $admin_settings_file;


	/**** Admin Settings ****/

	/// Website name
	static public $name 		=	"PhotoShow";

	/// Website root address
	static public $site_address	=   "";

	/// Display Facebook button
	static public $like 		=	false;

	/// Activates RSS feed
	static public $rss 		=	true;

	/// Facebook app id (optional for facebook button)
	static public $fbappid 		=	"";

	/// Display Google button
	static public $plusone 		=	false;

	/// Remove comments button
	static public $nocomments 	=	false;

	/// Remove registering options
	static public $noregister	=	false;
    
	/// Force https on login/register screens
	static public $forcehttps	    =	false;

	/// Remove download options
	static public $nodownload	=	false;

	/// Max number of comments
	static public $max_comments	=	50;

	/// Reverse menu order
	static public $reverse_menu = 	false;

	/// Selected localization
	static private $loc 			=	"default";

	/// Selected theme
	static public $user_theme 		=	"Default";

	/// Default localization
	static private $loc_default	=	array();

	/// Localization selected
	static private $loc_chosen 	=	array();

	/// Activate l33t
	static private $l33t 		=	false;

	/**** Other ****/

	/// Folders list
	private $folders 			=	array();

	/// Path to localizations
	static private $locpath 	=	array();

	/// Available localizations
	static public $ava_loc 	=	array();
	
	/// Available themes
	static public $ava_themes 	=	array();

	/// Path to themes
	static public $themespath = "";

	/// Size of the thumbs in pixels
	static public $thumbs_size = 200;

	/*** Video ***/
	
	///Video encode enable/disable
	static public $encode_video	=	false;
	
	/// FFMPEg path (unix : /usr/bin/ffmpeg or win : c:\ffmpeg.exe)
	static public $ffmpeg_path 		=	"/usr/bin/ffmpeg";
	
	///FFMPEG Option
	static public $ffmpeg_option	=	"-threads 4 -qmax 40 -acodec libvorbis -ab 128k -ar 41000 -vcodec libvpx";	


	/**
	 * Create Settings page
	 * 
	 */
	public function __construct(){
		$this->folders = Menu::list_dirs(Settings::$photos_dir,true);
	}

	/**
	 * Read the settings in the files.
	 * If a settings file is missing, raise an exception.
	 *
     * @param string $config_file (for testing purpose only)
	 * @return void
	 * @author Thibaud Rohmer
	 */
	static public function init($forced = false, $config_file = NULL){

		/// Settings already created
		if(Settings::$photos_dir !== NULL && !$forced) return;

		if(!isset($config)) $config = (object)array();

        /// Set default values for $config
        $config->timezone = "Europe/Paris";

		/// Load config.php file 
        if (!isset($config_file)){
            $config_file		=	realpath(dirname(__FILE__)."/../../config.php");
        }
		if(!include($config_file)){
			throw new Exception("You need to create a configuration file.");
		}

		/// Setup variables
		Settings::$photos_dir	=	$config->photos_dir;
		Settings::$thumbs_dir	=	$config->ps_generated."/Thumbs/";
		Settings::$conf_dir		=	$config->ps_generated."/Conf/";
		Settings::$admin_settings_file = $config->ps_generated."/Conf/admin_settings.ini";

		/// Set TimeZone
		date_default_timezone_set($config->timezone);

		// Now, check that this stuff exists.
		if(!file_exists(Settings::$photos_dir)){
			if(! @mkdir(Settings::$photos_dir,0750,true)){	
				throw new Exception("PHOTOS dir '".Settings::$photos_dir."' doesn't exist and couldn't be created !");
			}
		}

		if(!file_exists(Settings::$thumbs_dir)){
			if(! @mkdir(Settings::$thumbs_dir,0750,true)){
				throw new Exception("PS_GENERATED dir '".Settings::$thumbs_dir."' doesn't exist or doesn't have the good rights.");
			}
		}

		if(!file_exists(Settings::$conf_dir)){
			if(! @mkdir(Settings::$conf_dir,0750,true)){
				throw new Exception("PS_GENERATED dir '".Settings::$conf_dir."' doesn't exist or doesn't have the good rights.");
			}
		}

		// Get Admin Settings
		if(file_exists(Settings::$admin_settings_file)){
			$admin_settings = parse_ini_file(Settings::$admin_settings_file);

			if(isset($admin_settings['name'])){
				Settings::$name			=	stripslashes($admin_settings['name']);
			}

			if(isset($admin_settings['fbappid'])){
				Settings::$fbappid	=	$admin_settings['fbappid'];
			}

            if ($admin_settings['site_address']){
                Settings::$site_address = $admin_settings['site_address'];
            }else{
                Settings::$site_address	= "http://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
            }

            // Formatting the address so we can directly append "?t=..." to it without worry
            if (!preg_match("/ndex.php$/", Settings::$site_address) && !preg_match("/.*\/$/", Settings::$site_address)){
                Settings::$site_address	=	Settings::$site_address."/";
            }

			Settings::$like 		=	isset($admin_settings['like']);
			Settings::$plusone 		=	isset($admin_settings['plusone']);
			Settings::$noregister	=	isset($admin_settings['noregister']);
			Settings::$forcehttps   =   isset($admin_settings['forcehttps']);
			Settings::$nocomments	=	isset($admin_settings['nocomments']);
			Settings::$nodownload	=	isset($admin_settings['nodownload']);
			Settings::$l33t 		=	isset($admin_settings['l33t']);
			Settings::$reverse_menu	=	isset($admin_settings['reverse_menu']);
			Settings::$rss	=	isset($admin_settings['rss']);



			if(isset($admin_settings['max_comments'])){
				Settings::$max_comments = 	$admin_settings['max_comments'] + 0;
			}

			if(isset($admin_settings['thumbs_size'])){
				Settings::$thumbs_size = 	$admin_settings['thumbs_size'] + 0;
			}

			if(isset($admin_settings['loc'])){
				Settings::$loc = $admin_settings['loc'];
			}
			
			if(isset($admin_settings['user_theme'])){
				Settings::$user_theme = $admin_settings['user_theme'];
			}

			/*** Video ***/
			Settings::$encode_video	=	isset($admin_settings['encode_video']);
			if(isset($admin_settings['ffmpeg_path'])){
				Settings::$ffmpeg_path	=	$admin_settings['ffmpeg_path'];
			}
			if(isset($admin_settings['ffmpeg_option'])){
				Settings::$ffmpeg_option	=	$admin_settings['ffmpeg_option'];
			}
		}

		// Localization files path
		Settings::$locpath = dirname(dirname(dirname(__FILE__)))."/inc/loc/";

		// Get Localization array
		if(is_file(Settings::$locpath."/".Settings::$loc)){
			Settings::$loc_chosen = parse_ini_file(Settings::$locpath."/".Settings::$loc,true);
		}

		Settings::$loc_default = parse_ini_file(Settings::$locpath."/default.ini",true);

		// Localization files available
		Settings::$ava_loc=array();
		$a = scandir(Settings::$locpath);
		foreach($a as $f){
			if(File::Extension($f) == "ini"){
				Settings::$ava_loc[]=$f;
			}
		}

		Settings::$themespath = dirname(dirname(dirname(__FILE__)))."/user/themes/";

		// Themes available
		Settings::$ava_themes=array();
		$a = scandir(Settings::$themespath);
		foreach($a as $f){
			if($f[0] != '.'){
				Settings::$ava_themes[]=$f;
			}
		}
	}

	/**
	 * Set website language
	 */
	static public function set_lang($l){
		// Get Localization array
		if(is_file(Settings::$locpath."/".$l.".ini")){
			Settings::$loc_chosen = parse_ini_file(Settings::$locpath."/".$l.".ini",true);
		}
	}

	/**
	 * Returns value of $t in selected language
	 * 
	 */
	static public function _($a,$t){
		if(isset(Settings::$loc_chosen[$a][$t])){
			$t = Settings::$loc_chosen[$a][$t];
		}else if(isset(Settings::$loc_default[$a][$t])){
			$t = Settings::$loc_default[$a][$t];
		}

		if(Settings::$l33t){
			$t = Settings::l33t($t);
		}

		return $t;
	}
	
	static public function toRegexp($i) {
		return "!" . $i . "!";
	}

	static public function l33t($t){
		$t 		= strtolower($t);
		$from 	= array("a", "e", "f", "g","l", "o", "s", "t","h", "c", "m","n", "r", "v", "w");
		$to 	= array("4", "3", "ph", "9","1", "0", "5",  "7",'|-|', '(', '|\/|','|\|', '|2', '\/', '\/\/');
    	
    	return preg_replace(array_map(array(Settings,toRegexp), $from), $to, $t);
	}

	/**
	 * Save new settings
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function set(){
        $var = array("name",
            "site_address",
            "like",
            "plusone",
            "fbappid",
            "max_comments",
            "noregister",
            "forcehttps",
            "nocomments",
            "nodownload",
            "loc",
            "l33t",
            "reverse_menu",
	    "encode_video",
	    "ffmpeg_path",
	    "ffmpeg_option",
	    "user_theme",
	    "thumbs_size",
   	    "rss"
	    );
		$f = fopen(Settings::$admin_settings_file,"w");

		foreach($var as $v){
			if(isset($_POST["$v"])){
				fwrite($f,"$v = \"".$_POST["$v"]."\"\n");
			}
		}
		fclose($f);
		Settings::init(true);
	}

	/**
	 * Generate thumbs and webimages reccursively inside a folder
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function gener_all($folder){
		$files = Menu::list_files($folder,true);

		if( !ini_get('safe_mode') ){ 
			set_time_limit(1200); 
		}

		foreach($files as $file){
			/// Generate thumb
			Provider::image($file,true,false,false);

			/// Generate webimg
			Provider::image($file,false,false,false);
		}
	}

	/**
	 * Display settings page
	 */
	public function toHTML(){

 		echo "<div class='header'>";
 		echo "<h1>Settings</h1>";
 		echo "</div>";

		/// Site Title
		echo "<form class='niceform pure-form pure-form-aligned' action='?t=Adm&a=Set' method='post'>\n";
		echo "<h2>Global</h2>
				<div class='pure-control-group'>
					<label for='name'>".Settings::_("settings","title")."</label>
					<input type='text' name='name' value=\"".htmlentities(Settings::$name, ENT_QUOTES ,'UTF-8')."\">
				</div>";

		/// Site Address
		echo "<div class='pure-control-group'>
					<label>".Settings::_("settings","site_address")."</label>
					<input type='text' name='site_address' value=\"".htmlentities(Settings::$site_address, ENT_QUOTES ,'UTF-8')."\">
				</div>";

		/// Language
		echo "<div class='pure-control-group'>
					<label>".Settings::_("settings","language")."</label>
					<select name='loc'>";
		foreach(Settings::$ava_loc as $l){
			$p = htmlentities($l, ENT_QUOTES ,'UTF-8');
			echo "<option value=\"".addslashes($p)."\"";
			if($p == Settings::$loc){
				echo " selected ";
			}
			echo ">".substr($p,0,-4)."</option>";
		}
		echo "</select>";			
		echo "</div>";

		/// User Theme
		echo "<div class='pure-control-group'>
					<label>".Settings::_("settings","user_theme")."</label>
					<select name='user_theme'>";
		foreach(Settings::$ava_themes as $l){
			$p = htmlentities($l, ENT_QUOTES ,'UTF-8');
			echo "<option value=\"".addslashes($p)."\"";
			if($p == Settings::$user_theme){
				echo " selected ";
			}
			echo ">$p</option>";
		}
		echo "</select>";			
		echo "</div>";

		echo "<h2>Options</h2>";
		$options = array("noregister","forcehttps","nocomments","nodownload","reverse_menu","l33t","rss");
		foreach($options as $val){
			$c = (Settings::$$val)?"checked":"";
				echo "<div class='pure-controls'><label><input type='checkbox' name='$val' $c> ".Settings::_("settings",$val)."</label></div>\n";
		};

		/// Max Comments
		echo "<div class='pure-control-group'>
					<label>".Settings::_("settings","numcomments")."</label>
					<input type='text' name='max_comments' value=\"".htmlentities(Settings::$max_comments, ENT_QUOTES ,'UTF-8')."\">
				</div>\n";


		/// Thumbs size
		echo "<div class='pure-control-group'>
					<label>".Settings::_("settings","thumbs_size")."</label>
					<input type='text' name='max_img_dir' value=\"".htmlentities(Settings::$thumbs_size, ENT_QUOTES ,'UTF-8')."\">
				</div>\n";

		echo "<h2>Social Networks</h2>";

		/// Facebook Button
		$c = (Settings::$like)?"checked":"";
		echo "<div class='pure-controls'>
				<label><input type='checkbox' name='like' $c>".Settings::_("settings","fb")."</label>
			</div>\n";


		/// Facebook App ID
		echo "<div class='pure-control-group'>
					<label>".Settings::_("settings","facebook_appid")."</label>
					<input type='text' name='fbappid' value=\"".htmlentities(Settings::$fbappid, ENT_QUOTES ,'UTF-8')."\">
				</div>";
		echo "<h2>Video</h2>";


		/// Encode Video
		echo "<span>".Settings::_("settings","video_comment")."</span>";
		echo "<div class='pure-controls'>";
		$c = (Settings::$encode_video)?"checked":"";
		echo "<label><input type='checkbox' name='encode_video' $c>Encode Video</label>\n";
		echo "</div>";

		
		/// FFmpeg Path
		echo "<div class='pure-control-group'>
					<label>".Settings::_("settings","ffmpeg_path")."</label>
					<input type='text' name='ffmpeg_path' value=\"".htmlentities(Settings::$ffmpeg_path, ENT_QUOTES ,'UTF-8')."\">
				</div>\n";

		/// FFmpeg command line
		echo "<div class='pure-control-group'>
					<label>".Settings::_("settings","ffmpeg_option")."</label>
					<input type='text' name='ffmpeg_option' value=\"".htmlentities(Settings::$ffmpeg_option, ENT_QUOTES ,'UTF-8')."\">
				</div>";


		echo "<div class='pure-controls'><input type='submit' class='pure-button pure-button-primary' value='".Settings::_("settings","submit")."'/></div>\n";

		echo "</form>\n";


		echo "<div class='section'><h2>".Settings::_("settings","generate")."</h2>";

		echo "<form class='niceform pure-form' action='?t=Adm&a=GAl' method='post'>\n";
		echo "<fieldset>
					<label>".Settings::_("settings","folder")."</label>
					<select name='path'>";
		echo "<option value='.'>".Settings::_("settings","all")."</option>";
		foreach($this->folders as $f){
			$p = htmlentities(File::a2r($f), ENT_QUOTES ,'UTF-8');
			echo "<option value=\"".addslashes($p)."\">".basename($p)."</option>";
		}
		echo "</select>";
		echo "<input type='submit' class='pure-button pure-button-primary' value='".Settings::_("settings","submit")."'/>\n";
		echo "</fieldset>";
		echo "</form>";
		echo "</div>";
	}
}
?>
