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
	 * Create Asynchrone Execution (compatibles Linux/Windows)
	 *
	 * @param string $file
     * @return pid of the executed command (only linux)
	 * @author C�dric Levasseur/Franck Royer
	 */
	public function ExecInBackground($cmd) {
		error_log('DEBUG/Video: Background Execution : '.$cmd,0);
        $pid = 0;
		if (substr(php_uname(), 0, 7) == "Windows"){
		   pclose(popen('start /b '.$cmd.' 2>&1', 'r'));
		} else {
		    exec($cmd . " > /dev/null 2>&1 & echo $!", $output);
            $pid = intval($output[0]);
		}
        return $pid;
	}

	/**
	 * Compute the dimension of a video using ffmpeg
	 *
	 * @return the dimension in a array of int
	 * @author Franck Royer
	 */
	public function AutoRotateImage($file){

			if(!File::Type($file) || File::Type($file) != "Image"){
					return;
			}
			if(Settings::rotate_image){
				exec("`which exiftran ` -ai ".escapeshellarg($file)." 2>&1", $output);
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
		echo 	"<img src='inc/img.png' style='opacity:0;' alt=\"\">";
		echo 	"</a>";
		echo	"</div>";
	}
}

?>
