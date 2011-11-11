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
	

	
	
}
?>