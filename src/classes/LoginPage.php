<?php
/**
 * This file implements the class LoginPage.
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
 * LoginPage
 *
 * Lets a user log in.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */

class LoginPage extends Page
{
	
	/**
	 * Create Login Page
	 *
	 * @author Thibaud Rohmer
	 */
	public function __construct(){
			
	}
	
	/**
	 * Display Login Page on website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		
		$this->header();
		echo "<div class='inc_title'>Login</div>";
		echo "<form method='post' action='?t=Log' class='niceform'>\n";
		echo "<table>\n";
		echo "<tr>\n";
		echo "<td>Login : </td>\n";
		echo "<td><input type='text' name='login'></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<tr>\n";
		echo "<td>Password : </td>\n";
		echo "<td><input type='password' name='password'></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='submit' value='Login' class='button blue'> or <a href='?t=Reg'>register</a>";
		echo "</form>\n";

	}
}
?>