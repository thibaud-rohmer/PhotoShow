<?php
/**
 * This file implements the class BoardHeader.
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
 * BoardHeader
 *
 * Well... It contains the title and some buttons.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */
class BoardHeader{

	/// Name of the directory listed in parent Board
	public $title;
	
	/// Path of the directory listed in parent Board
	public $path;

	/// Current Working Directory
	private $w;

	/**
	 * Create BoardHeader
	 *
	 * @param string $title 
	 * @author Thibaud Rohmer
	 */
	public function __construct($title,$path){
		$this->path 	=	urlencode(File::a2r($path));
		$this->title 	=	$title;
		$this->w 		= 	File::a2r(CurrentUser::$path);
	}
	
	/**
	 * Display BoardHeader on Website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		echo 	"<div class='header'>";
		/// Title

	if(CurrentUser::$admin){
		echo 	"<h1><div class='box'>";
		echo 	"<form class='rename' action='?a=Mov' method='post'>
					<input type='hidden' name='move' value='rename'>
					<input type='hidden' name='pathFrom' value=\"".htmlentities($this->w, ENT_QUOTES ,'UTF-8')."\">
				<fieldset>
					<input type='text' name='pathTo' value=\"".htmlentities(basename($this->w), ENT_QUOTES ,'UTF-8')."\">
					<input type='submit' value='".Settings::_("adminpanel","rename")."'>
				</fieldset>
				</form>";
		echo 	"</div>";

		echo 	"<div class='box'><form class='create' action='?a=Upl' method='post'>
					<fieldset>
						<input type='hidden' name='path' value=\"".htmlentities($this->w, ENT_QUOTES ,'UTF-8')."\">
						<input id='foldername' name='newdir' type='text' value='".Settings::_("adminpanel","new")."'>
						<input type='submit' value='".Settings::_("adminpanel","create")."'>
					</fieldset>
					</form>
					</div></h1>";

	}else{
		echo 	"<h1>".htmlentities($this->title, ENT_QUOTES ,'UTF-8')."</h1>";
	}
		


		echo 	"<span>";
		
		echo "<div>";
		// Outputting Facebook Like Button
		if(Settings::$like){				
			$rootURL = Settings::$site_address;
			$pageURL = $rootURL."/?f=".urlencode($this->w);
			echo '<iframe src="//www.facebook.com/plugins/like.php?href='.$pageURL.'&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>';
		}

		if(CurrentUser::$admin){
			echo "<input type='submit' id='multiselectbutton' value='".Settings::_("adminpanel","multiselect")."'>";
		}

		if(!Settings::$nodownload){
			/// Zip button
			echo 	"<a href='?t=Zip&f=$this->path' class='button'>".Settings::_("boardheader","download")."</a>\n";
		}
		echo "</div>";

		if(CurrentUser::$admin){
		/// Upload Images form
			echo "<div id='files'></div>";
			echo "<form class='dropzone' id=\"".htmlentities($this->w, ENT_QUOTES ,'UTF-8')."\" 
				action='?a=Upl' method='POST' enctype='multipart/form-data'>
				<input type='hidden' name='path' value=\"".htmlentities($this->w, ENT_QUOTES ,'UTF-8')."\">
				<input type='file' name='images[]' multiple >
				<button>Upload</button>
				<div>".Settings::_("adminpanel","upload")."</div>
				</form>";
		}

		echo 	"</span>\n";



		echo 	"</div>\n";
	}
}

?>