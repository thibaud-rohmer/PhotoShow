<?php
/**
 * This file implements the class Menubar.
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
 * Menubar
 *
 * The menubar holds some information, depending on the user.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */

class MenuBar{
	
	/// True if user is logged in
	private $logged_in	= false;
	
	/// True if user is admin
	private $admin		= false;
	
	
	/**
	 * Create menubar
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function __create(){
		$this->logged_in 	= isset(CurrentUser::$account->login);
		$this->admin		= ($this->logged_in && CurrentUser::$admin);
	}
	
	/**
	 * Display Menubar on website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		echo "<div id='menubar'>\n";
		echo "<div class='align_left'>\n";
		echo "<div class='menubar-button'><a href='http://osi.6-8.fr/PhotoShow'>PhotoShow</a></div>\n";
		
		if($logged_in){
			// User logged in
			echo "<br/>";
			echo "<div class='menubar-button'>- logged as <a href='?a=account'>".htmlentities(CurrentUser::$account->login)."</a></div>\n";
			echo "</div><div class='align-right'>\n";
			echo "<div class='menubar-button'><a href='?a=logout'>LOGOUT</a></div>\n";
			
			if($admin){
				echo "<div class='menubar-button'><a href='?a=admin'>ADMIN</a></div>\n";
			}
			
		}else{
			// User not logged in
			echo "</div><div class='align_right'>\n";
			echo "<div class='menubar-button'><a href='?a=login'>LOGIN/REGISTER</a></div>\n";
		}
		
		echo "<div class='menubar-button'><a href='?a=rss'>RSS <img src='./inc/rss.png' height='11px'></a></div>\n";
		echo "</div>\n";
		echo "</div>\n";
	}
}
?>