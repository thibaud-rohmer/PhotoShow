<?php
/**
 * This file implements the class AdminPanel.
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
 * Admin Panel
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */
class AdminPanel
{

	private $infos;

	private $j;

	private $isfile = false;

	public function __construct(){

		$file = CurrentUser::$path;
		if(is_file($file)){
			$this->isfile = true;
		}

		$this->j = new Judge($file);

		$this->infos 		= $this->infodirtoHTML($file);
	}


	public function infodirtoHTML($dir){
		$w 	= File::a2r($dir);
		$ret = "";

		/// Folder name
		if(strlen($w)>1){
		$ret .=	"<form class='rename' action='?a=Mov' method='post'>
					<input type='hidden' name='move' value='rename'>
					<input type='hidden' name='pathFrom' value=\"".htmlentities($w, ENT_QUOTES ,'UTF-8')."\">
				<fieldset>
					<input type='text' name='pathTo' value=\"".htmlentities(basename($w), ENT_QUOTES ,'UTF-8')."\">
					<input type='submit' value='".Settings::_("adminpanel","rename")."'>
				</fieldset>
				</form>";
		}

		if(!($this->isfile)){
			$ret .=	"<form class='create' action='?a=Upl' method='post'>
					<fieldset>
						<input type='hidden' name='path' value=\"".htmlentities($w, ENT_QUOTES ,'UTF-8')."\">
						<input id='foldername' name='newdir' type='text' value='".Settings::_("adminpanel","new")."'>
						<input type='submit' value='".Settings::_("adminpanel","create")."'>
					</fieldset>
					</form>";

			/// Upload Images form
			$ret .= "<div id='files'></div><form class='dropzone' id=\"".htmlentities($w, ENT_QUOTES ,'UTF-8')."\" 
				action='?a=Upl' method='POST' enctype='multipart/form-data'>
				<input type='hidden' name='path' value=\"".htmlentities($w, ENT_QUOTES ,'UTF-8')."\">
				<input type='file' name='images[]' multiple >
				<button>Upload</button>
				<div>".Settings::_("adminpanel","upload")."</div>
				</form>";
		}
		return $ret;

	}

	public function toHTML(){
		echo '<h2>Infos</h2>';
		echo $this->infos;
		
		if(CurrentUser::$admin){
			echo $this->j->toHTML();
		}
	}
}


?>