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

require_once 'settings.php';
require_once 'listings.php';

/**
 * Returns an array of the logins
 */
function get_logins(){
	$settings=get_settings();
	$file=$settings['thumbs_dir']."/accounts.xml";
	$logins=array();
	
	// Check that file exists
	if(!file_exists($file)) return false;
	$xml=simplexml_load_file($file);
	
	// Look the accounts
	foreach($xml as $acc){
		$logins[]=(string)$acc->login;
	}
	
	return $logins;
}

/**
 * Login !
 *
 * 	\param string $login
 * 		Login
 * 	\param string $pass
 * 		Pass
 */
function log_me_in($login,$pass,$crypted=false){
	$settings=get_settings();
	$file=$settings['thumbs_dir']."/accounts.xml";
	
	if(!file_exists($file)) return false;
	
	$xml=simplexml_load_file($file);
	
	// Look for the account
	foreach($xml as $acc){
		if($acc->login==$login){
			if(($crypted && $acc->pass == $pass) OR (!$crypted && $acc->pass == sha1($pass))){
				$_SESSION['login']	=	$login;
				$xmlgrp				=	$acc->groups->children();
				foreach ( $xmlgrp as $g ){
					$_SESSION['groups'][]	=	(string) $g;
				}
				return true;
			}
		}
	}
	return false;
}

/**
 * Logout
 */
function log_me_out(){
	unset($_SESSION['login']);
}

/**
 * Returns an array with the groups (if no id entered, returns all groups)
 *
 * 	\param string id
 * 		User id
 **/
function get_groups($login=""){
	$settings	=	get_settings();
	$groups		=	array();
	$xmlgrp		=	array();
	
	if($login==""){
		$file=$settings['thumbs_dir']."/groups.xml";
		$xmlgrp=simplexml_load_file($file);
		foreach($xmlgrp as $g){
			$groups[]=$g->name;
		}
	}else{
		$file=$settings['thumbs_dir']."/accounts.xml";
		$xml=simplexml_load_file($file);
		foreach($xml->account as $acc){
			if($acc->login==$login){
				$xmlgrp=$acc->groups->children();
				break;
			}
		}
		
		foreach($xmlgrp as $g){
			$groups[]=(string) $g;
		}
		
	}

	return $groups;
}

/**
 * Adds the new groups to the groups.xml file
 *
 *	\param array $groups
 *		Some groups
 **/
function add_groups($groups,$rights=array()){
	$settings=get_settings();
	$file=$settings['thumbs_dir']."/groups.xml";
	
	// Make sure that we don't have any doubles
	$groups=array_unique($groups);
	
	// Create file if it doesn't exist
	if(!file_exists($file)){
		$rss='<?xml version="1.0"?><groups><group>root</group></groups>';
		$myfile=fopen($file,"w+");
		fwrite($myfile,$rss);
		fclose($myfile);
	}
	
	// Load into xml
	$xml=simplexml_load_file($file);
	
	// Remove known groups from $groups
	foreach($xml as $known_group){
		if($pos=array_search($known_group->name,$groups)>-1)
			unset($groups[$pos]);
	}
	
	// Add new groups to $xml
	foreach($groups as $ng_name){
		$ng=$xml->addChild('group');
		$ng->addChild('name',$ng_name);
		foreach($rights as $r){
			$ng->addChild('right',$r);
		}
	}
	
	// Write xml into file
	$xml->asXML($file);
}

/**
 * Checks if the password fits the user
 *
 * 	\param string $login
 * 		Login
 * 	\param string $pass
 * 		Pass
 */
function add_account($login,$pass,$groups=array(),$more=array()){
	$settings=get_settings();
	$file=$settings['thumbs_dir']."/accounts.xml";
	
	// Make sure that we don't have any doubles
	$groups[]	=	"user";
	$groups		=	array_unique($groups);
	
	// Create file if it doesn't exist
	if(!file_exists($file)){
		$groups[]	=	"root";
		$xml		=	new SimpleXMLElement("<accounts></accounts>");
	}else{
		// Load into xml
		$xml=simplexml_load_file($file);
	}
	
	// Return false if account already exists
	foreach($xml as $acc){
		if($acc->login==$login) return false;
	}
	
	// Create new account
	$new_account=$xml->addChild('account');
	$new_account->addChild('login',$login);
	$new_account->addChild('pass',sha1($pass));
	$ngrp=$new_account->addChild('groups');
	foreach ( $groups as $g){
		$ngrp->addChild('group',$g);
	}
	if(sizeof($more)>0){
		foreach ( $more as $info => $value){
			$new_account->addChild($info,$value);
		}
	}
	// Write xml into file
	$xml->asXML($file);
	
	// Add new groups to the groups file
	add_groups($groups);
	return true;
}

/**
 * Checks if a user is allowed to see a path
 *
 * 	\param string $f
 * 		Path required
 */
function has_right($f=false){
	// TODO : the function.
	return true;
}

/**
 * Returns true if the path is viewable
 *
 * 	\param string $f
 *		Path required
 */
function right_path($f=false){
	if(!$f) return false;

	if(!file_exists($f)) return false;
	
	$settings=get_settings();
	if(relative_path($f,$settings['photos_dir']) == -1 && relative_path($f,$settings['thumbs_dir']) == -1) return false;
	
	if(!has_right($f)) return false;
	
	return true;
}

/**
 * Returns an array containing the infos necessary to build the page
 * 
 * \param string $f
 * 		Relative Path to a directory or a file
 */
function parse_action($f=false){
	$action=array();
	
	/// Default action
	$action['dir']		=	"";
	$action['subdir']	=	"";
	$action['image']	=	"";
	$action['layout']	=	"thumbs";
	
	// Special cases
	$specials=array('rss','register','login');
	if(in_array($f,$specials)){
		$action['layout']	=	'special';
		$f					=	'';
	}
	
	$settings=get_settings();
	$path=$settings['photos_dir']."/".$f;

	/// We check that the user is allowed to look this path	
	if(!right_path($path)) return $action;

	
	$paths	=	explode('/',$f);

	/// First, get selected dir
	$action['dir']	=	$settings['photos_dir']."/".$paths[0];
	
	/// Then, get selected subdir
	if(sizeof($paths)>1)
		$action['subdir']	=	$settings['photos_dir']."/".$paths[0]."/".$paths[1];

	/// Then, check if we want to display an image
	if(is_file($path))
		$action['layout']	=	"image";

	$action['display']	=	$path;	

	return $action;
}

?>