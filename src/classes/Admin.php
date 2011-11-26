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
 class Admin extends Page
 {
 	/// Admin page
 	public $page;

 	/// Menu of the Admin page
 	public $menu;

 	/// Admin action
 	static public $action = "stats";

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

	 	/// Create menu
	 	$this->menu = new AdminMenu();
 		/// Get action
	 	if(isset($_GET['a'])){
	 		switch($_GET['a']){
		 		case "Sta"	:	$this->page = new AdminStats();
		 							break;

		 		case "Upl"	:		if(isset($_POST['path'])){
		 								AdminUpload::upload();
		 							}
		 							$this->page = new AdminUpload();
		 							break;
		 			
		 		case "Acc"	:		if(isset($_POST['old_password'])){
										Account::edit($_POST['login'],$_POST['old_password'],$_POST['password'],$_POST['name'],$_POST['email']);
									}
									if(isset($_POST['login'])){
										$this->page = new Account($_POST['login']);
									}else{
										$this->page = CurrentUser::$account;
									}

									break;
				
				case "Mov"		:	if(isset($_POST['pathFrom'])){
		 								AdminMove::move();
		 							}
									$this->page = new AdminMove();
									break;

				case "Del"		:	if(isset($_POST['del'])){
		 								AdminDelete::delete();
		 							}
									$this->page = new AdminDelete();
									break;

				case "JS"		:	$this->page = new AdminJS();
									break;

		 		default 		:	$this->page = new AdminStats();
	 		}
	 	}else{
	 		$this->page = new AdminStats();
		}

	 	if(CurrentUser::$js == 1){
	 		$this->page = new AdminJS();
	 	}
	}

	 /**
	  * Display admin page
	  * 
	  * @author Thibaud Rohmer
	  */
	public function toHTML(){
		$this->header();
		if(!CurrentUser::$js){
			echo "<div class='menu'>\n";
	 		$this->menu->toHTML();
	 		echo "</div>\n";
		}
		echo "<div class='panel'>\n";
	 	$this->page->toHTML();
	 	echo "</div>";
	 
	}

 }

 ?>