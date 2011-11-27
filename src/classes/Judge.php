<?php
/**
 * This file implements the class Judge.
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
 * Judge
 *
 * The Judge verifies the rights of Current User, and checks
 * if he is allowed to reach some content. No one fools the
 * Judge. After all, the Judge is the Law.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */

class Judge
{
	/// Absolute path to rights file for requested file
	public $path;
	
	/// True if requested file is public
	public $public;
	
	/// Groups allowed to see requested file
	public $groups=array();
	
	/// Users allowed to see requested file
	public $users=array();
	
	/// Name of requested file
	public $filename;

	/// Urlencoded relative path
	public $webpath;

	/**
	 * Create a Judge for a specific file.
	 *
	 * @param string $f 
	 * @param string $read_rights 
	 * @author Thibaud Rohmer
	 */
	public function __construct($f, $read_rights=true){
		$this->public	=	true;
		$this->groups	=	array();
		$this->users	=	array();
		
		$this->set_path($f);
		
		if($read_rights)
			$this->set_rights();
	}
	
	/**
	 * Get path to rights file associated to our file
	 *
	 * @param string $f 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	private function set_path($f){
		
		$basefile	= 	new File($f);
		$basepath	=	File::a2r($f);

		$this->filename = $basefile->name;
		$this->webpath 	= urlencode($basepath);

		if(is_file($f)){
			$rightsfile	=	dirname($basepath)."/.rights_".$basefile->name.".xml";
		}else{
			$rightsfile	=	$basepath."/.rights.xml";
		}
		$this->path =	File::r2a($rightsfile,Settings::$thumbs_dir);
	}
	
	/**
	 * Get rights (recursively) for the file
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	private function set_rights(){

		/// First, parse the rights file (if it exists)
		try{
			$xml_infos	=	new File($this->path);
			$xml		=	simplexml_load_file($this->path);

			$this->public	=	($xml->public == 1);

			foreach($xml->groups->children() as $g)
				$this->groups[]=(string)$g;

			foreach($xml->users->children() as $u)
				$this->users[]=(string)$u;

		}catch(Exception $e){
		
			/// If no rights file found, check in the containing directory
			try{

				// Look up
				$up_path		=	File::a2r(dirname($base));
				$j = new Judge($up);
				
				$this->groups 	= $j->groups;
				$this->users 	= $j->users;
				$this->public 	= $j->public;

			}catch(Exception $e){
				
				// We are as high as possible
				$this->public	=	true;
				$this->groups	=	array();
				$this->users	=	array();		
			}
		}
	}
	
	/**
	 * Save our judge for this file as an xml file
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function save(){
		
		/// Create xml
		$xml		=	new SimpleXMLElement('<rights></rights>');
		
		/// Put values in xml
		$xml->addChild('public',$this->public);
		$xml_users	=	$xml->addChild('users');
		$xml_groups	=	$xml->addChild('groups');

		foreach($this->users as $user)
			$xml_users->addChild("login",$user);

		foreach($this->groups as $group)
			$xml_groups->addChild("group",$group);
		
		if(!file_exists(dirname($this->path))){
			mkdir(dirname($this->path),0750,true);
		}
		/// Save xml
		$xml->asXML($this->path);
	}
	
	/**
	 * Edit rights of the Judge. Because you can.
	 *
	 * @param string $f 
	 * @param string $groups 
	 * @param string $users 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function edit($f,$users=array(),$groups=array(),$private=false){

		/// Just to be sure, check that user is admin
		if(!CurrentUser::$admin)
			return;

		// Create new Judge, no need to read its rights
		$rights			=	new Judge($f,false);

		/// Put the values in the Judge (poor guy)
		if(isset($groups)){
			$rights->groups =	$groups;
		}

		if(isset($users)){
			$rights->users =	$users;
		}
		
		$rights->public	=	( !$private && (sizeof($groups)==0 && sizeof($users)==0) ) ? 1 : 0;
		
		// Save the Judge
		$rights->save();
	}
	
	/**
	 * Returns true if the file to access is in the sub-path of the main directory
	 *
	 * @param string $f file to access
	 * @return bool
	 * @author Thibaud Rohmer
	 */
	public static function inGoodPlace($f){

		$rf =	realpath($f);
		$rd =	realpath(Settings::$photos_dir);
		
		if($rf == $rd) return true;

		if( substr($rf,0,strlen($rd)) == $rd ){
			return true;
		}
		return false;

	}

	/**
	 * Returns true if the current user may access this file
	 *
	 * @param string $f file to access
	 * @return bool
	 * @author Thibaud Rohmer
	 */
	public static function view($f){
		
		// Check if user has an account		
		if(!isset(CurrentUser::$account)){
			// User is not logged in
			$judge	=	new Judge($f);
			return($judge->public);
		}

		if(!Judge::inGoodPlace($f))
			return false;

		// No Judge required for the admin. This guy rocks.
		if(CurrentUser::$admin)
			return true;

		// Create Judge
		$judge	=	new Judge($f);
		
		// Public file
		if($judge->public){
			return true;
		}

		// User allowed
		if(in_array(CurrentUser::$account->login,$judge->users))
			return true;
			
		// User in allowed group
		foreach(CurrentUser::$account->groups as $group){
			if(in_array($group,$judge->groups))
				return true;
		}
		return false;
	}

	/**
	 * Display the rights on website, and let
	 * the admin edit them.
	 * 
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		
		echo "<div class='adminrights'>\n";
		echo "<h3>$this->filename</h3>\n";


		if($this->public){

			echo "<form action='?t=Pri&f=$this->webpath' method='post'>\n";
			echo "This item is public.";
			echo "<input type='submit' class='button blue' value='Go Private' />";
			echo "</form>";
			echo "</div>";
			return;

		}else{

			echo "<form action='?t=Pub&f=$this->webpath' method='post'>\n";
			echo "This item is private.";
			echo "<input type='submit' class='button blue' value='Go Public' />";
			echo "</form>";

		}

		echo "<form action='?t=Rig&f=$this->webpath' method='post'>\n";
		echo "<h3>Accounts</h3>";

		foreach(Account::findAll() as $account){
			
			if(in_array($account['login'], $this->users)){
				$checked = "checked";
			}else{
				$checked = "";
			}

			echo "<label><input type='checkbox' value='".$account['login']."' name='users[]' $checked >".$account['login']."</label>";
		}

		echo "<h3>Groups</h3>";

		foreach(Group::findAll() as $group){
			if($group['name'] == "root"){
				continue;
			}
			if(in_array($group['name'], $this->groups)){
				$checked = "checked";
			}else{
				$checked = "";
			}

			echo "<label><input type='checkbox' value='".$group['name']."' name='groups[]' $checked > ".$group['name']." </label>";
		}

		echo "</br><input type='submit' class='button blue' value='Set Rights'>\n";
		echo "</form>\n";
		echo "</div>\n";
	}


}
?>