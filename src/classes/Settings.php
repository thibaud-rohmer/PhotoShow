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

	/// Display Facebook button
	static public $like 		=	false;

	/// Display Google button
	static public $plusone 		=	false;

	/// Remove comments button
	static public $nocomments 	=	false;

	/// Remove registering options
	static public $noregister	=	false;

	/// Remove download options
	static public $nodownload	=	false;

	/// Max number of comments
	static public $max_comments	=	50;

	/// Max number of comments
	static public $max_img_dir	=	5;

	/// Selected localization
	static public $loc 			=	"default";

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
	static private $ava_loc 	=	array();


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
	 * @return void
	 * @author Thibaud Rohmer
	 */
	static public function init($forced = false){

		/// Settings already created
		if(Settings::$photos_dir !== NULL && !$forced) return;

		/// Set TimeZone
		date_default_timezone_set("Europe/Paris");

		/// Parse conf.ini file 
		$ini_file		=	realpath(dirname(__FILE__)."/../../conf.ini");

		if(!($ini_settings	=	@parse_ini_file($ini_file))){
			throw new Exception("You need to create a configuration file.");
		}

		/// Setup variables
		Settings::$photos_dir	=	$ini_settings['photos_dir'];
		Settings::$thumbs_dir	=	$ini_settings['ps_generated']."/Thumbs/";
		Settings::$conf_dir		=	$ini_settings['ps_generated']."/Conf/";
		Settings::$admin_settings_file = $ini_settings['ps_generated']."/Conf/admin_settings.ini";


		// Now, check that this stuff exists.
		if(!file_exists(Settings::$photos_dir)){
			if(! @mkdir(Settings::$photos_dir,0750,true)){	
				throw new Exception("PHOTOS dir doesn't exist and couldn't be created !");
			}
		}

		if(!file_exists(Settings::$thumbs_dir)){
			if(! @mkdir(Settings::$thumbs_dir,0750,true)){
				throw new Exception("PS_GENERATED dir doesn't exist or doesn't have the good rights.");
			}
		}

		if(!file_exists(Settings::$conf_dir)){
			if(! @mkdir(Settings::$conf_dir,0750,true)){
				throw new Exception("PS_GENERATED dir doesn't exist or doesn't have the good rights.");
			}
		}

		// Get Admin Settings
		if(file_exists(Settings::$admin_settings_file)){
			$admin_settings = parse_ini_file(Settings::$admin_settings_file);

			if(isset($admin_settings['name'])){
				Settings::$name			=	stripslashes($admin_settings['name']);
			}

			Settings::$like 		=	isset($admin_settings['like']);
			Settings::$plusone 		=	isset($admin_settings['plusone']);
			Settings::$noregister	=	isset($admin_settings['noregister']);
			Settings::$nocomments	=	isset($admin_settings['nocomments']);
			Settings::$nodownload	=	isset($admin_settings['nodownload']);
			Settings::$l33t 		=	isset($admin_settings['l33t']);


			if(isset($admin_settings['max_comments'])){
				Settings::$max_comments = 	$admin_settings['max_comments'] + 0;
			}

			if(isset($admin_settings['max_img_dir'])){
				Settings::$max_img_dir = 	$admin_settings['max_img_dir'] + 0;
			}

			if(isset($admin_settings['loc'])){
				Settings::$loc = $admin_settings['loc'];
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
		$var = array("name","like","plusone","max_comments","noregister","nocomments","nodownload","max_img_dir","loc","l33t");
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

		echo "<h1>".Settings::_("settings","settings")."</h1>";

		echo "<form action='?t=Adm&a=Set' method='post'>\n";
		echo "<fieldset><span>".Settings::_("settings","title")."</span><div><input type='text' name='name' value=\"".htmlentities(Settings::$name, ENT_QUOTES ,'UTF-8')."\"></div></fieldset>\n";

		echo "<fieldset><span>".Settings::_("settings","buttons")."</span><div class='buttondiv'>\n";
		if(Settings::$like){
			echo "<label><input type='checkbox' name='like' checked>".Settings::_("settings","fb")."</label>\n";
		}else{
			echo "<label><input type='checkbox' name='like'>".Settings::_("settings","fb")."</label>\n";
		}

		if(Settings::$plusone){
			echo "<label><input type='checkbox' name='plusone' checked>".Settings::_("settings","plusone")."</label>\n";
		}else{
			echo "<label><input type='checkbox' name='plusone'>".Settings::_("settings","plusone")."</label>\n";
		}

		echo "</div></fieldset>\n";


		echo "<fieldset><span>".Settings::_("settings","register")."</span><div class='buttondiv'>\n";
		if(Settings::$noregister){
			echo "<label><input type='checkbox' name='noregister' checked>".Settings::_("settings","noregister")."</label>\n";
		}else{
			echo "<label><input type='checkbox' name='noregister'>".Settings::_("settings","noregister")."</label>\n";
		}
		echo "</div></fieldset>\n";

		echo "<fieldset><span>".Settings::_("settings","comment")."</span><div class='buttondiv'>\n";
		if(Settings::$nocomments){
			echo "<label><input type='checkbox' name='nocomments' checked>".Settings::_("settings","nocomment")."</label>\n";
		}else{
			echo "<label><input type='checkbox' name='nocomments'>".Settings::_("settings","nocomment")."</label>\n";
		}
		echo "</div></fieldset>\n";

		echo "<fieldset><span>".Settings::_("settings","download")."</span><div class='buttondiv'>\n";
		if(Settings::$nodownload){
			echo "<label><input type='checkbox' name='nodownload' checked>".Settings::_("settings","nodownload")."</label>\n";
		}else{
			echo "<label><input type='checkbox' name='nodownload'>".Settings::_("settings","nodownload")."</label>\n";
		}
		echo "</div></fieldset>\n";



		echo Settings::_("settings","numcomments")."<br/>";
		echo "<fieldset><span>".Settings::_("settings","numcomm")."</span><div><input type='text' name='max_comments' value=\"".htmlentities(Settings::$max_comments, ENT_QUOTES ,'UTF-8')."\"></div></fieldset>\n";

		echo Settings::_("settings","sens")."<br/>";
		echo "<fieldset><span>".Settings::_("settings","images")."</span><div><input type='text' name='max_img_dir' value=\"".htmlentities(Settings::$max_img_dir, ENT_QUOTES ,'UTF-8')."\"></div></fieldset>\n";

		echo "<fieldset><span>".Settings::_("settings","language")."</span><div><select name='loc'>";
		foreach(Settings::$ava_loc as $l){
			$p = htmlentities($l, ENT_QUOTES ,'UTF-8');
			echo "<option value=\"".addslashes($p)."\"";
			if($p == Settings::$loc){
				echo " selected ";
			}
			echo ">".substr($p,0,-4)."</option>";
		}
		echo "</select></div></fieldset>";

		echo "<fieldset><span>l337</span><div class='buttondiv'>\n";
		if(Settings::$l33t){
			echo "<label><input type='checkbox' name='l33t' checked>l337</label>\n";
		}else{
			echo "<label><input type='checkbox' name='l33t'>l337</label>\n";
		}
		echo "</div></fieldset>\n";

		echo "<fieldset><input type='submit' value='".Settings::_("settings","submit")."'/></fieldset>\n";
		echo "</form>\n";


		echo "<h1>".Settings::_("settings","generate")."</h1>";
		echo "<form action='?t=Adm&a=GAl' method='post'>\n";
		echo "<fieldset><span>".Settings::_("settings","folder")."</span><div><select name='path'>";
		echo "<option value='.'>".Settings::_("settings","all")."</option>";
		foreach($this->folders as $f){
			$p = htmlentities(File::a2r($f), ENT_QUOTES ,'UTF-8');
			echo "<option value=\"".addslashes($p)."\">".basename($p)."</option>";
		}
		echo "</select></div></fieldset>";
		echo "<fieldset><input type='submit' value='Generate '/></fieldset>";
		echo "</form>";
	}
}
?>