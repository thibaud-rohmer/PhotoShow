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
	public $groups;
	
	/// Users allowed to see requested file
	public $users;
	
	/**
	 * Create a Judge for a specific file.
	 *
	 * @param string $f 
	 * @param string $inherited 
	 * @param string $read_rights 
	 * @author Thibaud Rohmer
	 */
	public function __construct($f, $inherited=false, $read_rights=true){
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
		if(is_file($f)){
			$rightsfile	=	dirname($basepath)."/.".$basefile->name.".xml";
		}else{
			$rightsfile	=	$basepath."/.config.xml";
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

			$this->public	=	(int)$xml->public;
			
			foreach($xml->groups->children() as $g)
				$this->groups[]=(string)$g;

			foreach($xml->users->children() as $u)
				$this->users[]=(string)$u;

			$next_inherited=true;
			
			
		}catch(Exception $e){
			// No Rights file
			$next_inherited=false;
		
			/// If no rights file found, check in the containing directory
			try{

				// Look up
				$up_path		=	File::a2r(dirname($base));
				$up				=	new Judge($up,$next_inherited);
				if($inherited){
					$this->public	=	$this->public && $up->public;
					$this->groups	=	array_intersect($this->groups,$up->groups);
					$this->users	=	array_intersect($this->groups,$up->users);
				}else{
					$this->groups	=	array_unique(array_merge($this->groups,$up->groups));
					$this->users	=	array_unique(array_merge($this->groups,$up->groups));
				}			

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
	private function save(){
		
		/// Create xml
		$xml		=	new SimpleXMLElement('<rights></rights>');
		
		/// Put values in xml
		$xml->addChild('public',$this->public);
		$xml_users	=	$xml->addChild('users');
		$xml_groups	=	$xml->addChild('groups');
		
		foreach($this->users as $user)
			$xml_users->addChild($user);

		foreach($this->groups as $group)
			$xml_groups->addChild($group);
		
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
	public static function edit($f,$groups=array(),$users=array()){
		
		/// Just to be sure, check that user is admin
		if(!CurrentUser::$admin)
			return
		
		// Create new Judge, no need to read its rights
		$rights			=	new Judge($f,false,false);
		
		/// Put the values in the Judge (poor guy)
		$rights->groups	=	array_unique($groups);
		$rights->users	=	array_unique($users);
		$rights->public	=	(sizeof($groups)==0 && sizeof($users)==0)?0:1;
		
		// Save the Judge
		$rights->save();
	}
	
	/**
	 * Returns true if the current user may access this file
	 *
	 * @param string $f file to access
	 * @return bool
	 * @author Thibaud Rohmer
	 */
	public static function view($f){
		// Check if user is logged in 
		if(!isset(CurrentUser::$account)){
			try{
				CurrentUser::init();
			}catch(Exception $e){
				// User is not logged in
				$judge	=	new Judge($f);
				return($judge->public);
			}
		}

		if(!File::a2r($f))
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
	
}
?>