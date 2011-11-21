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
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
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
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
class Comment implements HTMLObject
{
	/// Login of the poster
	public $login;
	
	/// Date when the comment wat posted
	public $date;
	
	/// Content of the comment
	public $content;

	/**
	 * Create comment
	 *
	 * @param string $login 
	 * @param string $content 
	 * @param string $date 
	 * @author Thibaud Rohmer
	 */
	public function __construct($login,$content,$date=null){
		$this->login	=	$login;
		$this->content	=	$content;
		$this->date		=	$date;
	}
	
	/**
	 * Display comment on website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		$login		=	stripslashes(htmlentities( $this->login ));
		$content	=	stripslashes(htmlentities( $this->content ));

		echo "<div class='comment'>\n";
		echo "<div class='login'>$login</div>\n";
		echo "<div class='content'>$content</div>\n";
		echo "</div>\n";
	}
}
?>