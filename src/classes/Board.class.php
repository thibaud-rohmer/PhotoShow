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

class Board
{

	private $title;
	private $path;
	private $selected;
	private $files;
	private $analyzed;
	private $dirs;
	
	private $header;
	private $boardlines=array();


	
	public function __construct($path){
		$this->set_variables($path);
		$this->grid();
	}
	
	public function __toString(){
		echo "<div class='board'>";
		echo $this->header;
		
		foreach($this->boardlines as $boardline)
			echo $boardline;
			
		return "</div>";
	}
	
	
	private function set_variables($path){
		$this->selected='';
		$this->analyzed=array();
		$this->path=$path;
		
		if(is_file($path)){
			$this->selected	=	$path;
			$this->path		=	dirname($path);
		}
		
		$this->title	=	basename($this->path);
		$this->header 	= 	new BoardHeader($this->title);
		$this->files	=	Menu::list_files($this->path);
		$this->dirs		=	Menu::list_dirs($this->path);

	}
		
	private function grid(){
		$bl	=	new BoardLine();
		
		foreach($this->files as $file){
			// Check rights
			if(CurrentUser::view($file)){
				
				$ratio	=	$this->ratio($file);
				
				if($bl->ratio + $ratio > 11){
				
					$bl->end_line();
					$this->boardlines[] = $bl;
					$bl	=	new BoardLine();
				
				}
				
				$bl->add_item($file,$ratio);
			}
		}
	}

	private function ratio($file){
		if(File::type($file) != "Image")
			return 2;
		
		list($x,$y) = getimagesize($file);
		
		return floor($x/$y)+1;

	}
}