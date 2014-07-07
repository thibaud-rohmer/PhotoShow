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
 * @link      http://github.com/thibaud-rohmer/PhotoShow
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
 * @link      http://github.com/thibaud-rohmer/PhotoShow
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
		echo "<div id='menuright-header'>";
		//echo "<a class='pure-button button-xsmall' href='http://www.photoshow-gallery.com'>PhotoShow - © Thibaud Rohmer </a>";
		echo "<div class='buttongroup-vertical'>";
		if(isset(CurrentUser::$account)){
			// User logged in
			echo "<a class='pure-button button-small' href='#'><i class='fa fa-user fa-lg' style='float:left;'></i> ".htmlentities(CurrentUser::$account->login, ENT_QUOTES ,'UTF-8')." <div style='float:right;'><i class='fa fa-caret-down fa-lg'></i></div></a>";
			if(CurrentUser::$admin){
				echo "<a class='pure-button button-small button-hidden hidden' href='?t=Adm'><i class='fa fa-cogs fa-lg' style='float:left;'></i> ".Settings::_("menubar","admin")."</a>";
			}
			echo "<a class='pure-button button-small button-hidden hidden' href='?t=Acc'><i class='fa fa-wrench fa-lg' style='float:left;'></i> Edit </a>";
			echo "<a class='pure-button button-small button-hidden hidden' href='?t=Logout'><i class='fa fa-sign-out fa-lg' style='float:left;'></i> ".Settings::_("menubar","logout")."</a>\n";
			
		}else{
			echo "<a class='pure-button button-small' href='#'><i class='fa fa-user fa-lg' style='float:left;'></i> Not logged in ! <div style='float:right;'><i class='fa fa-caret-down fa-lg'></i></div></a>";

			// User not logged in
			echo "<a class='pure-button button-small  button-hidden hidden' href='?t=Login'><i class='fa fa-sign-in fa-lg' style='float:left;'></i> ".Settings::_("menubar","login")."</a>";
			if(!Settings::$noregister){
				echo "<a class='pure-button button-small  button-hidden hidden' href='?t=Reg'><i class='fa fa-smile-o fa-lg' style='float:left;'></i> ".Settings::_("menubar","register")."</a>\n";
			}
		}
		echo "</div>";
		echo "</div>\n";
	}
}
?>