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

/**
 * Lists non-hidden directories contained in a directory.
 *
 * \param string $dir
 * 		The directory we look into.
 * \param boolean $fullpath
 * 		If true, returns the whole path to each directory
 */
function list_dirs($dir,$fullpath=false){
	$list=array();
	$settings=get_settings();
	/// Scanning the directory
	if(!file_exists($dir)|| !is_dir($dir)) $dir=$settings['photos_dir'];
	$dir_content = scandir($dir);					
	foreach ($dir_content as $content){
		/// We are not listing hidden directories, or '.' and '..'
		if($content[0] != '.'){
			$path=$dir."/".$content;
			/// We are either putting the full path, or just listing the directories
			if(is_dir($path)) $list[]=$fullpath?$path:$content;
		}
	}
	return $list;
}

/**
 * Lists non-hidden files contained in a directory.
 *
 * \param string $dir
 * 		The directory we look into.
 * \param boolean $fullpath
 * 		If true, returns the whole path to each directory
 */
function list_files($dir,$fullpath=false){
	$list=array();
	$settings=get_settings();
	/// Scanning the directory
	if(!file_exists($dir)|| !is_dir($dir)) $dir=$settings['photos_dir'];
	$dir_content = scandir($dir);					
	foreach ($dir_content as $content){
		/// We are not listing hidden directories, or '.' and '..'
		if($content[0] != '.'){
			$path=$dir."/".$content;
			/// We are either putting the full path, or just listing the directories
			if(is_file($path)) $list[]=$fullpath?$path:$content;
		}
	}
	return $list;
}

/**
 * Compares two paths. Return true if it is the same.
 *
 * \param string $a
 * 		First path
 * \param string $b
 * 		Second path
 */
function same_path($a,$b){
	return (realpath($a)==realpath($b));
}

/**
 * Returns the path to $f relatively to $d
 *
 * \param string $f
 *  	File we are checking, has to be inside $d
 * \param string $d
 *  	Folder where we want to chroot
 */
function relative_path($f,$d){
	$rf = realpath($f);
	$rd = realpath($d);
	
	/// We check if $f is inside $d
	if( substr($rf,0,strlen($rd)) != $rd ) return -1;
	return ( substr($rf,strlen($rd) + 1 ) );
}


?>
