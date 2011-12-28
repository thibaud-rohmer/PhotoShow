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
 * @link      http://github.com/thibaud-rohmer/PhotoShow
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
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

class ImagePanel implements HTMLObject
{

    /// Header of the html page
    public $page_header;
	
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
		
		if(!isset($file)){
			return;
		}

		/// Create Image object
		$this->image	=	new Image($file);
		
		/// Create Image object
		$this->imagebar	=	new ImageBar($file);

		/// Create EXIF object
		$this->exif		=	new Exif($file);
		
		if(!Settings::$nocomments){
			/// Create Comments object
			$this->comments	=	new Comments($file);
		}

        $pageURL = Settings::$site_address."/?f=".urlencode(File::a2r($file));
        
        // generate the header - opengraph metatags for facebook
        $this->page_header = "<meta property=\"og:url\" content=\"".$pageURL."\"/>\n"
            ."<meta property=\"og:site_name\" content=\"".Settings::$name."\"/>\n"
            ."<meta property=\"og:type\" content=\"image\"/>\n"
            ."<meta property=\"og:title\" content=\"".Settings::$name.": ".File::a2r($file)."\"/>\n"
            ."<meta property=\"og:image\" content=\"".Settings::$site_address."/?t=Thb&f=".urlencode(File::a2r($file))."\"/>\n";

		/// Set the Judge
		$this->judge 	=	new Judge($file);
	}

	/**
	 * Display ImagePanel on website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		if(!isset($this->image)){
			return;
		}

        /*
		echo "<div id='exif' class='box'>\n";
		$this->exif->toHTML();
         */

/*
		if(CurrentUser::$admin){
			$this->judge->toHTML();
		}

		echo "</div>\n";
*/
		echo "<div id='bigimage'>\n";

		$this->image->toHTML();

		echo "</div>\n";

		echo "<div id='image_bar'>\n";
		$this->imagebar->toHTML();
		echo "</div>\n";
/*
		echo "<div id='comments' class='box'>\n";
		if(!Settings::$nocomments){
			$this->comments->toHTML();
		}
		echo "</div>\n";
*/
	}
	
}
?>
