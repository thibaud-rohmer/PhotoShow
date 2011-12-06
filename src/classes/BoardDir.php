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
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
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
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
class BoardDir implements HTMLObject
{
	/// URL-encoded relative path to dir
	public $url;
	
	/// Path to dir
	public $path;

	/// Images representing the dir
	public $images;
	
	/**
	 * Construct BoardItem
	 *
	 * @param string $file 
	 * @param string $ratio 
	 * @author Thibaud Rohmer
	 */
	public function __construct($dir,$img=array()){
		$this->path 	= 	$dir;
		$this->url		=	urlencode(File::a2r($dir));
		if(sizeof($img) == 0){
			echo "plip";
			$this->images 	= array();
		}else{
			$this->images	=	$img;
		}
	}
	
	/**
	 * Display BoardItem on Website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		
		if(sizeof($this->images) > 0){
			$getfile =	"t=Thb&f=".urlencode(File::a2r($this->images[0]));
		}else{
			$getfile = 	"";
		}			
		
		/// We display the image as a background
		echo 	"<div class='directory'>";

		echo 	"<span class='name hidden'>".htmlentities(basename($this->path), ENT_QUOTES ,'UTF-8')."</span>";
		echo 	"<span class='path hidden'>".htmlentities(File::a2r($this->path), ENT_QUOTES ,'UTF-8')."</span>";

		echo 	"<div class='dir_img'";
		echo 	" style='";
		echo 	" background: 		url(\"?$getfile\") no-repeat center center;";
		echo 	" -webkit-background-size: cover;";
		echo 	" -moz-background-size: cover;";
		echo 	" -o-background-size: cover;";
		echo 	" background-size: 	cover;";
		echo 	"'>\n";
		echo 	"<span class='img_bg hidden'></span>";

		/// Images in the directory
		if( sizeof($this->images) > Settings::$max_img_dir ){
			for($i=0;$i < Settings::$max_img_dir;$i++){
				
				$pos = floor(sizeof($this->images) *  $i / Settings::$max_img_dir );
				
				if(Judge::view($this->images[$pos])){
					echo "<div class='alt_dir_img hidden'>".urlencode(File::a2r($this->images[$pos]))."</div>";
				}

			}
		}else{
			foreach($this->images as $img){
				if(Judge::view($img)){
					echo 	"<div class='alt_dir_img hidden'>".urlencode(File::a2r($img))."</div>";
				}
			}
		}
		echo 	"<a href='?f=$this->url'>";
		echo 	"<img src='./inc/img.png' width='100%' height='100%'>";
		echo 	"</a>\n";
		echo 	"</div>\n";
		echo 	"<div class='dirname'>";

		if(!(CurrentUser::$admin || CurrentUser::$uploader)){
			echo 	htmlentities(basename($this->path), ENT_QUOTES ,'UTF-8');			
		}else{
			$w 	= File::a2r($this->path);

			echo 	"<form class='rename' action='?a=Mov' method='post'>
					<input type='hidden' name='move' value='rename'>
					<input type='hidden' name='pathFrom' value=\"".htmlentities($w, ENT_QUOTES ,'UTF-8')."\">
					<input type='text' name='pathTo' value=\"".htmlentities(basename($w), ENT_QUOTES ,'UTF-8')."\">
				</form>";

		}

		echo 	"</div>\n";
		echo 	"</div>\n";
	}
}

?>