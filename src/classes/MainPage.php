<?php
/**
 * This file implements the class MainPage.
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
 * MainPage
 *
 * This is the page containing the Boards.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */

class MainPage extends Page
{
	
	/// True if the image div should be visible
	static private $image_div = false;
	
	/// Boardpanel object
	static private $boardpanel;
	
	/// Menubar object
	static private $menubar;
	
	/// Imagepanel object
	static private $imagepanel;
		
		
	/**
	 * Creates the page
	 *
	 * @author Thibaud Rohmer
	 */
	public function __construct(){	
			
		try{
			$settings=new Settings();
		}catch(FileException $e){
			// If Accounts File missing... Register !
			$this->header();
			new RegisterPage();
			exit;
		}
		
		/// Check how to display current file
		if(is_file(CurrentUser::$path)){
			$this->image_div 	= 	true;
			$this->imagepanel	=	new ImagePanel(CurrentUser::$path);
			$this->boardpanel	=	new BoardPanel(dirname(CurrentUser::$path));
		}else{
			$this->imagepanel	=	new ImagePanel();
			$this->boardpanel	=	new BoardPanel(CurrentUser::$path);
		}

		/// Create MenuBar
		$this->menubar 		= 	new MenuBar();
	}
	
	/**
	 * Display page on the website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		$this->header();
		echo "<body>";
		
		$this->menubar->toHTML();

		echo "<div id='container'>\n";
		
			echo "<div class='layout_boards $this->boards_class'>\n";
			$this->boardpanel->toHTML();
			echo "</div>\n";
		
			if($this->image_div){
				echo "<div class='layout_image'>\n";
			}else{
				echo "<div class='layout_image hidden'>\n";
			}
			$this->imagepanel->toHTML();
			echo "</div>\n";
		
		echo "</div>\n";
		
		echo "</body>";
	}
}

?>