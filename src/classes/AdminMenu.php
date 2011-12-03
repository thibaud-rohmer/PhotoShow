<?php
/**
 * This file implements the class AdminMenu.
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
 * AdminMenu
 *
 * Menu for the admin. Just for the admin. U no admin ? U no menu.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
 class AdminMenu
 {
 	/// Menu options
 	public $options=array();

 	/**
 	 * Build AdminMenu
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function __construct(){
 		$this->options['Sta']	= "View Statistics";
 	 	$this->options['Set']	= "Edit Settings";
 	 	$this->options['EdA']	= "Edit Accounts";
 	}
 
 	/**
 	 * Display AdminMenu on website
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function toHTML(){

		foreach($this->options as $op=>$val){
			if( $_GET['a'] == $op){
				$class = "menu_item selected";
			}else{
				$class = "menu_item";
			}
 			echo "<div class='$class'>\n";
			echo "<div class='menu_title'>\n";
			echo "<a href='?t=Adm&a=$op'>$val</a>";
			echo "</div>\n</div>\n";
 		}
		echo "<div class='menu_item'>\n";
		echo "<div class='menu_title'>\n";
		echo "<a href='.'>Back</a>";
		echo "</div>\n</div>\n";

 	}

 }
 ?>