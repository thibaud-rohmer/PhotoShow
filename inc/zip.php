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

if(file_exists('zip.php')) chdir('..');

require_once 'src/settings.php';
require_once 'src/images.php';
require_once 'src/secu.php';
require_once 'src/listings.php';

// Let's check that we have a $_GET['f']
if(!isset($_GET['f'])){
	echo "Missing argument";
	return;
}

$settings	=	get_settings();
$file		=	$settings['photos_dir']."/".$_GET['f'];

// Let's check that we have a $_GET['f']
if(!right_path($file)){
	return;
}

// Let's check that the file exists
if (!file_exists($file) || !is_dir($file)){
	return;
}


// We prepare the file
$tmpfile = tempnam("tmp", "zip");
$zip = new ZipArchive();
$zip->open($tmpfile, ZipArchive::OVERWRITE);

// Staff with content
$photos=list_files($file,true);

foreach($photos as $photo){
	$zip->addFile($photo,basename($photo));
}

// Close and send to user
$fname=basename($file);
$zip->close();
header('Content-Type: application/zip');
header('Content-Length: ' . filesize($tmpfile));
header("Content-Disposition: attachment; filename=\"$fname.zip\"");
readfile($tmpfile);
unlink($tmpfile);

?>