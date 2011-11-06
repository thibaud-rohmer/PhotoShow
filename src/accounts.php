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

require_once realpath(dirname(__FILE__).'/secu.php');
require_once realpath(dirname(__FILE__).'/settings.php');


/**
 * Returns the path to the accounts file
 * 		If the file doesn't exist, it creates it.
 */
function accounts_file(){
	$settings=get_settings();
	$file=$settings['thumbs_dir']."/accounts.xml";
	
	if(!file_exists($file)){
		$f=fopen($file);
		fwrite($f,"<?xml version='1.0'?><accounts></accounts>");
		fclose($f);
	}
	
	return $file;
}

/**
 * Returns the path to the groups file
 * 		If the file doesn't exist, it creates it.
 */
function groups_file(){
	$settings=get_settings();
	$file=$settings['thumbs_dir']."/groups.xml";
	
	if(!file_exists($file)){
		$f=fopen($file);
		fwrite($f,"<?xml version='1.0'?><groups><group><name>root</name><rights></rights></group></groups>");
		fclose($f);
	}
	
	return $file;
}


/**
 * Returns an array of the logins
 */
function get_logins(){
	$file=accounts_file();
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
 * Does coffee with account
 *
 * - No name => list of all the accounts
 * - Name => Infos of the account
 * - $info == DELETE => Delete the account
 * - Name + Info => Add/Edit the account
 *
 */
function account($name=NULL,$info=NULL,$new=false){
	$ret 	=	array();
	
	// Load the file as XML
	$file	=	accounts_file();
	$xml	=	simplexml_load_file($file);

// No name => List accounts
	if( !isset($name) ){
		return xml_list_accounts($xml);
	}

// No info => List infos
	if( !isset($info) ){
		return xml_list_infos($xml,$name);
	}
	
// Create/Add/Delete account
	$account=null;
	
	foreach($xml as $acc){
		if($name==(string)$acc->login){
			$account=$acc;
		}
	}
	
	if(isset($account)&&$new) 
		return false;

	if(!isset($account)){
		if(!$new){
			return false;
		}
		$acc		=	$xml->addChild('account');
		$newgroups	=	$acc->addChild('groups');
		$newgroups->addChild('group','user');
	}
	
	
	foreach($infos as $info=>$value){
		// Flemme...		
	}
}

/**
 * Lists the accounts
 */
function xml_list_accounts($xml){
	$ret=array();
	
	// Look the accounts
	foreach($xml as $acc){
		$ret[]=(string)$acc->login;
	}
	
	return $ret;
}

function xml_list_infos($xml,$login){
	$ret=array();
	
	// Look the accounts
	foreach($xml as $acc){
		if( $login == (string)$acc->$login){
			foreach($acc as $info->$val){
				$ret[(string)$info]=(string)$val;
			}
		}
	}
	
	return $ret;
}

/**
 * Does coffee with group
 *
 * - No name => list of all the groups
 * - Name => Rights of the group
 * - Name + Rights => Add/Edit the group
 *
 */
function group($name=NULL,$rights=NULL){
	$ret 	=	array();
	
	// Load the file as XML
	$file	=	groups_file();
	$xml	=	simplexml_load_file($file);
	
// No name => List all the groups
	if( !isset($name) ){
		return xml_list_groups($xml);
	}

// Name => Rights of the group
	if( !isset($rights) ){
		return xml_list_rights($xml,$group);
	}
	
// Name + Rights => Create/Edit the group
	// Look for the group
	$exists=false;
	foreach( $xml->group as $g ){
		if( $name == (string)$g->name ){
			// Remove its rights
			unset($g->rights);
			$selected_group	=	$g;
			$exists			=	true;
		}
	}
	
	// If doesn't exist, create it
	if(!$exists){
		$selected_group=$xml->addChild('group');
		$selected_group->addChild('name',$name);
	}
	
	$selected_rights	=	$selected_group->addChild('rights');
	foreach( $rights as $r ){
		$selected_rights->addChild('right',$r);
	}
	
	// Finally, save it
	$xml->asXML($file);
}



function xml_list_groups($xml){
	$ret=array();
	
	// Copy all of the groups in the array
	foreach ( $xml->group as $g ) {
		$ret[]=(string) $g->name;
	}
	// Return the array
	return $ret;
}


function xml_list_rights($xml,$group){
	$ret=array();
	
	// Look for the group in the array
	foreach ( $xml->group as $g ) {
		if ( $group == (string)$g->name ){
			// Copy all of the groups in the array
			foreach( $g->rights as $r ){
				$ret[]	=	(string) $r;
			}
			break;
		}			
	}
	return $ret;
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
		$file=groups_file();
		$xmlgrp=simplexml_load_file($file);
		foreach($xmlgrp as $g){
			$groups[]=$g->name;
		}
	}else{
		$file=accounts_file();
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
	// Make sure that we don't have any doubles
	$groups=array_unique($groups);
	
	// Load file into xml
	$file=groups_file();
	$xml=simplexml_load_file($file);
	
	// Remove known groups from $groups
	foreach ( $xml as $known_group ){
		if ( ($pos=array_search((string)$known_group->name,$groups)) >-1)
			unset($groups[$pos]);
	}
	
	// Add new groups to $xml
	foreach ( $groups as $ng_name ){
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
 * Deletes the account passed as argument
 *
 * 	\param string $account
 *  	Login of the user to delete
 */
function delete_account($account){
	return delete_accounts(array($account));
}


/**
 * Deletes the accounts passed as argument
 *
 * 	\param array $list
 *  	List of the users to delete
 */
function delete_accounts($list){
	// Load file
	$file=accounts_file();
	$xml=simplexml_load_file($file);
	
	// Delete users
	$count=0;
	foreach($xml as $acc){
		if(in_array($acc->login,$list)){
			unset($xml->account[$count]);
		}
		$count++;
	}
	
	// Write file
	$xml->asXML($file);
	// TODO : Remove groups deleted from groups.xml
	
	return true;
}

/**
 * Returns all you need to know about an account in an array
 *
 * 	\param string $login
 * 		Account name
 */
function get_account($login){
	$account=array();

	// Load file
	$file=accounts_file();
	$xml	=	simplexml_load_file($file);

	// Look for the account
	foreach($xml as $acc){
		if($acc->login==$login){
			$xml_acc=$acc;
			break;
		}
	}
	if(!isset($xml_acc)) return false;

	// Parse infos
	foreach($xml_acc as $name=>$val){
		$account[(string)$name]=(string)$val;
	}

	// Parse groups
	foreach($xml_acc->groups as $g){
		$account['groups'][]=(string)$g;
	}
	
	return $account;
}

/**
 * Adds an account to the base
 *
 * 	\param string $login
 * 		Login
 * 	\param string $groups
 * 		Groups
 * 	\param string $more
 * 		More info
 */
function edit_account($login,$infos){
	$updated=array();
	
	// Load into xml
	$file=accounts_file();
	$xml=simplexml_load_file($file);
	
	foreach($xml as $acc){
		// Keep the current account stored
		if($acc->login==$login){
			$old_acc=$acc;
		}else{
		// Return false if account already exists
			if(isset($infos['login']) && $acc->login==$infos['login'])
				return false;
		
		}
	}

	if(!isset($old_acc)) return false;

	// Small checking for the password
	if(isset($infos['verify password']) || isset($infos['password'])){
		if(($infos['verify password'] == $infos['password']) && strlen($infos['password']) >= 6)
			$infos['pass']=sha1($infos['password']);
		unset($infos['password']);
		unset($infos['verify password']);
	}
	
	// Edit account
	$counter=0;
	foreach ($old_acc->children() as $val){
		if(isset($infos[(string)$val])){
			unset($old_acc->$val[$counter]);
		}
		$counter++;
	}

	foreach ($infos as $inf=>$val){
		$old_acc->addChild($inf,$val);
		$updated[]=$inf;
	}

	// Write xml into file
	$xml->asXML($file);
	
	return $updated;
}

/**
 * Adds an account to the base
 *
 * 	\param string $login
 * 		Login
 * 	\param string $pass
 * 		Pass
 * 	\param string $groups
 * 		Groups
 * 	\param string $more
 * 		More info
 */
function add_account($login,$pass,$groups=array(),$more=array()){
	$file=accounts_file(); // !!! Creation du premier compte ?
	
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

?>