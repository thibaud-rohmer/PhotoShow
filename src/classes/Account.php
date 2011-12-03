<?php
/**
 * This file implements the class Account.
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package	  PhotoShow
 * @category  Website
 * @author	  Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright 2011 Thibaud Rohmer
 * @license	  http://www.gnu.org/licenses/
 * @link	  http://github.com/thibaud-rohmer/PhotoShow-v2
 */

/**
 * Account
 *
 * Implements functions to work with a User account.
 * Read the account from the Accounts XML file,
 * edit it, and save it.
 * 
 * Account Structure:
 * - Login
 * - Name
 * - Password (Encryption : sha1)
 * - Email
 * - Groups -> Group names
 *
 * 
 * @package	  PhotoShow
 * @category  Website
 * @author	  Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license	  http://www.gnu.org/licenses/
 * @link	  http://github.com/thibaud-rohmer/PhotoShow-v2
 */
class Account extends Page
{
	/// Login of the user
	public $login;

	/// Password of the user, encrypted via sha1
	public $password;

	/// Name of the user (optional)
	public $name;

	/// Email of the user (optional)
	public $email;

	/// List of the groups the user is in. No duplicates. Minimum list : array("user")
	public $groups;
	
	/**
	 * Find account in the base.
	 *
	 * @param string $login 
	 * @author Thibaud Rohmer
	 */
	public function __construct($login=NULL){
		if(!isset($login))
			return;
		
		$xml_infos	=	CurrentUser::$accounts_file;
		$xml		=	simplexml_load_file($xml_infos);

		// Look each account
		foreach( $xml as $account ){
			if((string)$account->login == $login){
				$this->login	= (string)$account->login;
				$this->password = (string)$account->password;
				$this->name		= (string)$account->name;
				$this->email	= (string)$account->email;
				$this->groups 	= array();

				foreach($account->groups->children() as $group){
					$this->groups[] = (string)$group;
				}

				return;
			}
		}
		//throw new Exception("Login $login not found");
	}
	
	/**
	 * Creates a new account in the base
	 *
	 * @param string $login 
	 * @param string $password 
	 * @author Thibaud Rohmer
	 */ 
	public static function create($login,$password,$groups=array(),$name='',$email=''){
		
		// Check if login already exists
		if(Account::exists($login))
			return false;

		// All users belong to the "user" group
		$groups[]="user";
		
		$xml_infos=CurrentUser::$accounts_file;
		
		if(!file_exists($xml_infos) || sizeof(Account::findAll()) == 0 ){
			// No account

			// Create accounts file
			$xml	=	new SimpleXMLElement('<accounts></accounts>');
			$xml->asXML($xml_infos);
			
			// Set this account as root
			$groups[] = "root";
		}


		if( !(preg_match("/^[A-Z][a-zA-Z -]+$/", $login) === 0) || strlen($password) < 6){
			return false;
		}

		$acc			=	new Account();
		$acc->login		=	$login;
		$acc->password	=	Account::password($password);
		$acc->groups	=	$groups;
		$acc->name		=	$name;
		$acc->email		=	$email;
		$acc->save();
		return true;
	}
	
	/**
	 * Encrypt password
	 *
	 * @param string $password 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function password($password){
		return sha1($password);
	}
		
	
	/**
	 * Add a group to this user
	 *
	 * @param string $group 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function add_group($group){
		// Check that this user doesn't already belong to this group
		if(!in_array($group,$this->groups)){
			$this->groups[]=$group;
			$g = new Group($group);
			$g->save();
		}
	}
	
	/**
	 * Remove a group from this user
	 *
	 * @param string $group 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function remove_group($group){
		// Check that this user belongs to this group
		if(in_array($group,$this->groups)){
			$id=array_search($group,$this->groups);
			unset ( $this->groups[$id] );
		}
	}

	/**
	 * Save account in the base
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function save(){

		$xml_infos	=	CurrentUser::$accounts_file;
		$xml		=	simplexml_load_file($xml_infos);
			
		foreach( $xml as $acc ){
			if((string)$acc->login == $this->login){
				$account=$acc;
				break;
			}
		}

		if(isset($account)){		
			$account->password	=	$this->password;
			$account->name		=	$this->name;
			$account->email		=	$this->email;
			unset($account->groups);
		}else{
			$account=$xml->addChild('account');
			$account->addChild(		'login' ,		$this->login);
			$account->addChild(		'password', $this->password);
			$account->addChild(		'name'	,		$this->name);
			$account->addChild(		'email' ,		$this->email);
		}
		// Create the groups
		$groups = $account->addChild('groups');
		foreach($this->groups as $group){
			$groups->addChild('group',$group);
			
			try{
				$g	=	new Group($g);
				$g->save();
			}catch(Exception $e){
				// This group already exists
			}
		}
		// Saving into file
		$xml->asXML($xml_infos);
	}

	/**
	 * Edit an account
	 * 
	 * @param string $login
	 * @param string $old_password
	 * @param string $password
	 * @param string $name
	 * @param string $email
	 * @author Thibaud Rohmer
	 */
	public static function edit($login=NULL, $old_password=NULL, $password=NULL, $name=NULL, $email=NULL, $groups=array()){
		
		/// Only the admin can modify other accounts
		if( $login != CurrentUser::$account->login ){
			return;
		}

		if(isset($login) && (preg_match("/^[A-Z][a-zA-Z -]+$/", $login) === 0) ){
			$acc = new Account($login);
		}else{
			$acc = CurrentUser::$account;
		}

		/// Check password
		if( Account::password($old_password) != $acc->password ){
			return;
		}

		/// Edit attributes
		if(isset($password) && sizeof($password) > 4 ){
			$acc->password = Account::password($password);
		}

		if(isset($name)){
			$acc->name = $name;
		}

		if(isset($email)){
			$acc->email = $email;
		}

		if(CurrentUser::$admin && sizeof($groups) > 0){
			$acc->groups = $groups;
		}

		/// Save account
		$acc->save();
	}
	
