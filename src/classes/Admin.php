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
 * @link      http://github.com/thibaud-rohmer/PhotoShow
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
 * @link      http://github.com/thibaud-rohmer/PhotoShow
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

 		/// Check that current user is an admin or an uploader
	 	if( !(CurrentUser::$admin || CurrentUser::$uploader) ){
	 		return;
	 	}


 		/// Get actions available for Uploaders too
 		if(isset($_GET['a'])){
	 		switch($_GET['a']){
	 			case "Abo" 		: 	$this->page = new AdminAbout();
	 								break;
	 								
		 		case "Upl"		:	if(isset($_POST['path'])){
		 								AdminUpload::upload();
		 								CurrentUser::$path = File::r2a(stripslashes($_POST['path']));
		 							}
		 							$this->page = new AdminFiles();
		 							break;
				
				case "Mov"		:	if(isset($_POST['pathFrom'])){
										try{
	 										CurrentUser::$path = File::r2a(dirname(stripslashes($_POST['pathFrom'])));	
										}catch(Exception $e){
											CurrentUser::$path = Settings::$photos_dir;
										}
									}
	 								AdminMove::move();
	 								
	 								if(isset($_POST['move']) && $_POST['move']=="rename"){
										try{
								//			if(is_dir(File::r2a(stripslashes($_POST['pathFrom'])))){
	 							//				CurrentUser::$path = dirname(File::r2a(stripslashes($_POST['pathFrom'])))."/".stripslashes($_POST['pathTo']);	
	 							//			}
										}catch(Exception $e){
											CurrentUser::$path = Settings::$photos_dir;
										}
									}
		 							
									$this->page = new AdminFiles();
									break;

				case "Del"		:	if(isset($_POST['del'])){
		 								CurrentUser::$path = dirname(File::r2a(stripslashes($_POST['del'])));
		 								AdminDelete::delete();
		 							}
									$this->page = new AdminFiles();
									break;
	 		}
	 	}

 		/// Check that current user is an admin
	 	if( !(CurrentUser::$admin) ){
	 		return;
	 	}

 		/// Get action
 		if(isset($_GET['a'])){
	 		switch($_GET['a']){
		 		case "Sta"		:	$this->page = new AdminStats();
		 							break;

		 		case "VTk"		:	$this->page = new GuestToken();
		 							break;

				case "DTk" 		:	if(isset($_POST['tokenkey'])){
										GuestToken::delete($_POST['tokenkey']);
									}
									$this->page = new GuestToken();
									break;

		 		case "Acc"		:	if(isset($_POST['edit'])){
										Account::edit($_POST['login'],$_POST['old_password'],$_POST['password'],$_POST['name'],$_POST['email'],NULL,$_POST['language']);
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

				case "AAc"		:	Account::create($_POST['login'],$_POST['password'],$_POST['verif']);
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

				case "Fil"		:	$this->page = new AdminFiles();
									break;

				case "JS"		:	break;

				case "EdA"		:	$this->page = new JSAccounts();
									break;
				
				case "GAl"		:	if(isset($_POST['path'])){
										Settings::gener_all(File::r2a(stripslashes($_POST['path'])));
									}
				case "Set" 		:	if(isset($_POST['name'])){
										Settings::set();
									}
									$this->page = new Settings();
									break;
									
				case "Tis"	:		if(isset($_POST['f'])){
										TextInfo::Save_File($_POST['f'],$_POST['title'],$_POST['author'],$_POST['contain']);
									}
									break;	
									
				case "Tid"	:		if(isset($_POST['f'])){
										TextInfo::Delete_File($_POST['f']);
									}
									break;									
		 		}
		}
		
		if(!isset($this->page)){
			$this->page = new AdminStats();			
		}

	 	/// Create menu
	 	$this->menu = new AdminMenu();

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

		echo "<div class='center'>\n";
		if($_GET['a']=="JS"){
			$this->page = new JS();
		}else{
		 	$this->page->toHTML();			
		}
	 	echo "</div>";
	 
	}

 }

 ?>
