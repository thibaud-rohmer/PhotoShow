<?php
/**
 * This file implements the class CurrentUser.
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
 * CurrentUser
 *
 * Stores the information of the currently logged user.
 * Implements login and logout function.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */

class CurrentUser
{
	///	Current user account
	public static $account;
	
	/// Bool : true if current user is an admin
	public static $admin;
	
	/// Current path requested by the user
	public static $path;
	
	/// Current type of stuff requested by user (Page / Zip / Image)
	public static $action = "Page";
	
	/// Type of page to display
	public static $page;
	
	/// File containing users info
	static public $accounts_file;
	
	/// File containing groups info
	static public $groups_file;

	/// Is this a JS query ?
	static public $js = false;

	/**
	 * Retrieves info for the current user account
	 *
	 * @author Thibaud Rohmer
	 */
	static public function init(){
		
		CurrentUser::$accounts_file =	Settings::$conf_dir."/accounts.xml";
		
		CurrentUser::$groups_file	=	Settings::$conf_dir."/groups.xml";
		

		/// Set path
		if(isset($_GET['f'])){			
			CurrentUser::$path = stripslashes(File::r2a($_GET['f']));
		
			if(isset($_GET['p'])){
				switch($_GET['p']){
					case 'n':	CurrentUser::$path = File::next(CurrentUser::$path);
								break;
					case 'p':	CurrentUser::$path = File::prev(CurrentUser::$path);
								break;
				}
			}
		
		}else{
			/// Path not defined in URL
			CurrentUser::$path = Settings::$photos_dir;
		}


		/// Set CurrentUser account
		if(isset($_SESSION['login'])){
				CurrentUser::$account	=	new Account($_SESSION['login']);
				CurrentUser::$admin = in_array("root",CurrentUser::$account->groups);
		}

		/// Set action (needed for page layout)
		if(isset($_GET['t'])){
			switch($_GET['t']){
				
				case "Page"	:
				case "Img"	:
				case "Thb"	:
				case "Big"	:
				case "Zip"	:	CurrentUser::$action=$_GET['t'];
								break;
				
				case "Reg"	:	if(isset($_POST['login']) && isset($_POST['password'])){
									if(!Account::create($_POST['login'],$_POST['password'])){
										echo "Erreur de cr&eacute;ation du compte.";
									}
								}

				case "Log"	:	if(isset($_SESSION['login'])){
									CurrentUser::logout();
									echo "D&eacute;connect&eacute;";
									break;
								}

								if(isset($_POST['login']) && isset($_POST['password'])){
									try{
										if(!CurrentUser::login($_POST['login'],$_POST['password'])){
											echo "Mauvais mot de passe";
										}
									}catch(Exception $e){
										echo "Compte non trouv&eacute;";
									}
								}
								
								if(!isset(CurrentUser::$account)){
									CurrentUser::$action = $_GET['t'];
								}
						
								break;

				case "Acc"	:	if(isset($_POST['old_password'])){
									Account::edit($_POST['login'],$_POST['old_password'],$_POST['password'],$_POST['name'],$_POST['email']);
								}
								CurrentUser::$action = "Acc";
								break;

				case "Adm"	:	if(CurrentUser::$admin){
									CurrentUser::$action = "Adm";
								}
								break;

				case "Com"	:	Comments::add(CurrentUser::$path,$_POST['content'],$_POST['login']);
								break;

				case "Rig"	:	Judge::edit(CurrentUser::$path,$_POST['users'],$_POST['groups'],true);
								CurrentUser::$action = "Judge";
								break;
				
				case "Pub"	:	Judge::edit(CurrentUser::$path);
								CurrentUser::$action = "Judge";
								break;

				case "Pri"	:	Judge::edit(CurrentUser::$path,array(),array(),true);
								CurrentUser::$action = "Judge";
								break;

				case "Inf" 	:	CurrentUser::$action = "Inf";
								break;

				default		:	CurrentUser::$action = "Page";
								break;
			}
		}else{
			CurrentUser::$action = "Page";
		}

		if(isset($_GET['a']) && CurrentUser::$action != "Adm"){
			if(CurrentUser::$admin){
				new Admin();
			}
		}

		if(isset($_GET['j'])){
			CurrentUser::$action =	"JS";
		}


		/// Set default action
		if(!isset(CurrentUser::$action)){
			CurrentUser::$action = "Page";
		}

		/// Throw exception if accounts file is missing
		if(!file_exists(CurrentUser::$accounts_file)){
			throw new Exception("Accounts file missing",69);
		}
		
		/// Create Group File if it doesn't exist
		if(!file_exists(CurrentUser::$groups_file)){
			Group::create_group_file();
		}

		if(isset(CurrentUser::$account)){
			CurrentUser::$admin = in_array("root",CurrentUser::$account->groups);
		}
	}
	
	/**
	 * Log the user in
	 *
	 * @param string $login User login
	 * @param string $password User password
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function login($login,$password){

		CurrentUser::$admin	=	false;
		
		$acc =	new Account($login);
	
		// Check password
		if(Account::password($password) == $acc->password){
			$_SESSION['login']		=	$login;
			CurrentUser::$account	=	$acc;
		}else{
			// Wrong password
			return false;			
		}
		if(in_array('root',$acc->groups))
			CurrentUser::$admin = true;
		
		return true;
	}
	
	/**
	 * Log the user out
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function logout(){
		CurrentUser::$account	= NULL;
		CurrentUser::$admin 	= false;
		session_unset();
	}

}
?>
