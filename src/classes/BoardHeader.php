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

	/**
	 * Create BoardHeader
	 *
	 * @param string $title 
	 * @author Thibaud Rohmer
	 */
	public function __construct($title,$path){
		$this->path 	=	urlencode(File::a2r($path));
		$this->title 	=	$title;
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
		echo 	"<h1>".htmlentities($this->title, ENT_QUOTES ,'UTF-8')."</h1>";
		
		echo 	"<span>";
		
		// Outputting Facebook Like Button
		if(Settings::$like){				
			$rootURL = Settings::$site_address;
			$pageURL = $rootURL."/?f=".urlencode(File::a2r(CurrentUser::$path));
			echo '<iframe src="//www.facebook.com/plugins/like.php?href='.$pageURL.'&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>';
		}

		if(!Settings::$nodownload){
			/// Zip button
			echo 	"<a href='?t=Zip&f=$this->path' class='button'>".Settings::_("boardheader","download")."</a>\n";
		}

		echo 	"</span>\n";

		echo 	"</div>\n";
	}
}

?>