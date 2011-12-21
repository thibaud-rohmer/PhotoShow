<?php
/**
 * This file implements the class RegisterPage.
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
 * RegisterPage
 *
 * This is the page that lets the user create an account.
 * If there is no account created yet, the acount created
 * here will be the admin.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */


class RegisterPage extends Page
{
	
	private $admin_account;

	private $included;

	/**
	 * Create Register Page
	 *
	 * @author Thibaud Rohmer
	 */
	public function __construct($admin_account = false, $included = false){
		$this->admin_account = $admin_account;		
		$this->included 	 = $included;
	}
	
	/**
	 * Display Register Page on website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){

		if(!$this->included){
			echo "<div class='center'>\n";

			$this->header();
			
			if($this->admin_account){
				echo "<h1>".Settings::_("register","mainacc")."</h1>";						
			}else{
				echo "<h1>".Settings::_("register","register")."</h1>";
			}
			
			echo "<form id='register' method='post' action='?t=Reg'>\n";
		}else{
			echo "<form id='register' class='adduser' method='post' action='?t=Adm&a=AAc'>\n";
		}
		echo "<span>".Settings::_("register","logintxt")."</span>";
		echo "<fieldset><span>".Settings::_("register","login")."</span>";
		echo "<div><input id='login' type='text' name='login' class='validate[required,custom[onlyLetterNumber],ajax[userNotExists]]'></div></fieldset>\n";
		echo "<span>".Settings::_("register","passtxt")."</span>";
		echo "<fieldset><span>".Settings::_("register","pass")."</span>\n";
		echo "<div><input id='password' type='password' name='password' class='validate[required,minSize[6]]'></div></fieldset>\n";
		echo "<span>".Settings::_("register","veriftxt")."</span>";
		echo "<fieldset><span>".Settings::_("register","verif")."</span>\n";
		echo "<div><input id='verif' type='password' name='verif' class='validate[required,equals[password]]'></div></fieldset>\n";
		echo "<input type='submit' value='".Settings::_("register","submit")."'> ".Settings::_("register","or")." <a class='inline' href='.'>".Settings::_("register","back")."</a>";
		echo "</form>\n";

		if(!$this->included){
			echo "</div>\n";
		}
	}
}
?>