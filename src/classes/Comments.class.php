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

class Comments
{
	private $comments=array();
	private $file;
	private $commentsfile;
	
	public function __construct($file=null){
		if(!isset($file)) return;
		
		if(File::Type($file) != "Image")
			throw new Exception("$file is not an image");
		
		$this->file	=	$file;
		$settings	=	new Settings();
		$basefile	= 	new File($file);
		$basepath	=	File::a2r($file);
		if(is_file($file)){
			$comments	=	dirname($basepath)."/.".$basefile->name."_comments.xml";
		}
		
		$this->commentsfile =	File::r2a($comments,$settings->thumbs_dir);
		
		if(file_exists($this->commentsfile))
			$this->parse_comments_file();
	}

	private function parse_comments_file(){
		$xml		=	simplexml_load_file($xml_infos);
		
		foreach( $xml as $comm ){
			$comments[]=new Comment((string)$comm->name,(string)$comm->content,(string)$comm->date);
		}
	}
	
	public function toHTML(){
		echo "<div id='comments' class='box'>";
		foreach($this->comments as $com){
			$com->toHTML();
		}
		echo "<div id='comments_form_div'>\n";
			echo "<form action='#' id='comments_form' method='post'>\n";
				echo "<div class='label'>Name</div>\n";
				echo "<input type='text' name='name' id='name'>\n";
				echo "<div class='label'>Comment</div>\n";
				echo "<textarea name='comment' id='comment'></textarea>\n";
				echo "<input type='submit' value='Send' class='button blue'>\n";
			echo "</form>\n";
		echo "</div>\n";
			
		echo "</div>";
	}
}

?>