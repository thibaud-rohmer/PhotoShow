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
	private $exif;
	private $comments;
	
	public function __construct($file=NULL){
		$this->file=$file;
		$this->image	=	new Image($file);
		$this->exif		=	new Exif($file);
		$this->comments	=	new Comments($file);

	}

	public function toHTML(){
		echo "<div id='top'>\n";
		$this->exif->toHTML();

		echo "<div id='center'>\n";
		$this->image->toHTML();
		echo "</div>\n";

		$this->comments->toHTML();


		echo "</div>\n";
	}
	
}
?>