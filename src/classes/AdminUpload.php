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
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
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
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
 class AdminUpload
 {
 	/// Directories where we can upload
 	public $dirs = array();

 	/// What have we done ?
 	public $done;

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

 	}

 	/**
 	 * Upload files on the server
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function upload(){

 		$allowedExtensions = array("tiff","jpg","jpeg","gif","png");

 		/// Just to be really sure... 
 		if(!CurrentUser::$admin){
 			return;
 		}

 		/// Set upload path
 		$path = File::r2a($_POST['path']);
 		
 		/// Create dir and update upload path if required
 		if(strlen($_POST['newdir'])>0 && !strpos($_POST['newdir'],'..')){

 			$path = $path."/".$_POST['newdir'];
 			if(!file_exists($path)){
 				mkdir($path,0750,true);
 			}

 		}

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
		    		$done .= "Successfully uploaded $name";
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
 		echo 	"<div class='title'>Upload</div>";
 		echo 	"<form action='#' method='post' enctype='multipart/form-data' class='niceform'>";
 		echo 	"<table>";
 		echo 	"<tr><td><input  name='images[]' type='file' multiple /></td></tr>";
 		echo 	"<tr><td><select name='path'>";
 		echo 	"<option value='.'>.</option>";
 		foreach($this->dirs as $dir){
 				echo "<option value='$dir'>".$dir."</option>\n";
 		}
 		echo 	"</select></tr></td>";
 		echo 	"<tr><td>Create Dir : <input name='newdir' type='text' /></td></tr>";
 		echo 	"<tr><td><input type='submit' class='button blue' /></td></tr>";
 		echo 	"</table>";
 		echo 	"</form>";

 	}

 }
 ?>