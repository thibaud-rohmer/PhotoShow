<?php
/*
    This file is part of PhotoShow.

    PhotoShow is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PhotoShow is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
*/

class MenuBar{
	
	private $logged_in	= false;
	private $admin		= false;
	
	public function __create(){
		$this->logged_in 	= isset(CurrentUser::$account->login);
		$this->admin		= ($this->logged_in && CurrentUser::$admin);
	}
	
	public function __toString(){
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
		return "</div>\n";
	}
}
?>