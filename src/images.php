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

require_once 'src/settings.php';
require_once 'src/listings.php';
require_once 'src/phpthumb/ThumbLib.inc.php';

/**
 * Returns the path to the thumb corresponding to $file
 *
 * \param $file
 * 		The file we need a thumb of.
 */
function get_thumb($file){
	$settings=get_settings();
	$rp=relative_path($file,$settings['photos_dir']);
	return $settings['thumbs_dir']."/".$rp;
}

/**
 *	Generates a thumb, returns it and saves it
 * 
 * \param $file
 * 		The file we generate a thumb of.
 */
function gener_thumb($file){
	/// Set destination
	$dest=get_thumb($file);

	mkdir(dirname($dest),0750,true);
	
	$thumb = PhpThumbFactory::create($file);
	$thumb->resize(200, 200);
	$thumb->save($dest);
	$thumb->show($dest);
}

?>