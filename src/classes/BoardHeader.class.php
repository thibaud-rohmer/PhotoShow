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

class BoardHeader{
	
	public $title;
	
	public function __construct($title){
		$this->title 	=	$title;
	}
	
	public function __toString(){
		echo 	"<div class='header'>";
		echo 	"<div class='title'>$this->title</div>";
		
		echo 	"<div class='buttons'>";
		echo 	"<div class='button blue'><a href='inc/?a=zipf=$rp'>ZIP</a></div>\n";
	
		if(isset(CurrentUser::$account->login)){
			if(CurrentUser::$account->admin){
				echo 	"<div class='button orange'><a href='inc/admin.php?f=upload'>Upload Photos</a></div>\n";
			}
		}
		echo 	"</div>\n";
		return 	"</div>\n";
	}
}

?>