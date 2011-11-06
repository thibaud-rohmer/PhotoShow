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
 * Parses the comments and returns them in a nice array
 *
 * 	\param string $f
 * 		The comments file
 */
function parse_comments($f){
	return simplexml_load_file($f);	
}

/**
 * Generates an rss feed
 *
 * 	\param string $t
 *			Type of feed
 * 	\param array $info
 *			Information necessary
 */
function feed($t,$info){
	$settings=get_settings();
	
	if(!isset($settings["rss_$t"])) return;
	
	$file=$settings["rss_$t"];
	if($file[0]!="/"){
		$file=realpath(dirname(__FILE__))."/../".$settings["rss_$t"];
	}
	
	if(!file_exists($file)){
		$rss='<?xml version="1.0"?><rss version="2.0"><channel></channel></rss>';
		$myfile=fopen($file,"w+");
		fwrite($myfile,$rss);	
		fclose($myfile);
	}
	
	$xml		=	simplexml_load_file($file);
	$new_item	=	$xml->addChild('item');
	$new_item->addChild('title',$info['title']);
	$new_item->addChild('description',$info['description']);
	$new_item->addChild('link',$info['link']);
	
	$xml->asXML($file);	
}

?>