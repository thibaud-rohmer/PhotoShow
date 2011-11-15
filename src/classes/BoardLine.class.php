<?php
/**
 * This file implements the class BoardLine.
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
 * BoardLine
 *
 * Contains the items for one line of the Board
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
class BoardLine
{
	/// Array of the items
	public $items = array();
	
	/// Sum of the ratios of the contents
	public static $ratio;
	
	/**
	 * Create BoardLine
	 *
	 * @author Thibaud Rohmer
	 */
	public function __construct(){

		/// Initialize total ratio
		$ratio=0;

	}
	
	/**
	 * Display Boardline on website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){

		echo "<div class='boardline'>\n";
		
		/// Output all items of the board
		foreach($this->items as $item)
			$item->toHTML();
		
		echo "</div>\n";

	}
	
	/**
	 * Append an item to the board
	 *
	 * @param string $file 
	 * @param string $ratio 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function add_item($file,$ratio){	

		/// Append item
		$this->items[]	=	new BoardItem($file,$ratio);
		
		/// Update total ratio
		$this->ratio	=	$ratio + $this->ratio;

	}
	
	/**
	 * Complete a line, and calculate
	 * the width of each item depending
	 * on their ratio
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function end_line(){
		
		/// Set the width of each item
		foreach($this->items as $item)
			$item->set_width($this->ratio);
			
	}
}

?>