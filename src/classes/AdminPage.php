<?php
/**
 * This file implements the class Admin.
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
 * Admin
 *
 * Aministration panel
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
 class AdminPage extends Page
 {
 	/// Admin page
 	public $page;

 	/// Menu of the Admin page
 	public $menu;

 	/**
 	 * Create admin page
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function __construct(){

 		/// Check that current user is an admin
	 	if(!CurrentUser::$admin){
	 		return;
	 	}

	 	/// Setup admin variables
	 	Admin::init();

	 	/// Create menu
	 	$this->menu = new AdminMenu();

	 	/// So, what to we do ?
	 	switch(Admin::$action){
	 		case "stats"	:	$this->page = new AdminStats();
	 							break;

	 		case "upload"	:	if(isset($_POST['path'])){
	 								AdminUpload::upload();
	 							}
	 							$this->page = new AdminUpload();
	 							break;

	 		default 		:	$this->page = new AdminStats();
	 	}
	}

	 /**
	  * Display admin page
	  * 
	  * @author Thibaud Rohmer
	  */
	public function toHTML(){
		$this->header();

	 	$this->menu->toHTML();
	
		echo "<div class='boards_panel_thumbs'>\n";
	 	$this->page->toHTML();
	 	echo "</div>";
	 
	}
 }

 ?>