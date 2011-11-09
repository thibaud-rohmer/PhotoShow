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

class ImagePanel
{
	public $file;
	private $image;
	
	public function __construct($file=NULL){
		$this->file=$file;
		$this->image=new Image($file);
	}

	public function __toString(){
		echo "<div id='top'>\n";
		echo "<div id='exif' class='box'>\n";
	//	new Exif($file);
		echo "</div>\n";

		echo "<div id='center'>\n";
		echo $this->image;
		echo "</div>\n";
		echo "<div id='bar'>\n";

		echo "</div>\n";
		echo "<div id='comments' class='box'>\n";
	//	new Comments($file);
		echo "</div>\n";
		return "</div>\n";
	}
	
}
?>