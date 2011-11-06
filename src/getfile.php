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
session_start();

if(file_exists('secu.php')) chdir('..');

require_once 'src/secu.php';
require_once 'src/listings.php';
require_once 'src/settings.php';
require_once 'src/images.php';

$settings=get_settings();

$file = $settings['photos_dir']."/".urldecode($_GET['file']);

if(!right_path($file)) {
	echo "bad boy";
	return;
}

if(isset($_GET['t']) && $_GET['t']=="thumb"){
	$file = $settings['thumbs_dir']."/".urldecode($_GET['file']);
}

if (file_exists($file) && is_file($file))
{
	header('Content-type: image/jpeg');
	readfile($file);
}else{
	header('Content-type: image/jpeg');
	gener_thumb($settings['photos_dir']."/".urldecode($_GET['file']));	
}

?>