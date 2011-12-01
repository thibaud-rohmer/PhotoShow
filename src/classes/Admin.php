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
 		switch($_GET['a']){
	 		case "Sta"		:	$this->page = new AdminStats();
	 							break;

	 		case "Acc"		:	if(isset($_POST['old_password'])){
									Account::edit($_POST['login'],$_POST['old_password'],$_POST['password'],$_POST['name'],$_POST['email']);
								}
								if(isset($_POST['login'])){
									$this->page = new Account($_POST['login']);
								}else{
									$this->page = CurrentUser::$account;
								}

								break;

			case "GC"		:	Group::create($_POST['group']);
								$this->page = new JSAccounts();
								break;
			
			case "AGA"		:	$a = new Account($_POST['acc']);
								$a->add_group($_POST['group']);
								$a->save();
								$this->page = CurrentUser::$account;
								break;

			case "AGR"		:	$a = new Account($_POST['acc']);
								$a->remove_group($_POST['group']);
								$a->save();
								$this->page = CurrentUser::$account;
								break;

			case "ADe"		:	Account::delete($_POST['name']);
								$this->page = new JSAccounts();
								break;

			case "GDe"		:	Group::delete($_POST['name']);
								$this->page = new JSAccounts();
								break;

			case "CDe"		:	CurrentUser::$path = File::r2a($_POST['image']);
								Comments::delete($_POST['image'],$_POST['date']);
								$this->page = new MainPage();
								break;

	 		case "Upl"		:	if(isset($_POST['path'])){
	 								AdminUpload::upload();
	 								CurrentUser::$path = File::r2a($_POST['path']);
	 							}
	 							$this->page = new AdminFiles();
	 							break;
			
			case "Mov"		:	if(isset($_POST['pathFrom'])){
									try{
 										CurrentUser::$path = File::r2a(dirname($_POST['pathFrom']));	
									}catch(Exception $e){
										CurrentUser::$path = Settings::$photos_dir;
									}
								}
 								AdminMove::move();
 								if(isset($_POST['move']) && $_POST['move']=="rename"){
									try{
 										CurrentUser::$path = dirname(File::r2a($_POST['pathFrom']))."/".$_POST['pathTo'];	
									}catch(Exception $e){
										CurrentUser::$path = Settings::$photos_dir;
									}
								}
	 							
								$this->page = new AdminFiles();
								break;

			case "Del"		:	if(isset($_POST['del'])){
	 								CurrentUser::$path = dirname(File::r2a($_POST['del']));
	 								AdminDelete::delete();
	 							}
								$this->page = new AdminFiles();
								break;

			case "Fil"		:	$this->page = new AdminFiles();
								break;

			case "JS"		:	break;

			case "EdA"		:	$this->page = new JSAccounts();
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
		echo "<div class='menu'>\n";
 		$this->menu->toHTML();
 		echo "</div>\n";

		echo "<div class='panel'>\n";
		if($_GET['a']=="JS"){
			$this->page = new JS();
		}else{
		 	$this->page->toHTML();			
		}
	 	echo "</div>";
	 
	}

 }

 ?>