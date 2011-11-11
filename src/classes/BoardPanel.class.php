<?php
/**
 * This file implements the class BoardPanel.
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
 * BoardPanel
 *
 * Implements the displaying of multiple components:
 * a Menu, and a Board.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
class BoardPanel
{
	/// Board to display
	private $board;
	
	/// Menu to display
	private $menu;
	
	/// Menu class depending on layout (image|boards)
	private $menu_class;
	
	/// Boards panel class depending on layout (image|boards)
	private $boards_class;
	
	/**
	 * Create BoardPanel
	 *
	 * @param string $dir 
	 * @author Thibaud Rohmer
	 */
	public function __construct($dir){
		if(isset($_SESSION['max_images'])){
			$max_images=$_SESSION['max_images'];
		}
		
		$settings=new Settings();

		/// Board
		$this->board	=	new Board($dir);
		
		/// Menu
		$this->menu		=	new Menu($settings->photos_dir);
		
		/// Check layout
		if(is_file(CurrentUser::$path)){
			$this->boards_class	=	"boards_panel_image";
			$this->menu_class 	=	"hidden"; 	
		}else{
			$this->boards_class 	=	"boards_panel_thumbs";
			$this->menu_class 		=	"";
		}
	}

	/**
	 * Display BoardPanel on website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		
		/// Display Menu
		echo "<div id='menu' class='$this->menu_class'>\n";
		$this->menu->toHTML();
		echo "</div>\n";

		/// Display Boards
		echo "<div id='boards_panel' class='$this->boards_class'>\n";
		$this->board->toHTML();
		echo "</div>\n";
	}
}