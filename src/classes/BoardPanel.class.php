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

class BoardPanel
{
	static public $images		=	0;
	static public $max_images	=	50;
	static public $offset		=	0;
	
	private $board;
	private $menu;
	private $class;
	
	public function __construct($file,$class){

		if(isset($_SESSION['max_images'])){
			$max_images=$_SESSION['max_images'];
		}

		$settings	=	new Settings();

		$this->board=	new Board($file);
		$this->menu	=	new Menu($settings->photos_dir,$file);
		$this->class=	$class;
	}

	public function toHTML(){
		
		// Menu
		echo "<div id='menu'>\n";
		$this->menu->toHTML();
		echo "</div>\n";

		// Boards
		echo "<div id='boards_panel' class='$this->class'>\n";
		$this->board->toHTML();
		echo "</div>\n";
	}
}