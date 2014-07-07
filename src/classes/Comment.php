<?php
/**
 * This file implements the class Comment.
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
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

/**
 * Comment
 *
 * Specifies what is a comment.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */
class Comment implements HTMLObject
{
	/// Login of the poster
	public $login;
	
	/// Date when the comment wat posted
	public $date;
	
	/// Content of the comment
	public $content;

	/// File
	public $file;

	/**
	 * Create comment
	 *
	 * @param string $login 
	 * @param string $content 
	 * @param string $date 
	 * @author Thibaud Rohmer
	 */
	public function __construct($login,$content,$date,$file=null){
		$this->login	=	$login;
		$this->content	=	$content;
		$this->date		=	$date;
		$this->file 	=	$file;
	}
	
	/**
	 * Display comment on website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML($id=0){
		$login		=	stripslashes(htmlentities( $this->login , ENT_QUOTES ,'UTF-8'));
		$content	=	stripslashes(htmlentities( $this->content , ENT_QUOTES ,'UTF-8'));
		$date		=	$this->date;

		echo "<div class='pure-g'>\n";

		echo "<div class='pure-u-1-2 commentauthor'><div>$login</div></div>\n";
		echo "<div class='pure-u-1-2 commentcontent'>$content\n";
		if(CurrentUser::$admin){
			echo "<div class='commentdelete'><form action='?t=Adm&a=CDe' method='post'>
								<input type='hidden' name='image' value='".htmlentities(File::a2r($this->file), ENT_QUOTES ,'UTF-8')."'>
								<input type='hidden' name='id' value='$id'>
								<input type='submit' class='pure-button button-xsmall button-warning' value='x'>
							</form></div>";
		}
		echo "</div>\n";

		echo "</div>\n";
	}
}
?>
