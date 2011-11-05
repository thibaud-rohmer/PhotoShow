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


if(file_exists('comments.php')) chdir('..');

require_once 'src/settings.php';
require_once 'src/secu.php';
require_once 'src/xml.php';


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

// Get path without extension
$info = pathinfo($_GET['f']);
$path =  dirname($_GET['f'])."/".basename($_GET['f'],'.'.$info['extension']);

// Get comms file
$comms_file	=	$settings['thumbs_dir']."/".$path."_coms.xml";

$comments	=	array();
// Let's check that the file exists
if (file_exists($comms_file) && is_file($comms_file)){
	// Parsing the comments
	$comments=parse_comments($comms_file);
}

if(isset($_POST['name']) && isset($_POST['comment']) && $_POST['name']!='' && $_POST['comment']!=''){
	if(!file_exists($comms_file)){
		$rss='<?xml version="1.0"?><comments></comments>';
		$myfile=fopen($comms_file,"w+");
		fwrite($myfile,$rss);	
		fclose($myfile);
		$comments=parse_comments($comms_file);
	}
	$name		=	$_POST['name'];
	$comment	=	$_POST['comment'];
	$newitem	=	$comments->addChild('comment');
	$newitem->addChild('id',$name);
	$newitem->addChild('val',$comment);
	$comments->asXML($comms_file);
	
	$info['title']="New comment by ".$_POST['name'];
	$info['description']=$_POST['comment'];
	$info['link']=$settings['site_url']."?f=".$_GET['f'];
	feed('comments',$info);
}


echo "<div class='box_title'>Comments</div><div id='comments_display'>";

// Displaying the comments one by one
if(sizeof($comments)>0){
	foreach($comments as $com){
		$id		=	htmlentities( $com->id );
		$val	=	htmlentities( $com->val );
	
		echo "<div class='comment'>\n";
		echo "<div class='comment_id'>$id</div>\n";
		echo "<div class='comment_data'>$val</div>\n";
		echo "</div>\n";
	}
}
?>
</div>
<div id='comments_form_div'>
	<form action='#' id='comments_form' method='post'>
		<div class='label'>Name</div>
		<input type='text' name='name' id='name'>
		<div class='label'>Comment</div>
		<textarea name='comment' id='comment'></textarea>
		<input type='submit' value='Send' class='button blue'>
	</form>
</div>
