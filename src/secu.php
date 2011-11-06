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

require_once realpath(dirname(__FILE__).'/settings.php');
require_once realpath(dirname(__FILE__).'/listings.php');
require_once realpath(dirname(__FILE__).'/accounts.php');
require_once realpath(dirname(__FILE__).'/login.php');


/**
 * Returns the path to the rights of the file
 *
 *	\param string $file
 * 		Path to the file (absolute)
 */
function get_rights_file($f){
	$settings=get_settings();
	
	// Check type of file, to find the settings file
	if(is_file($f)){
		// Get path without extension
		$tf		=	relative_path($f,$settings['photos_dir']);
		$info 	= 	pathinfo($tf);
		$path 	= 	dirname($tf)."/.".basename($tf,'.'.$info['extension']).".xml";
		$file	=	$settings['thumbs_dir']."/".$path;
	}else{
		$tf		=	relative_path($f,$settings['photos_dir']);
		$info 	=	pathinfo($tf);
		$file	=	$settings['thumbs_dir']."/".$tf."/.config.xml";
		$union	=	false;
	}
	return $file;
}

/**
 * Edits the rights of a file
 *
 * 	\param string $f
 * 		File we want to edit the settings of
 * 	\param array $infos
 * 		Groups and Users allowed
 */
function edit_rights($f,$infos){
	$file	=	get_rights_file($f);
	$xml	=	new SimpleXMLElement('<?xml version="1.0"?><values></values>');
	$xml_g=$xml->addChild('groups');
	$xml_u=$xml->addChild('users');
	if(sizeof($infos['groups'])>0)
		foreach($infos['groups'] as $g)
			$xml_g->addChild('group',$g);
	if(sizeof($infos['users'])>0)
		foreach($infos['users'] as $u)
			$xml_u->addChild('user',$u);
	
	$xml->asXML($file);
}

/**
 *	Returns an array of: the groups, and the users, who are allowed to view $f
 *
 *  The rights are made this way :    rights(file) U ( Inter(rights(dirs)) )  
 *
 * 	\param $f
 * 		The file we want to view
 * 	\param $union
 * 		Union or Intersection
 */
function who_can_view($f,$union=true){
	$allowed['groups']=array();
	$allowed['users']=array();

	$settings=get_settings();
	
	// Check if file exists
	if(!file_exists($f)){
		return false;
	}
	
	$file=get_rights_file($f);

	// If there is no settings file, we check previous dir
	if(!file_exists($file)){
		if(same_path($f,$settings['photos_dir'])) return $allowed;		
		return who_can_view(dirname($f),true);
	}

	// Loading the file
	$xml=simplexml_load_file($file);

	// Parsing the file
	foreach($xml->groups->children() as $g)
		$allowed['groups'][]=(string)$g;
	
	foreach($xml->users->children() as $u)
		$allowed['users'][]=(string)$u;
	
	// Stop there if we have reach the main dir
	if(same_path($f,$settings['photos_dir'])) return $allowed;
	
	// Check previous dir
	$prev_allowed=who_can_view(dirname($f),false);

	// Union / Intersection of the arrays
	if($union==true){
		$allowed['groups']=array_unique(array_merge($allowed['groups'],$prev_allowed['groups']));
		$allowed['users']=array_unique(array_merge($allowed['users'],$prev_allowed['users']));
	}else{
		if(sizeof($prev_allowed['groups'])>0)
			$allowed['groups']=array_intersect($allowed['groups'],$prev_allowed['groups']);		
		if(sizeof($prev_allowed['users'])>0)
			$allowed['users']=array_unique(array_merge($allowed['users'],$prev_allowed['users']));
	}
	return $allowed;
}


/**
 * Checks if a user is allowed to see a path
 *
 * 	\param string $f
 * 		Path required
 */
function has_right($f){
	// Admin is always right.
	if(admin())
		return true;
		
	if(!$allowed=who_can_view($f))
		return false;
	// If this is public, everyone has right
	if(sizeof($allowed['groups'])==0 && sizeof($allowed['users'])==0)
		return true;

	// If this isn't public, and we aren't loggued in, we can't have right
	if(!isset($_SESSION['login']))
		return false;

	// Either our login is in the list of allowed users, or we are in an allowed group.
	return (in_array($_SESSION['login'],$allowed['users']) || sizeof(array_intersect($_SESSION['groups'],$allowed['groups']))>0);
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
	$specials=array('rss','register','login','user');
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