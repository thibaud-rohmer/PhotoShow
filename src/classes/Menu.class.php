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

class Menu
{
	public $title;
	public $selected_class;
	private $selected;
	private $contents;
	private $webdir;
	private $items=array();
	
	public function __construct($dir,$selected){
		if(!CurrentUser::view($dir)) return;
				
		$this->title = basename($dir);
		$this->webdir=urlencode(File::a2r($dir));

		try{
			// Check if selected dir is in $dir
			File::a2r($selected,$dir);
			$this->selected			=	true;
			$this->selected_class 	=	"selected";
		}catch(Exception $e){
			// Selected dir not in $dir
			$this->selected			=	false;
			$this->selected_class 	=	"";
		}

		foreach($this->list_dirs($dir) as $d){
			$this->items[]	=	new Menu($d,$selected);
		}
	}
	
	public function __toString(){
		echo "<div class='menu_item $this->selected_class'>\n";
		echo "<div class='menu_title'><a href='?f=$this->webdir'>$this->title</a></div>\n";
		echo "<div class='menu_content'>\n";
		foreach($this->items as $item)
			echo $item;
		return "</div>\n</div>\n";
		
	}
	
	public static function list_dirs($dir){
		$list=array();
		if(!is_dir($dir)) 
			throw new Exception("$dir is not a directory");
		$dir_content = scandir($dir);					
		foreach ($dir_content as $content){
			if($content[0] != '.'){
				$path=$dir."/".$content;
				if(is_dir($path))
					$list[]=$path;
			}
		}
		return $list;
	}
	
	public static function list_files($dir){
		$list=array();
		if(!is_dir($dir)) 
			throw new Exception("$dir is not a directory");
		$dir_content = scandir($dir);					
		foreach ($dir_content as $content){
			if($content[0] != '.'){
				$path=$dir."/".$content;
				if(is_file($path))
					$list[]=$path;
			}
		}
		return $list;
	}
}
?>