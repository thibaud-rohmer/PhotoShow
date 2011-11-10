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

class BoardLine
{
	public $items;
	public static $ratio;
	
	public function __construct(){
		$ratio=0;
	}
	
	public function toHTML(){
		echo "<div class='boardline'>\n";
		foreach($this->items as $item)
			echo $item;
		echo "</div>\n";
	}
	
	public function add_item($file,$ratio){	
		$this->items[]	=	new BoardItem($file,$ratio);
		$this->ratio	=	$ratio + $this->ratio;
	}
	
	public function end_line(){
		foreach($this->items as $item)
			$item->set_width($this->ratio);
	}
}

?>