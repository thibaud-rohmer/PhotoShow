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

class Judge
{
	public $path;
	public $public;
	public $groups;
	public $users;
	
	public function __construct($f, $inherited=false, $read_rights=true){
		$this->public	=	true;
		$this->groups	=	array();
		$this->users	=	array();
		
		$this->set_path($f);
		
		if($read_rights)
			$this->set_rights();
	}
	
	private function set_path($f){
		$settings	=	new Settings();
		$basefile	= 	new File($f);
		$basepath	=	File::a2r($f);
		if(is_file($f)){
			$rightsfile	=	dirname($basepath)."/.".$basefile->name.".xml";
		}else{
			$rightsfile	=	$basepath."/.config.xml";
		}
		$this->path =	File::r2a($rightsfile,$settings->thumbs_dir);
	}
	
	private function set_rights(){
		try{
			// Parse the file
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
	
	private function save(){
		$xml		=	new SimpleXMLElement('<rights></rights>');
		$xml->addChild('public',$this->public);
		$xml_users	=	$xml->addChild('users');
		$xml_groups	=	$xml->addChild('groups');
		
		foreach($this->users as $user)
			$xml_users->addChild($user);

		foreach($this->groups as $group)
			$xml_groups->addChild($group);
		
		$xml->asXML($this->path);
	}
	
	public static function edit($f,$groups=array(),$users=array()){
		// Create new Judge, no need to read its rights
		$rights			=	new Judge($f,false,false);
		$rights->groups	=	array_unique($groups);
		$rights->users	=	array_unique($users);
		$rights->public	=	(sizeof($groups)==0 && sizeof($users)==0)?0:1;
		
		// Save the Judge
		$rights->save();
	}
	
}
?>