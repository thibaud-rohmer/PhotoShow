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