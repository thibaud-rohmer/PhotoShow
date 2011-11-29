<?php
/**
 * This file implements the class JS.
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
 * JS Files
 *
 * Form for editing files. With JS.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
class JSFiles
{
	private $editFiles;

	private $infos;

	private $j;

	public function __construct(){

		$this->j = new Judge(CurrentUser::$path);

		$this->editFiles 	= $this->editFiles();
		$this->infos 		= $this->infodirtoHTML(CurrentUser::$path);
	}


	public function infodirtoHTML($dir){
		$w 	= File::a2r($dir);
		$ret = "";

		/// Folder name
		if(strlen($w)>1){
		$ret .=	"<form class='rename'>
				<fieldset class='".addslashes(htmlentities(File::a2r(dirname($dir))))."'>
					<input id='foldername' class='".addslashes(htmlentities($w))."' type='text' value='".addslashes(htmlentities(basename($w)))."'>
					<input type='submit' value='Rename'>
				</fieldset>
				</form>";
		}
		$ret .=	"<form class='create'>
				<fieldset>
					<input type='hidden' name='path' value='".addslashes(htmlentities($w))."'>
					<input id='foldername' name='newdir' type='text' value='New Folder'>
					<input type='submit' value='Create'>
				</fieldset>
				</form>";

		/// Upload Images form
		$ret .= "<form class='dropzone' id='".addslashes(htmlentities($w))."/' 
			action='?t=Adm&a=Upl&j=Pan' method='POST' enctype='multipart/form-data'>
			<input type='hidden' name='path' value='".addslashes(htmlentities($w))."'>
			<input type='file' name='images[]' multiple >
			<button>Upload</button>
			<div>Upload Images Here</div>
			</form>";

		/// List images
		$ret .= 	"<table id='files'></table>";
		$ret .= 	"<div class='images'>";
		foreach (Menu::list_files($dir) as $img){
			$ret .= "<div class='thmb'><img src='?t=Thb&f=".urlencode(File::a2r($img))."'><span class='".addslashes(htmlentities(File::a2r($img)))."'>".htmlentities(basename($img))."</span></div>";
		}
		$ret .=	"</div>";

		return $ret;

	}

	public function dir2div($dir){
		
		$subdirs 	= Menu::list_dirs($dir);
		$res 		= "<li class=' ";
		if(sizeof($subdirs) > 0){
			$res 	.= " has_sub ";
		}

		try{
			File::a2r(CurrentUser::$path,$dir);
			$res 		.=	" selected ";
		}catch(Exception $e){
			// Do nothing
		}

		$res 		.= " dir'>";
		$res 		.= "
		<div class='title $class'>	<span id='".urlencode(htmlentities(File::a2r($dir)))."' class='".addslashes(htmlentities(File::a2r($dir)))."'>".basename($dir)."</span></div>
			<ul class='subdirs'>
			";

		foreach($subdirs as $d){
			$res .= $this->dir2div($d);
		}
		$res 		.= "</ul></li>";
		return $res;
	}

	public function editFiles(){

		$ret = "<div class='folders'>
				<div class='explanations'>
				 > Click on a folder to open it <br />
				 > Drag'n'drop folders to move them <br />
				 > To delete an element, drag'n'drop it in the bin<br />
				 > Either drag'n'drop images on UPLOAD button, or click on it
				</div>";
		$ret .= $this->dir2div(Settings::$photos_dir);
		$ret .= "</div>
				<div class='bin'><img src='inc/bin.png'></div>";
		return $ret;
	}

	public function toHTML(){
		echo $this->editFiles;
		echo "<div class='infos'>";
		echo $this->infos;
		echo $this->j->toHTML();
		echo "</div>";
	}
}


?>