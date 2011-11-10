<?php
/*
    This file is part of PhotoShow.

    PhotoShow is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PhotoShow is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Class for the current logged user
 *
 * @package default
 * @author Thibaud Rohmer
 */
class CurrentUser
{
	public static $account;
	public static $admin;
	public static $path;
	
	/**
	 * Retrieves info for the current user account
	 *
	 * @author Thibaud Rohmer
	 */
	public function init(){
		if(!isset($account)){
			if(!isset($_SESSION['login']))
				throw new Exception('No user is logged');
			else
				$account	=	new Account($_SESSION['login']);
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
		$admin	=	false;
		$acc 	=	new Account($login);
		
		// Check password
		if(sha1($password) == $login->password){
			$_SESSION['login']	=	$login;
			$account			=	$acc;
		}else{
			// Wrong password
			throw Exception("Wrong password.");			
		}
		if(in_array('root',$account))
			$admin=true;
	}
	
	/**
	 * Log the user out
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function logout(){
		unset($_SESSION);
	}
	
	/**
	 * Returns true if the user may access this file
	 *
	 * @param string $f file to access
	 * @return bool
	 * @author Thibaud Rohmer
	 */
	public static function view($f){
		// Account not set
		if($account == NULL){
			try{
				self::init();
			}catch(Exception $e){
				// User is not logged in
				$judge	=	new Judge($f);
				return($judge->public);
			}
		}
		
		if(!File::a2r($f))
			return false;
		
		// No Judge required for the admin.
		if($admin)
			return true;

		// Create Judge
		$judge	=	new Judge($f);
		
		// Public file
		if($judge->public)
			return true;
		
		// User allowed
		if(in_array($account->login,$judge->users))
			return true;
			
		// User in allowed group
		foreach($account->groups as $group){
			if(in_array($group,$judge->groups))
				return true;
		}

		return false;
	}
	
	
}
?>