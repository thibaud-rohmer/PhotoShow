<?php
/**
 * This file implements the class AdminUpload.
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
 * AdminUpload
 *
 * Upload page
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */
 class AdminUpload
 {
 	/// Directories where we can upload
 	public $dirs = array();

 	/// What have we done ?
 	public $done;

 	/// Currently selected dir
 	private $selected_dir;

 	/**
 	 * Create upload page
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function __construct(){

 		/// Get all subdirs
 		$list_dirs = Menu::list_dirs(Settings::$photos_dir,true);

 		foreach ($list_dirs as $dir){
 			$this->dirs[] = File::a2r($dir);
 		}

 		if(isset(CurrentUser::$path)){
 			$this->selected_dir = File::a2r(CurrentUser::$path);
 		}

 	}

 	/**
 	 * Upload files on the server
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function upload(){

 		$allowedExtensions = array("tiff","jpg","jpeg","gif","png");
		
		/// Just to be really sure ffmpeg is enabled - necessary to generate thumbnail jpg and webm
		if (Settings::$encode_video) {
			array_push($allowedExtensions,"flv","mov","mpg","mp4","ogv","mts","3gp","webm");
		}

		$already_set_rights = false;

 		/// Just to be really sure... 
 		if( !(CurrentUser::$admin || CurrentUser::$uploader) ){
 			return;
 		}

 		/// Set upload path
 		$path = stripslashes(File::r2a($_POST['path']));
 		
 		/// Create dir and update upload path if required
 		if(strlen(stripslashes($_POST['newdir']))>0 && !strpos(stripslashes($_POST['newdir']),'..')){

 			$path = $path."/".stripslashes($_POST['newdir']);
 			if(!file_exists($path)){
 				@mkdir($path,0750,true);
 				@mkdir(File::r2a(File::a2r($path),Settings::$thumbs_dir),0750,true);
 			}

 			/// Setup rights
 			if(!isset($_POST['inherit'])){
 				if(isset($_POST['public'])){
 					Judge::edit($path);
 				}else{
 					Judge::edit($path,$_POST['users'],$_POST['groups']);					
 				}
 			}
 			$already_set_rights = true;
 		}
		if(!isset($_FILES["images"])) return;
 		/// Treat uploaded files
 		foreach ($_FILES["images"]["error"] as $key => $error) {

			// Check that file is uploaded
		    if ($error == UPLOAD_ERR_OK) {

				// Name of the stored file
		        $tmp_name = $_FILES["images"]["tmp_name"][$key];
		
				// Name on the website
		        $name = $_FILES["images"]["name"][$key];
				
				$info = pathinfo($name);
				$base_name =  basename($name,'.'.$info['extension']);
		
				// Check filetype
				if(!in_array(strtolower($info['extension']),$allowedExtensions)){
					continue;
				}
				
				// Rename until this name isn't taken
				$i=1;
				while(file_exists("$path/$name")){
					$name=$base_name."-".$i.".".$info['extension'];
					$i++;
				}

				// Save the files
		        if(move_uploaded_file($tmp_name, "$path/$name")){
		    	//	$done .= "Successfully uploaded $name";
				Video::FastEncodeVideo("$path/$name");
		        }

		        /// Setup rights
	 			if(!$already_set_rights && !isset($_POST['inherit'])){
 					if(isset($_POST['public'])){
 						Judge::edit("$path/$name");
 					}else{
 						Judge::edit("$path/$name",$_POST['users'],$_POST['groups']);					
 					}
 				}
			}
		}
	}

 	/**
 	 * Display upload page on website
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function toHTML(){
 		echo 	"<h1>Upload</h1>";

 		echo 	"<form action='?t=Adm&a=Upl' method='post' enctype='multipart/form-data'>";
 		echo 	"<fieldset><span>Images</span><div><input  name='images[]' type='file' multiple /></div></fieldset>";
 		echo 	"<fieldset><span>Location</span><div><select name='path'>";
 		echo 	"<option value='.'>.</option>";

 		foreach($this->dirs as $dir){
 				if($dir == $this->selected_dir){
 					$selected = "selected";
 				}else{
 					$selected = "";
 				}
 				echo "<option value='".htmlentities($dir, ENT_QUOTES ,'UTF-8')."' $selected>".htmlentities($dir, ENT_QUOTES ,'UTF-8')."</option>\n";
 		}

 		echo 	"</select></div></fieldset>";
 		echo 	"<fieldset><span>New Dir</span><div><input name='newdir' type='text' /></div></fieldset>";
 	 	echo 	"<fieldset><span>Rights</span><div><label><input type='checkbox' name='inherit' checked /> Inherit</label></div></fieldset>";
 		echo 	"<fieldset><span>Public</span><div><label><input type='checkbox' name='public' checked /> Public</label></div></fieldset>";
 		echo 	"<fieldset><span>Groups</span><div>";
 		foreach(Group::findAll() as $group){
 			echo "<label><input type='checkbox' name='groups[]' value='".htmlentities($group['name'], ENT_QUOTES ,'UTF-8')."' checked /> ".htmlentities($group['name'], ENT_QUOTES ,'UTF-8')." </label>";
 		}
 		echo 	"</div></fieldset>";
 	
 		echo 	"<fieldset><span>Users</span><div>";
 		foreach(Account::findAll() as $account){
 			echo "<label><input type='checkbox' name='users[]' value='".htmlentities($account['login'], ENT_QUOTES ,'UTF-8')."' checked /> ".htmlentities($account['login'], ENT_QUOTES ,'UTF-8')." </label>";
 		}
 		echo 	"</div></fieldset>";
 		echo 	"<fieldset><input type='submit' class='button blue' /></fieldset>";

 		echo 	"</form>";

 	}

 }
 ?>