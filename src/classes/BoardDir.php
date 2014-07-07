<?php
/**
 * This file implements the class BoardDir.
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
 * BoardDir
 *
 * Implements the displaying of directory on the grid of
 * the Website.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */
class BoardDir implements HTMLObject
{
	/// URL-encoded relative path to dir
	public $url;
	
	/// Path to dir
	public $path;

	/// Images representing the dir
	public $images;

	/// URL for loading the image
	private $img;
	
	/**
	 * Construct BoardItem
	 *
	 * @param string $file 
	 * @param string $ratio 
	 * @author Thibaud Rohmer
	 */
	public function __construct($dir,$img){
		$this->path 	= 	$dir;
		$this->url		=	urlencode(File::a2r($dir));
		if($img == NULL){
			$this->img='inc/folder.png';
		}else{
					$this->img = "?t=Thb&f=".urlencode(File::a2r($img));

		}
	}
	
	/**
	 * Display BoardItem on Website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		echo "<div class=' pure-u-1-3 pure-u-sm-1-3 pure-u-lg-1-4 pure-u-xl-1-8'>";
		echo "<div class='directory'>";
		echo 	"<a href=\"?f=$this->url\">";
		echo 	"<img src=\"$this->img\"/ >";
		echo "<div class='dirname'>";
		(array)$name = explode('/', $this->path);
		echo 	htmlentities(end($name), ENT_QUOTES ,'UTF-8');
		echo "</div>";
		echo 	"</a>\n";
		echo "</div>\n";
		echo "</div>";

	}
}

?>
