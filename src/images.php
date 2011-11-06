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
require_once realpath(dirname(__FILE__).'/phpthumb/ThumbLib.inc.php');
require_once realpath(dirname(__FILE__).'/xml.php');

/**
 * Returns the path to the thumb corresponding to $file
 *
 * \param string $file
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
 * \param string $file
 * 		The file we generate a thumb of.
 */
function gener_thumb($file){
	/// Set destination
	$dest=get_thumb($file);

	if(!file_exists(dirname($dest))){
		mkdir(dirname($dest),0750,true);
		
		$settings=get_settings();
		$info['title']="New album:".basename(dirname($dest));
		$info['description']="<img src='".$settings['site_url']."/src/getfile.php?f=".relative_path($file,$settings['photos_dir'])."'>";
		$info['link']=$settings['site_url']."?f=".dirname($dest);
		
		feed("albums",$info);
	}
	$thumb = PhpThumbFactory::create($file);
	$thumb->resize(200, 200);
	$thumb->save($dest);
	$thumb->show($dest);
	
	
}

/**
 *  Detects the ratio width/height of the images. Returns the list of the information.
 * 	\param array $images
 * 			List of the images to analyze
 * 	\param int $images_per_line
 * 			Number of images per line
 */
function analyze_images($images,$images_per_line){
	$ratio		=	array();
	$list		=	array();
	$line		=	0;
	$line_value	=	0;
	
	// Calculate the ratio width/height of each image 
	foreach ( $images as $image ) {
		list($x,$y)=getimagesize($image);
		
		if($y>$x){  			/// Portrait
			$r=1;
		}else{		/// Large landscape
			$r=floor($x/$y)+1;
		}
		if($r>3) $r=3;
		$ratio[]=$r;
	}
	
	// Create the grid 
	foreach ( $ratio as $r ){
		$line_value=$line_value+$r;
		if($line_value>$images_per_line){	/// The line is complete
			$line_value	=	0;
			$line		=	$line+1;
		}
		$list[$line][]=$r;
	}
	
	return $list;
}

/**
 * Calculates the float from a string containing a fraction
 * 
 *	\param string $f
 * 		The fraction
 */
function frac2float($f){
	$frac	=	explode('/',$f);
	$float	=	$frac[0]/$frac[1];
	return $float;
}

/**
 * Formats the raw exif data into good data
 * 
 *	\param string $d
 * 		Raw name of the data
 * 	\param ? $value
 * 		Raw exif
 */
function parse_exif($d,$raw_exif){
	// Values that don't need to be processed
	$untouched=array('FileName','Model','Make','ISOSpeedRatings');
	if(in_array($d,$untouched)) 
		return $raw_exif[$d];
	
	$v=0;
	switch ($d){
		case 'ExposureTime': 	$v	=	$raw_exif[$d]." s";
								break;
		case 'FocalLength':		$v		=	frac2float($raw_exif[$d])." mm";
								break;
		case 'FNumber':			$v	=	frac2float($raw_exif['FocalLength'])/frac2float($raw_exif[$d]);
								break;
	}
	return $v;
}

?>
