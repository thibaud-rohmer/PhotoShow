<?php
/**
 * This file implements the class Comments.
 * 
 * PHP versions 4 and 5
 *
 * LICENSE:
 * 
 * This file is part of PhotoShow.
 *
 * PhotoShow is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhotoShow is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright 2011 Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */

/**
 * Comments
 *
 * Implements the creating, reading, editing, and
 * displaying of the comments, from and to an xml
 * file.
 * The file is stored in 
 * [Thumbs]/[imagepath]/.[image]_comments.xml
 * Comment Structure:
 * - Name
 * - Rights -> Right
 * 
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
class Comments
{
	/// Array of the comments
	private $comments=array();
	
	/// Path to item
	private $file;
	
	/// Path to comments file
	private $commentsfile;
	
	/**
	 * Read comments for item $file
	 *
	 * @param string $file 
	 * @author Thibaud Rohmer
	 */
	public function __construct($file=null){
		
		/// No item, no comment !
		if(!isset($file)) return;
		
		/// Comments are only supported for Images... who said "for now" ?
		if(File::Type($file) != "Image")
			throw new Exception("$file is not an image");
		
		/// Set variables
		$this->file	=	$file;
		$settings	=	new Settings();
		$basefile	= 	new File($file);
		$basepath	=	File::a2r($file);

		/// Build relative path to comments file
		$comments	=	dirname($basepath)."/.".$basefile->name."_comments.xml";
		
		/// Set absolute path to comments file
		$this->commentsfile =	File::r2a($comments,Settings::$thumbs_dir);
		
		/// Check that comments file exists
		if(file_exists($this->commentsfile))
			$this->parse_comments_file();
	}

	/**
	 * Read contents of comments file, and
	 * store comments in the comments array
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	private function parse_comments_file(){
		$xml		=	simplexml_load_file($xml_infos);
		
		foreach( $xml as $comm ){
			$comments[]=new Comment((string)$comm->name,(string)$comm->content,(string)$comm->date);
		}
	}
	
	/**
	 * Display comments on website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		echo "<div id='comments' class='box'>";
		
		/// Display each comment
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