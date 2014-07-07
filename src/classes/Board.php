<?php
/**
 * This file implements the class Board.
 * 
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package	   PhotoShow
 * @author	   Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright  2011 Thibaud Rohmer
 * @license	   http://www.gnu.org/licenses/
 * @link	   http://github.com/thibaud-rohmer/PhotoShow
 */

/**
 * Board
 *
 * Lists the content of a directory and displays
 * it on a grid.
 * It implements a grid generating algorithm, and
 * outputs its content in a div of class board
 * via the toHTML() function.
 *
 * @package	   PhotoShow
 * @author	   Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright  Thibaud Rohmer
 * @license	   http://www.gnu.org/licenses/
 * @link	   http://github.com/thibaud-rohmer/PhotoShow
 */
class Board implements HTMLObject
{
	/// Board title : name of the directory listed
	private $title;
    
    /// Header
    public $header_content;
	
	/// Path to listed directory
	private $path;
	
	/// Paths to the files in the directory
	private $files;
	
	/// Paths to the directories in the directory
	private $dirs;
	
	/// Board header, containing the title and some buttons
	private $header;
	
	/// Array of each line of the grid
	private $boarditems=array();

	/// Array of the folders
	private $boardfolders=array();

	/**
	 * Board constructor
	 *
	 * @param string $path 
	 * @author Thibaud Rohmer
	 */
	public function __construct($path=NULL){
		
		if(!isset($path)){
			$path = CurrentUser::$path;
		}

		$this->analyzed=array();
		$this->path=$path;

		// If $path is a file, list directory containing the file
		if(is_file($path)){
			$this->path		=	dirname($path);
		}
		
		$this->title	=	basename($this->path);
		$this->header	=	new BoardHeader($this->title,$this->path);
		$this->files	=	Menu::list_files($this->path);
		$this->dirs		=	Menu::list_dirs($this->path);

        $pageURL = Settings::$site_address."/?f=".urlencode(File::a2r($this->path));
        
        // generate the header - opengraph metatags for facebook
        $this->page_header = "<meta property=\"og:url\" content=\"".$pageURL."\"/>\n"
            ."<meta property=\"og:site_name\" content=\"".Settings::$name."\"/>\n"
            ."<meta property=\"og:type\" content=\"article\"/>\n"
            ."<meta property=\"og:title\" content=\"".Settings::$name.": ".File::a2r($this->path)."\"/>\n";
        if (Settings::$fbappid){
            $this->page_header .= "<meta property=\"fb:app_id\" content=\"".Settings::$fbappid."\"/>\n";
        }

        if (!empty($this->files))
        {
            $i = 0;
            foreach($this->files as $file){
                if ( $i > 9){
                    break;
                }
                if (Judge::is_public($file))
                {
                    $this->page_header .= "<meta property=\"og:image\" content=\"".Settings::$site_address."/?t=Thb&f=".urlencode(File::a2r($file))."\"/>\n";
                    $i++;
                }
            }
        }else{ // No files in the directory, getting thumbnails from sub-directories
            $i = 0;
            foreach($this->dirs as $d){
                if ( $i > 9){
                    break;
                }
                $img = Judge::searchDir($d, true);
                if ($img)
                {
                    $this->page_header .= "<meta property=\"og:image\" content=\"".Settings::$site_address."/?t=Thb&f=".urlencode(File::a2r($img))."\"/>\n";
                    $i++;
                }
            }
        }
		
		$this->foldergrid();
	}
	

	/**
	 * Generate the grid, line by line
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	private function grid($type="Image"){
		$this->boarditems =	array();
		
		foreach($this->files as $file){
			// Check rights
			if(!(Judge::view($file))){
				continue;
			}
			
			// Check filetype
			if (File::Type($file) != $type){
				continue;
			}

			$this->boarditems[] = new BoardItem($file);
		}
	}

	/**
	 * Generate a foldergrid
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	private function foldergrid(){
		foreach($this->dirs as $d){
			$firstImg = Judge::searchDir($d);
			if(!$firstImg){
				if(CurrentUser::$admin){
					$firstImg = NULL;
				}else{
					continue;
				}
			}


			$item = new BoardDir($d,$firstImg);
			$this->boardfolders[] = $item;

		}
		if(Settings::$reverse_menu){
			$this->boardfolders = array_reverse($this->boardfolders);
		}
	}


	/**
	 * Display board on website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){		
		// Output header
		$this->header->toHTML();

		if(sizeof($this->boardfolders)>0){
			echo "<div class='section sectiondir'>";
			echo "<h2>".Settings::_("board","albums")."</h2>";
			echo "<div class='pure-g'>";
			foreach($this->boardfolders as $boardfolder){
				$boardfolder->toHTML();
			}
			echo "</div>";
			echo "</div>";
		}

		$this->grid("Image");
		if(sizeof($this->boarditems)>0){
			echo "<div class='section sectionimg'>";
			echo "<h2>".Settings::_("board","images")."</h2>";
			echo "<div class='pure-g line'>";
			foreach($this->boarditems as $item){
				$item->toHTML();
			}
			echo "</div>";
			echo "</div>";

		}


		$this->grid("Video");
		if(sizeof($this->boarditems)>0){
			echo "<div class='section sectionvid'>";
			echo "<h2>".Settings::_("board","videos")."</h2>";
			echo "<div class='pure-g'>";
			// Output grid
			foreach($this->boarditems as $item){
				$item->toHTML();
			}
			echo "</div>";
			echo "</div>";
		}

		
	}
	
}
