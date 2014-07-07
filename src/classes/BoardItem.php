<?php
/**
 * This file implements the class BoardItem.
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
 * BoardItem
 *
 * Implements the displaying of an item of the grid on
 * the Website.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */
class BoardItem implements HTMLObject
{
	/// URL-encoded relative path to file
	public $file;
	
	/// Path to file
	public $path;

	/// Ratio of the file
	public $ratio;
	
	/// Item width
	public $width;
	
	/**
	 * Construct BoardItem
	 *
	 * @param string $file 
	 * @param string $ratio 
	 * @author Thibaud Rohmer
	 */
	public function __construct($file,$ratio=0){
		$this->path 	= 	$file;
		$this->file		=	urlencode(File::a2r($file));
		$this->ratio	=	$ratio;
	}
	
	/**
	 * Display BoardItem on Website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		
		$getfile =	"t=Thb&f=$this->file";

		echo "<div class='item $lgcls pure-u-1-2 pure-u-sm-1-2 pure-u-md-1-3 pure-u-lg-1-4 pure-u-xl-1-8'>";
		echo 	"<a href='?f=$this->file'>";
		echo 	"<img src='?$getfile'>";
		echo 	"</a>\n";
		echo "</div>\n";

	}



	/**
	 * Calculate width (in percent) of the item : 
	 * 90 * item_ratio / line_ratio
	 *
	 * @param string $r 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function set_width($line_ratio){
		$this->width = 90 * $this->ratio / $line_ratio;
	}
}

?>