<?php
/**
 * This file implements the class AdminJS.
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
 * AdminJS
 *
 * Do-everything page
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
class AdminJS extends Page
{
	private $dirdiv;

	public function __construct(){
		
		$this->dirdiv = $this->dir2div(Settings::$photos_dir);

	}

	private function dir2div($dir){
		
		$subdirs 	= Menu::list_dirs($dir);
		$res 		= "<li class=' ";
		if(sizeof($subdirs) > 0){
			$res 	.= " has_sub";
		}
		$res 		.= " dir'>";
		
		$res 		.= "
		<div class='title'>	<span id='".urlencode(htmlentities(File::a2r($dir)))."' class='".addslashes(htmlentities(File::a2r($dir)))."'>".basename($dir)."</span></div>
			<ul class='subdirs'>
			";

		foreach($subdirs as $d){
			$res .= $this->dir2div($d);
		}
		$res 		.= "</ul></li>";
		return $res;
	}

	public function toHTML(){
		echo "<div class='folders'>";
		echo "<div class='explanations'>";
		echo " > Click on a folder to open it <br />";
		echo " > Drag'n'drop folders to move them <br />";
		echo " > Either drag'n'drop images on UPLOAD button, or click on it";
		echo "</div>";
		echo $this->dirdiv;
		echo "</div>";
		echo "<div class='infos'>";
		echo "</div>";
		echo "<div class='bin'><img src='inc/bin.png'></div>";

	}
}

?>