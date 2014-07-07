<?php
/**
 * This file implements the class Image.
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
 * Image
 *
 * The image is displayed in the ImagePanel. This file
 * implements its displaying.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

class Image implements HTMLObject
{
	/// URLencoded version of the relative path to file
	static public $fileweb;
	
	/// URLencoded version of the relative path to directory containing file
	private $dir;
	
	/// Width of the image
	private $x;
	
	/// Height of the image
	private $y;

	/// Force big image or not
	private $t;
	
	
	/**
	 * Create image
	 *
	 * @param string $file 
	 * @author Thibaud Rohmer
	 */
	public function __construct($file=NULL,$forcebig = false){
		
		/// Check file type
		if(!isset($file) || !File::Type($file) || File::Type($file) != "Image")
			return;
		
		/// Set relative path (url encoded)
		$this->fileweb	=	urlencode(File::a2r($file));
		
		/// Set relative path to parent dir (url encoded)
		$this->dir	=	urlencode(dirname(File::a2r($file)));
		
		/// Get image dimensions
		list($this->x,$this->y)=getimagesize($file);

		/// Set big image
		if($forcebig){
			$this->t = "Big";
		}else{
			$this->t = "Img";

			if($this->x >= 1200 || $this->y >= 1200){
				if ($this->x > $this->y){
					$this->x = 1200;
				}else{
					$this->x = $this->x * 1200 / $this->y;
				}
			}
		}
	}
	
	
	/**
	 * Display the image on the website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		echo 	"<div id='image_big' ";
		echo 	"style='";
		echo 		" background: black url(\"?t=".$this->t."&f=$this->fileweb\") no-repeat center center;";
		echo 		" background-size: contain;";
		echo 		" -moz-background-size: contain;";
		echo 		" height:100%;";
		echo 	"';>";

		echo "<input type='hidden' id='imageurl' value='?t=Big&f=$this->fileweb'>";
		echo 	"<a href='?f=$this->dir'>"; 
		echo 	"<img src='inc/img.png' height='100%' width='100%' style='opacity:0;'>";
		echo 	"</a>";
		echo	"</div>";
	}
}

?>
