<?php
/**
 * This file implements the class CurrentUser.
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
 * CurrentUser
 *
 * Stores the information of the currently logged user.
 * Implements login and logout function.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
class JS
{
	
	public function __construct(){
		switch(CurrentUser::$action){
			case "Page":		if(is_file(CurrentUser::$path)){
										$b = new ImagePanel(CurrentUser::$path);
									//$this->script_load("image_panel");
									$b->toHTML();
								}else{
									$b = new Board(CurrentUser::$path);
									//$this->script_load("panel");
									$b->toHTML();
								}
								break;

			case "Adm":			$page = new Admin();
								if( !isset($_POST['path']) || isset($_POST['newdir']) ){
									$page->toHTML();									
								}
								break;

			case "Inf":			$this->infodirtoHTML(CurrentUser::$path);
								break;

			case "Judge":		$j = new Judge(CurrentUser::$path);
								$j->toHTML();
								break;
		}

	}


	private function infodirtoHTML($dir){
		/// Folder name
		echo	"<form><fieldset><input id='foldername' type='text' value='".htmlentities(basename($dir))."'><input type='submit'></form></fieldset></span>";

		/// Upload Images form
		echo "<form class='dropzone' id='".htmlentities(File::a2r($dir))."/' 
			action='?t=Adm&a=Upl&j=1' method='POST' enctype='multipart/form-data'>
			<input type='hidden' name='path' value='".htmlentities(File::a2r($dir))."'>
			<input type='file' name='images[]' multiple >
			<button>Upload</button>
			<div>Upload Images Here</div>
			</form>";

		/// List images
		echo 	"<table id='files'></table>";
		echo 	"<div class='images'>";
		foreach (Menu::list_files($dir) as $img){
			echo "<div class='thmb'><img src='?t=Thb&f=".urlencode(File::a2r($img))."'><span class='".addslashes(htmlentities(File::a2r($img)))."'>".htmlentities(basename($img))."</span></div>";
		}
		echo 	"</div>";

		$j = new Judge($dir);
		$j->toHTML();
	}
}


?>