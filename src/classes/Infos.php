<?php
/**
 * This file implements the class Infos.
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
 * Used to print the info panel
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
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

class Infos implements HTMLObject
{
	private $info;
	
	private $exif;

	private $header;

	private $comments;

	private $path;

	private $w;

	private $title;

	private $thumb; 

	private $dl;

	private $deleteform ='';

	public function __construct(){

		if(CurrentUser::$admin || CurrentUser::$uploader){
			$this->info = new AdminPanel();
		}
		
		$this->exif = new Exif(CurrentUser::$path);


		if(!Settings::$nocomments){
			$this->comments	=	new Comments(CurrentUser::$path);
		}

		$this->path 	=	urlencode(File::a2r(CurrentUser::$path));
		$this->title 	=	basename(CurrentUser::$path);
		$this->w 		= 	File::a2r(CurrentUser::$path);

		if(is_file(CurrentUser::$path)){
			$this->thumb ="<img src=\"?t=Thb&f=".urlencode(File::a2r(CurrentUser::$path))."\" />";
			$this->dl = "?t=BDl&f=$this->path";
		}else{
			$this->thumb ="<img src='inc/folder.png' />";
			$this->dl = "?t=Zip&f=$this->path";
		}

		if(CurrentUser::$admin){

		$this->deleteform = "<div id='deleteform'><form class='pure-form' action='?a=Del' method='post'>
				<input type='hidden' name='del' value=\"".htmlentities($this->w, ENT_QUOTES ,'UTF-8')."\">
						<button class='button-round button-error' type='submit'><i class='fa fa-trash-o'></i></button>
				</form>
				</div>";
		}
	}

	public function toHTML(){
		
		echo "<div class='infos_img'>";

		echo $this->thumb;
		echo $this->deleteform;
		
		echo "<div class='infos_title'>".htmlentities($this->title, ENT_QUOTES ,'UTF-8')."</div>";
		if(!Settings::$nodownload){
			/// Zip button
			echo 	"<a href='$this->dl' class='floating-action'><i class='fa fa-arrow-down fa-large'></i></a>\n";
		}
		echo "</div>";

		if(CurrentUser::$admin && is_dir(CurrentUser::$path)){
		/// Upload Images form
			echo "<h3>Upload</h3>";
			echo "<div id='files'></div>";
			echo "<form class='dropzone' id=\"".htmlentities($this->w, ENT_QUOTES ,'UTF-8')."\" 
				action='?a=Upl' method='POST' enctype='multipart/form-data'>
				<input type='hidden' name='path' value=\"".htmlentities($this->w, ENT_QUOTES ,'UTF-8')."\">
				<input type='hidden' name='inherit' value='1' />
				<input type='file' name='images[]' multiple >
				<button>Upload</button>
				<div>".Settings::_("adminpanel","upload")."</div>
				</form>";
		}

		if(CurrentUser::$admin || CurrentUser::$uploader ){
		$this->info->toHTML();
		}

		$this->exif->toHTML();

		echo "<div id='comments' class='box'>\n";
		if(!Settings::$nocomments){
			$this->comments->toHTML();
		}
		echo "</div>\n";

		echo "<div id='share'>\n";
		echo 	"<span></span>";
		
		echo "<div>";


		// Outputting Facebook Like Button
		if(Settings::$like){				
			$rootURL = Settings::$site_address;
			$pageURL = $rootURL."/?f=".urlencode($this->w);
			echo '<iframe src="//www.facebook.com/plugins/like.php?href='.$pageURL.'&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>';
		}


		echo "</div>";



		echo 	"</span>\n";

		echo "</div>";

	}

}

?>
