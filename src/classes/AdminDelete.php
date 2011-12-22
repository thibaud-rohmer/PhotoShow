<?php
/**
 * This file implements the class AdminDelete.
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
 * AdminDelete
 *
 * Delete page
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
 class AdminDelete
 {

 	/// Directories where we can upload
 	public $files = array();

 	/// What have we done ?
 	public $done;

 	/// Currently selected dir/file
 	private $selected;

 	/**
 	 * Create upload page
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function __construct(){

 		/// Get all subdirs
 		$list_dirs = Menu::list_dirs(Settings::$photos_dir,true);

 		/// Get all subfiles
 		$list_files = Menu::list_files(Settings::$photos_dir,true);


 		foreach ($list_dirs as $dir){
 			$this->files[] 	= File::a2r($dir);
 		}

 		foreach ($list_files as $file){
 			$this->files[] = File::a2r($file);
 		}

 		if(isset(CurrentUser::$path)){
 			$this->selected = File::a2r(CurrentUser::$path);
 		}
 	}

 	/**
 	 * Delete files on the server
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function delete(){

 		/// Just to be really sure... 
 		if(!CurrentUser::$admin){
 			return;
 		}

 		$del 	=	File::r2a(stripslashes($_POST['del']));
 		if($del == Settings::$photos_dir){
 			return;
 		}

 		return 	AdminDelete::rec_del($del);
	}

	/**
	 * Reccursively delete all files in $dir
	 * 
	 * @param string $dir
	 * @author Thibaud Rohmer
	 */
	public function rec_del($dir){
		if(is_file($dir)){
			return unlink($dir);
		}

		$dirs 	=	Menu::list_dirs($dir);
		$files 	= 	Menu::list_files($dir,false,true);

		foreach($dirs as $d){
			AdminDelete::rec_del($d);
		}
		
		foreach($files as $f){
			unlink($f);
		}

		return rmdir($dir);
	}


 	/**
 	 * Display upload page on website
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function toHTML(){
 		echo 	"<h1>Delete</h1>";
 		echo 	"<form action='?t=Adm&a=Del' method='post' enctype='multipart/form-data'>";
 		echo 	"<fieldset><span>Supprimer</span><div><select name='del'>";
		foreach($this->files as $file){
 				if($file == $this->selected){
 					$selected = "selected";
 				}else{
 					$selected = "";
 				}
 				echo "<option value='".htmlentities($file)."' $selected>".htmlentities($file)."</option>\n";
 		}

 		echo 	"</select></div></fieldset>\n";

 		echo 	"<fieldset><input type='submit' /></fieldset>";
 		echo 	"</form>";

 	}

 }
 ?>
