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
		$res 		= "<div class='";
		if(sizeof($subdirs) > 0){
			$res 	.= " has_sub";
		}
		$res 		.= " dir'>";
		
		$res 		.= "<div class='title'><form class='dropzone' id='".htmlentities(File::a2r($dir))."/' action='?t=Adm&a=Upl' method='POST' enctype='multipart/form-data'>
			<input type='hidden' name='path' value='".htmlentities(File::a2r($dir))."'>
			<input type='file' name='images[]' multiple >
			<button>Upload</button>
			</form>
			<span id='".htmlentities(File::a2r($dir))."'>".basename($dir)."</span></div><div class='subdirs'>
			";

		foreach($subdirs as $d){
			$res .= $this->dir2div($d);
		}
		$res 		.= "</div></div>";
		return $res;
	}

	public function toHTML(){
		echo "<div class='toolbar'><div>ToolBar</div>
				<div class='newdir'><div class='title'><div class='dropzone'></div><span>New Folder</span></div></div>
		</div>";
		echo $this->dirdiv;

		echo "<table id='files'></table>";
	}
}

?>