	/**
	 * Delete an account
	 *
	 * @param string $login 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function delete($login){
		$xml_infos 	=	CurrentUser::$accounts_file;
		$xml		=	simplexml_load_file($xml_infos);
		
		$i=-1;
		$found = false;
		foreach( $xml as $acc ){
			$i++;
			if((string)$acc->login == $login){
				$found = true;
				continue;
			}
		}
		
		if($found){
			unset($xml->account[$i]);
		}

		$xml->asXML($xml_infos);
	}

	/**
	 * Check if an account already exists
	 *
	 * @param string $login
	 * @return bool
	 * @author Thibaud Rohmer
	 */
	public static function exists($login){

		// Check if the accounts file exists
		if(!file_exists(CurrentUser::$accounts_file)){
			return false;
		}
		$xml_infos	=	CurrentUser::$accounts_file;
		$xml		=	simplexml_load_file($xml_infos);

		foreach( $xml as $account ){
			if((string)$account->login == $login)
				return true;
		}
	
		return false;
	}
	
	
	/**
	 * Returns an array containing all accounts
	 *
	 * @return array $accounts
	 * @author Thibaud Rohmer
	 */
	public static function findAll(){
		$accounts	=	array();
		
		$xml_infos	=	CurrentUser::$accounts_file;
		$xml		=	simplexml_load_file($xml_infos);

		foreach( $xml as $account ){
			$new_acc=array();
			
			$new_acc['login']		= $account->login;
			$new_acc['password']	= $account->password;
			$new_acc['name']		= $account->name;
			$new_acc['email']		= $account->email;
			$new_acc['groups']		= array();
			foreach($account->groups->children() as $group){
				$new_acc['groups'][]= $group;
			}

			$accounts[]=$new_acc;
		}
		
		return $accounts;
	}
	
	/**
	 * Returns the rights of an account
	 *
	 * @param string $login 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function rights($login){
		$rights =	array();

		$xml_infos	=	CurrentUser::$accounts_file;
		$xml		=	simplexml_load_file($xml_infos);

		foreach( $xml as $account ){
			if($account->login==$login){
				foreach($account->groups->children() as $group){
					$rights=array_unique(array_merge($rights,Group::rights($group)));
				}
			}
		}

		return $rights;
	}

	/**
	 * Display a form to edit account
	 * 
	 * 
	 */
	 public function toHTML(){
	 	$this->header();
	 	echo "<div class='panel'>\n";
	 	echo "<h1>Account</h1>\n";

		echo "Editing account ".htmlentities($this->login, ENT_QUOTES ,'UTF-8');
	 	echo "<form method='post' action='#'>\n";
	 	echo "<input type='hidden' value='".htmlentities($this->login, ENT_QUOTES ,'UTF-8')."' name='login' />\n";
	 	echo "<fieldset><span>Name </span><div><input type='text' value='".htmlentities($this->name, ENT_QUOTES ,'UTF-8')."' name='name' /></div></fieldset>\n";
	 	echo "<fieldset><span>Email </span><div><input type='text' value='".htmlentities($this->email, ENT_QUOTES ,'UTF-8')."' name='email' /></div></fieldset>\n";
	 	echo "<fieldset><span>Password </span><div><input type='password' value='' name='password' /></div></fieldset>\n";

 		echo "<fieldset><label>Old Password : <input type='password' value='' name='old_password' /></fieldset>\n";

	 	echo "<input type='submit' class='button blue'>\n";
	 	echo "or <a href='.'>Cancel</a>";
	 	echo "</form>\n";
	 	echo "</div>\n";
	 }

}


?>
