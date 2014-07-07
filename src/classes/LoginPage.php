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
 * @link      http://github.com/thibaud-rohmer/PhotoShow
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
 * @link      http://github.com/thibaud-rohmer/PhotoShow
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

        if (Settings::$forcehttps && !$_SERVER["HTTPS"]){
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
            exit();
        }else{
            $this->header();
            echo "<div class='header'>";
            echo "<h1>".Settings::_("login","logintitle")."</h1>";
                        echo "</div>";

            echo "<div class='center'>\n";
            echo "<form method='post' action='?t=Login' class='pure-form pure-form-aligned niceform'>\n";
            echo "<fieldset>
                 <div class='pure-control-group'>
                 <label>".Settings::_("login","login")."</label>
                    <input type='text' name='login' value='' placeholder='".Settings::_("login","login")."'>
                </div>
                 <div class='pure-control-group'>
                 <label>".Settings::_("login","pass")."</label>
                    <input type='password' name='password' value='' placeholder='".Settings::_("login","pass")."'>
                    </div>
                 <div class='pure-control-group'>
                    <input type='submit' class='pure-button pure-button-primary' value='".Settings::_("login","submit")."'>
                </div>
            </fieldset>
            </form>\n";

	   
            if (!Settings::$noregister){
               echo " <a class='pure-button button-success' href='?t=Reg'>".Settings::_("login","register")."</a> ".Settings::_("login","or");
            }
            echo " <a class='pure-button button-warning' href='.'>".Settings::_("login","back")."</a>"; echo "</fieldset></form>\n";
            echo "</div>\n";
        }
    }
}
?>
