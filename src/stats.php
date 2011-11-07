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
require_once realpath(dirname(__FILE__).'/layout.php');

/**
 * Returns the number of accounts
 */
function count_accounts(){
	$settings=get_settings();
	$file=$settings['thumbs_dir']."/accounts.xml";
	$logins=array();
	
	// Check that file exists
	if(!file_exists($file)) return false;
	
	$xml=simplexml_load_file($file);
	
	// Return number of accounts
	if(phpversion()>='5.3.0'){
		$count=$xml->count();
	}else{
		$count=count($xml->children());
	}
	return $count;
}

/**
 * Returns the number of groups
 */
function count_groups(){
	$settings=get_settings();
	$file=$settings['thumbs_dir']."/groups.xml";
	$logins=array();
	
	// Check that file exists
	if(!file_exists($file)) return false;
	
	$xml=simplexml_load_file($file);
	
	// Return number of groups
	if(phpversion()>='5.3.0'){
		$count=$xml->count();
	}else{
		$count=count($xml->children());
	}
	return $count;
}

/**
 * Returns the number of photos
 *
 * 	\param string $dir
 * 		Path from where to count
 */
function count_photos($dir=false){
	$settings=get_settings();
	$count=0;

	// If not set, we start from photos_dir
	if(!($dir)) $dir=$settings['photos_dir'];
	
	$folders	=	list_dirs($dir,true);
	$files		=	list_files($dir);
	
	// Number of photos in the current dir
	$count		=	sizeof($files);
	
	// Number of photos in subdirs
	foreach($folders as $f){
		$count 	=	$count + count_photos($f);
	}
	
	return $count;
}

?>