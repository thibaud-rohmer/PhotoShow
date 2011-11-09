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
		$settings	=	new Settings();
		$xml_infos	=	$settings->groups_file;
		$xml		=	simplexml_load_file($xml_infos);

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
	 * Create group and save into base
	 *
	 * @param string $name 
	 * @param string $rights 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function create($name,$rights=array()){
		if(self::exists($name))
			throw new Exception("$name already exists");
			
		$settings	=	new Settings();
		$xml_infos	=	$settings->groups_file;
		$xml		=	simplexml_load_file($xml_infos);
		
		$g=$xml->addChild('group');
		$g->addChild('name',$name);
		$xml_rights=$g->addChild('rights');
		
		foreach($rights as $r)
			$xml_right->addChild($r);
	}
	
	/**
	 * Save group into base
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function save(){
		$settings	=	new Settings();
		$xml_infos	=	$settings->groups_file;
		$xml		=	simplexml_load_file($xml_infos);

		foreach( $xml as $group ){
			if( (string)$group->$name == $this->name){
				unset($group->rights);
				$xml_rights=$group->addChild('rights');
				
				foreach( $this->rights as $right )
					$xml_rights->addChild('right',$right);
		
				$xml->asXML();
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
		$xml_infos	=	$settings->groups_file;
		$xml		=	simplexml_load_file($xml_infos);

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
		$settings	=	new Settings();
		$xml_infos	=	$settings->groups_file;
		$xml		=	simplexml_load_file($xml_infos);

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
	
	
}

?>