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

        if (Settings::$forcehttps && !$_SERVER["HTTPS"]){
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
            exit();
        }else{

            if(!$this->included){
                echo "<div class='center'>\n";

                $this->header();


                if($this->admin_account){
                    echo "<h1>".Settings::_("register","mainacc")."</h1>";						
                }else{
                    echo "<h1>".Settings::_("register","register")."</h1>";
                }

                echo "<form method='post' action='?t=Reg'>\n";
            }else{
                echo "<form class='adduser' method='post' action='?t=Adm&a=AAc'>\n";
            }
            echo "<div class='section'><h2>Create Account</h2>";

            /// Login
            echo "<fieldset>
                <div class='fieldname'>
                    <span>".Settings::_("register","logintxt")."</span>
                </div>
                <div class='fieldoptions'>
                    <input type='text' name='login' value=''>
                </div>
            </fieldset>\n";


            /// Password
            echo "<fieldset>
                <div class='fieldname'>
                    <span>".Settings::_("register","passtxt")."</span>
                </div>
                <div class='fieldoptions'>
                    <input type='password' name='password' value=''>
                </div>
            </fieldset>\n";

            /// Verif
            echo "<fieldset>
                <div class='fieldname'>
                    <span>".Settings::_("register","veriftxt")."</span>
                </div>
                <div class='fieldoptions'>
                    <input type='password' name='verif' value=''>
                </div>
            </fieldset>\n";


            echo "<fieldset class='alignright'><input type='submit' value='".Settings::_("register","submit")."'> ";

            if(!$this->included){
                echo Settings::_("register","or")." <a class='inline' href='.'>".Settings::_("register","back")."</a>";
            }
            echo "</fieldset></form>\n";
            echo "</div>";

            if(!$this->included){
                echo "</div>\n";
            }
        }
    }
}
?>
