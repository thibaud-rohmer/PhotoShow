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
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
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
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
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

	/// Website name
	static public $name="PhotoShow";

	/// Display Facebook button
	static public $like=false;

	/// Display Google button
	static public $plusone=false;

	/// Max number of comments
	static public $max_comments=50;

	/// Folders list
	private $folders=array();

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

		if(file_exists(Settings::$admin_settings_file)){
			$admin_settings = parse_ini_file(Settings::$admin_settings_file);

			if(isset($admin_settings['name'])){
				Settings::$name			=	stripslashes($admin_settings['name']);
			}

			Settings::$like 		=	isset($admin_settings['like']);
			Settings::$plusone 		=	isset($admin_settings['plusone']);

			if(isset($admin_settings['max_comments'])){
				Settings::$max_comments = 	$admin_settings['max_comments'] + 0;
			}
		}
	}

	public static function set(){
		$var = array("name","like","plusone","max_comments");
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
		echo "<form action='?t=Adm&a=Set' method='post'>\n";
		echo "<fieldset><span>Title</span><div><input type='text' name='name' value=\"".htmlentities(Settings::$name, ENT_QUOTES ,'UTF-8')."\"></div></fieldset>\n";

		echo "<fieldset><span>Buttons</span><div class='buttondiv'>\n";
		if(Settings::$like){
			echo "<label><input type='checkbox' name='like' checked>Facebook</label>\n";
		}else{
			echo "<label><input type='checkbox' name='like'>Facebook</label>\n";
		}

		if(Settings::$plusone){
			echo "<label><input type='checkbox' name='plusone' checked>Google +1</label>\n";
		}else{
			echo "<label><input type='checkbox' name='plusone'>Google +1</label>\n";
		}

		echo "</div></fieldset>\n";


		echo "<fieldset><span>Comments</span><div><input type='text' name='max_comments' value=\"".htmlentities(Settings::$max_comments, ENT_QUOTES ,'UTF-8')."\"></div></fieldset>\n";

		echo "<fieldset><input type='submit' /></fieldset>\n";
		echo "</form>\n";

		echo "<h1>Generate all thumbnails and 800x600 images (recursively)</h1>";
		echo "<form action='?t=Adm&a=GAl' method='post'>\n";
		echo "<fieldset><span>Folder</span><div><select name='path'>";
		echo "<option value='.'>All</option>";
		foreach($this->folders as $f){
			$p = htmlentities(File::a2r($f), ENT_QUOTES ,'UTF-8');
			echo "<option value=\"".addslashes($p)."\">$p</option>";
		}
		echo "</select></div></fieldset>";
		echo "<fieldset><input type='submit' value='Generate '/></fieldset>";
		echo "</form>";
	}
}
?>