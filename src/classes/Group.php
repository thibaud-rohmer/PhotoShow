<?php
/**
 * This file implements the class Group.
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
 * Group
 *
 * Each group has several rights. Those rights will
 * be used to determine what a user belonging to 
 * the groups is allowed to do.
 * Groups are stored in the [thumbs]/groups.xml file.
 * Their structure is :
 * - Name
 * - Rights -> Right
 *
 * 
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */

class Group
{
	public $name;
	public $rights;
	
	/**
	 * Find group in base.
	 *
	 * @param string $name 
	 * @author Thibaud Rohmer
	 */
	public function __construct($name){
		
		/// Check if group file exists
		if(!file_exists(CurrentUser::$groups_file)){
			Group::create_group_file();
		}

		/// Load file
		$xml		=	simplexml_load_file(CurrentUser::$groups_file);

		foreach( $xml as $group ){
			if($group->$name == $name){
				$this->name		=	$group->name;
				$this->rights	=	$group->rights;
				return;
			}
		}
		throw new Exception("$name not found");
	}

	/**
	 * Create group file
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function create_group_file(){
			$xml=new SimpleXMLElement('<groups></groups>');
			$xml->asXML(CurrentUser::$groups_file);
			Group::create("root");
			Group::create("user");
	}

	/**
	 * Create group and save into base
	 *
	 * @param string $name 
	 * @param string $rights 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function create($name,$rights=array()){
		
		/// Check that groups file exists
		if(!file_exists(CurrentUser::$groups_file)){
			Group::create_group_file();
		}
		
		/// Check that group doesn't already exist
		if(self::exists($name))
			throw new Exception("$name already exists");

		/// Load file
		$xml		=	simplexml_load_file(CurrentUser::$groups_file);
		
		$g=$xml->addChild('group');
		$g->addChild('name',$name);
		$xml_rights=$g->addChild('rights');
		
		foreach($rights as $r)
			$xml_right->addChild($r);
		
		$xml->asXML(CurrentUser::$groups_file);
	}
	
	/**
	 * Save group into base
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function save(){
		/// Load file
		$xml		=	simplexml_load_file(CurrentUser::$groups_file);

		foreach( $xml as $group ){
			if( (string)$group->$name == $this->name){
				unset($group->rights);
				$xml_rights=$group->addChild('rights');
				
				foreach( $this->rights as $right )
					$xml_rights->addChild('right',$right);
		
				$xml->asXML(CurrentUser::$groups_file);
				return;
			}
		}
		
		throw new Exception("$this->name not found");
	}
	
	/**
	 * Check if the group is already in the base
	 *
	 * @param string $name 
	 * @return bool
	 * @author Thibaud Rohmer
	 */
	public static function exists($name){
		try{
		$settings	=	new Settings();
		}catch(Exception $e){
			// No file, no group !
			return false;
		}
		
		/// Load file
		$xml		=	simplexml_load_file(CurrentUser::$groups_file);

		foreach( $xml as $group ){
			if( (string)$group->$name == $name)
				return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the rights of the group
	 *
	 * @param string $name 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function rights($name){
		$rights		=	array();

		/// Load file
		$xml		=	simplexml_load_file(CurrentUser::$groups_file);

		foreach( $xml as $group ){
			if( (string)$group->$name == $name){
				foreach($group->rights as $right){
					$rights[]=$right;
				}
				return $rights;
			}
		}
		throw new Exception("$name not found");
	}
	
	public static function findAll(){
		
		$groups		=	array();

		/// Load file
		$xml		=	simplexml_load_file(CurrentUser::$groups_file);

		foreach( $xml as $group ){

			$newgroup = array();
			$newgroup['name'] 	= $group->name;
			$newgroup['rights']	= array();
			
			foreach($group->rights->children() as $right){
				$newgroup['rights'][] = $right;
			}
			
			$groups[] = $newgroup;
		}

		return $groups;

	}
	
}

?>