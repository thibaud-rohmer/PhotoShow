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
	
	/// TestInfo , containing the title and explain
	public $textinfo;	

	/**
	 * Create BoardHeader
	 *
	 * @param string $title 
	 * @author Thibaud Rohmer
	 */
	public function __construct($title,$path){
		$this->path 	=	urlencode(File::a2r($path));
		$this->title 	=	$title;
		$this->textinfo 	=	new TextInfo($path);
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
		if ($this->textinfo->title) { 
			echo 	"<h1>".$this->textinfo->title."</h1>";
		} else { 
			echo 	"<h1>".(htmlentities($this->title, ENT_QUOTES ,'UTF-8'))."</h1>";	
		}
		
			if(CurrentUser::$admin){
			/// Edit button
			echo 	"<span>\n";
			echo 	"<div id='edit_textinfo'><a href='#' class='button'>".Settings::_("textinfo","edit")."</a></div>\n";
			echo 	"</span>\n";
		}		
		
		if(!Settings::$nodownload){
			/// Zip button
			echo 	"<span>\n";
			echo 	"<a href='?t=Zip&f=$this->path' class='button'>".Settings::_("boardheader","download")."</a>\n";
			echo 	"</span>\n";
		}
		
		echo 	"</div>\n";
		$this->textinfo->toHTML();
	}
}

?>