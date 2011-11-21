<?php
/**
 * This file implements the class ImagePanel.
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
 * ImagePanel
 *
 * The ImagePanel contains one image, and the infos
 * about that image (such as EXIF, Comments).
 * If the user is logged, it contains even more stuff.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */

class ImagePanel implements HTMLObject
{
	
	/// Image object
	private $image;
	
	/// Exif object
	private $exif;
	
	/// Comments object
	private $comments;
	
	/// Judge object
	private $judge;


	/**
	 * Create ImagePanel
	 *
	 * @param string $file 
	 * @author Thibaud Rohmer
	 */
	public function __construct($file=NULL){
		
		/// Create Image object
		$this->image	=	new Image($file);
		
		/// Create EXIF object
		$this->exif		=	new Exif($file);
		
		/// Create Comments object
		$this->comments	=	new Comments($file);

		if($file != NULL){
			/// Set the Judge
			$this->judge 	=	new Judge($file);
		}
	}

	/**
	 * Display ImagePanel on website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		
		echo "<div id='top'>\n";
		echo "<div id='exif' class='box'>\n";
		$this->exif->toHTML();

		if(CurrentUser::$admin){
			$this->judge->toHTML();
		}

		echo "</div>\n";

		echo "<div id='center'>\n";
		$this->image->toHTML();
		echo "</div>\n";

		$this->comments->toHTML();

		echo "</div>\n";
	}
	
}
?>