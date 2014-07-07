<?php
/**
 * This file implements the class ImageBar.
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
 * ImageBar
 *
 * The ImageBar contains some buttons insanely awesome
 * buttons, incredibly usefull. Yeah, it rocks.
 * 
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */
class ImageBar
{

	/// Buttons to display
	private $buttons = array();

	private $awesome = array();

	/// True if photosphere
	private $photosphere = false;

	/**
	 * Create the ImageBar
	 * 
	 * @author Thibaud Rohmer
	 */
	public function __construct($fs=false){

		$file = urlencode(File::a2r(CurrentUser::$path));

		$this->photosphere = (basename(dirname(CurrentUser::$path)) == "PhotoSpheres");

		$this->buttons['prev'] = 	"?p=p&f=".$file;
		$this->awesome['prev'] = 	"<i class='fa fa-chevron-left fa-lg'></i>";
		
		$this->buttons['back'] = 	"?f=".urlencode(File::a2r(dirname(CurrentUser::$path)));
		$this->awesome['back']  = 	"<i class='fa fa-reply fa-lg'></i>";

		if(!Settings::$nodownload){
			$this->buttons['img']  = 	"?t=Big&f=".$file;
			$this->awesome['img']  = 	"<i class='fa fa-eye fa-lg'></i>";

			$this->buttons['get']  = 	"?t=BDl&f=".$file;
			$this->awesome['get']  = 	"<i class='fa fa-download fa-lg'></i>";
		}

		$this->buttons['slideshow'] = 	"?f=".$file;
		$this->awesome['slideshow'] = 	"<i class='fa fa-youtube-play fa-lg'></i>";

		if($this->photosphere){
			$this->buttons['pshere'] =   "#' id='photosphere";
			$this->awesome['pshere'] =   "<img height='20px' src='inc/photosphere_logo.png'/>";
		}

		$this->buttons['next'] = 	"?p=n&f=".$file;
		$this->awesome['next'] = 	"<i class='fa fa-chevron-right fa-lg'></i>";

		$this->buttons['pause'] = 	"?f=".$file;
		$this->awesome['pause'] = 	"<i class='fa fa-pause fa-lg'></i>";

		$this->buttons['play'] = 	"?f=".$file;
		$this->awesome['play'] = 	"<i class='fa fa-play fa-lg'></i>";

		$this->buttons['stop'] = 	"?f=".$file;
		$this->awesome['stop'] = 	"<i class='fa fa-stop fa-lg'></i>";



	}

	/**
	 * Display ImageBar on Website
	 * 
	 * @author Thibaud Rohmer
	 */
	 public function toHTML(){
	 	foreach($this->buttons as $name=>$url){
	 		echo "<span id='$name'><a href='$url'>".$this->awesome[$name]."</a></span>";
	 	}
	 }

}

?>