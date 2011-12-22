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
	private $image_div = false;
	
	/// Boardpanel object
	private $panel;
	
	/// Boards class;
	private $panel_class;

	/// Menubar object
	private $menubar;
	
	/// Imagepanel object
	private $image_panel;

	/// Image_panel class
	private $image_panel_class;

	/// Imagepanel object
	private $menu;

	/// Admin Panel
	private $admin_panel;

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
			$this->image_panel			=	new ImagePanel(CurrentUser::$path);
			$this->image_panel_class 	=	"image_panel";
			$this->panel				=	new Board(dirname(CurrentUser::$path));
			$this->panel_class			=	"linear_panel";
		}else{
			$this->image_panel			=	new ImagePanel();
			$this->image_panel_class	=	"image_panel hidden";
			$this->panel				=	new Board(CurrentUser::$path);
			$this->panel_class			=	"panel";
		}

		/// Create MenuBar
		$this->menubar 		= 	new MenuBar();

		/// Menu
		$this->menu			=	new Menu();

		if(CurrentUSer::$admin){
			$this->admin_panel = new AdminPanel();
		}
		
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

		echo "<div id='container'>\n";		

		$this->menubar->toHTML();

		echo "<div id='page'>\n";

		/// Start menu
		echo "<div id='menu' class='menu'>\n";

		$this->menu->toHTML();

		if(CurrentUser::$admin){
			echo "<div class='bin'><img src='inc/bin.png'> Supprimer</div>";
		}
		echo "</div>\n";
		/// Stop menu

		/// Start Panel
		echo "<div class='$this->panel_class'>\n";
		$this->panel->toHTML();
		echo "</div>\n";
		/// Stop Panel

		/// Start ImagePanel
		echo "<div class='$this->image_panel_class'>\n";
		$this->image_panel->toHTML();
		echo "</div>\n";
		/// Stop ImagePanel
		
		if(CurrentUser::$admin){
			echo "<div class='infos'>\n";
			$this->admin_panel->toHTML();
			echo "</div>\n";
		}
		echo "</div>\n";
		
		echo "</div>\n";
		
		echo "</body>";
	}
}

?>
