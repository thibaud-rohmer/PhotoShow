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

class Settings
{

	static public $photos_dir;
	static public $thumbs_dir;
	static public $feeds_dir;
	static public $accounts_file;
	static public $groups_file;
	
	public function __construct(){
		// Settings already created
		if($this->photos_dir !== NULL) return;

		$ini_file		=	realpath(dirname(__FILE__)."/../../conf.ini");
		$ini_settings	=	parse_ini_file($ini_file);
		
		$this->photos_dir	=	$ini_settings['photos_dir'];
		$this->thumbs_dir	=	$ini_settings['thumbs_dir'];
		$this->feeds_dir		=	$ini_settings['feeds_dir'];
		$this->accounts_file=	$this->thumbs_dir."/accounts.xml";
		$this->groups_file	=	$this->thumbs_dir."/groups.xml";
		
		// Now, check that this stuff exists.
		if(!file_exists($this->photos_dir)){
			throw new Exception("Photos dir doesn't exist !");
		}

		if(!file_exists($this->thumbs_dir)){
			throw new Exception("Thumbs dir doesn't exist !");
		}
		
		if(!file_exists($this->feeds_dir)){
			throw new Exception("Feeds dir doesn't exist !");
		}
		
		if(!file_exists($this->accounts_file)){
			$e = new FileException("Accounts file missing",69);
			$e->file = $this->accounts_file;
			throw $e;
		}
		
		if(!file_exists($this->groups_file)){
			$xml=new SimpleXMLElement('<groups></groups>');
			$xml->asXML($this->groups_file);
			Group::create("root");
			Group::create("user");
		}
	}
		
}
?>