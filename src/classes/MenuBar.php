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

class MenuBar implements HTMLObject{
	
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
	public function __construct(){

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
		echo "<a href='#'>PhotoShow</a>\n";
		if(isset(CurrentUser::$account)){
			// User logged in
			echo "<div class='menubar-button'>- logged as <a href='?t=Acc'>".htmlentities(CurrentUser::$account->login)."</a></div>\n";
			echo "</div><div class='align_right'>\n";
			echo "<a href='?t=Log'>LOGOUT</a>\n";
			
			if(CurrentUser::$admin){
				echo "<a href='?t=Adm'>ADMIN</a>\n";
			}
			
		}else{
			// User not logged in
			echo "</div><div class='align_right'>\n";
			echo "<a class='login' href='?t=Log'>LOGIN</a>\n";
			echo "<a class='register' href='?t=Reg'>REGISTER</a>\n";
		}
		
		//echo "<a href='?a=rss'>RSS <img src='./inc/rss.png' height='11px'></a>\n";
		echo "</div>\n";

		echo "<span>Powered by <a href='http://osi.6-8.fr/PhotoShow'>PhotoShow</a> - © 2011 Thibaud Rohmer</span>";

		echo "</div>\n";
	}
}
?>