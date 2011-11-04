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

if(file_exists('exif.php')) chdir('..');

require_once 'src/settings.php';
require_once 'src/images.php';
require_once 'src/secu.php';

// Let's check that we have the EXIF extension
$load_ext = get_loaded_extensions();
if (!in_array(exif, $load_ext)) {
	echo "Exif extension is not installed on the server available";
	return;
}

// Let's check that we have a $_GET['f']
if(!isset($_GET['f'])){
	echo "Missing argument";
	return;
}

$settings	=	get_settings();
$file		=	$settings['photos_dir']."/".$_GET['f'];
$raw_exif	=	exif_read_data($file);

// Let's check that we have a $_GET['f']
if(!right_path($file)){
	echo "Not allowed";
	return;
}

// Let's check that the file exists
if (!file_exists($file) || !is_file($file)){
	echo "The file couldn't be found";
	return;
} 

// And now, let's do some magic.
$wanted=array();
$wanted['Name'][]			=	'FileName';
$wanted['Model'][]			=	'Model';
$wanted['Make'][]			=	'Make';
$wanted['Expo'][]			=	'ExposureTime';
$wanted['Focal Length'][]	=	'FocalLength';
$wanted['Aperture'][]		=	'FNumber';
$wanted['ISO'][]			=	'ISOSpeedRatings';

$exif	=	array();

foreach($wanted as $name => $data){
	foreach($data as $d){
		if(isset($raw_exif[$d])){
			$exif[$name]	=	parse_exif($d,$raw_exif);
		}	
	}
}
?>


<div class="box_title">exif</div>
<table class="data"><tbody>
<?php

foreach($exif as $name=>$value){
	echo "<tr><td class='td_data'>$name</td><td class='td_value'>$value</td></tr>\n";
}


?>	
	
</tbody></table>
