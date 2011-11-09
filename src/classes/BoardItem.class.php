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

class BoardItem
{
	public $file;
	public $ratio;
	public $width;
	
	public function __construct($file,$ratio){
		$this->file	=	urlencode(File::a2r($file));
		$this->ratio	=	$ratio;
	}
	
	public function __toString(){
		$getfile	=	($this->width>25) ? "file=$this->file" : "t=thumb&file=$this->file";
			
		echo 	"<div class='board_item'";
		echo 	"style='";
		echo 	" width: 			$this->width%;";
		echo 	" background: 		url(\"src/getfile.php?$getfile\") no-repeat center center;";
		echo 	" background-size: 	cover;";
		echo 	"'>\n";

		echo 	"<a href='?f=$this->file'>";
		echo 	"<img src='./inc/img.png' width='100%' height='100%'>";
		echo 	"</a>\n";
		return 	"</div>\n";
	}
	
	public function set_width($r){
		$this->width = 90 * $this->ratio / $r;		
	}
}

?>