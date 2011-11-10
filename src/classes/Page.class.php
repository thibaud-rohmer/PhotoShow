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


class Page
{
	static public $action;
	static public $layout;
	static public $file;
	
	static private $image_class;
	static private $boards_class;
	
	static private $boardpanel;
	static private $menubar;
	static private $imagepanel;
		
	public function __construct(){		
		try{
			$settings=new Settings();
		}catch(FileException $e){
			// If Accounts File missing... Register !
			$this->header();
			new RegisterPage();
			exit;
		}
		
		$this->action	=	"thumbs";
		$this->file	=	$settings->photos_dir;
				
		
		// Setup variables
		if(isset($_GET['a']))	$this->action	=	$_GET['a'];
		if(isset($_GET['f']))	$this->file		=	File::r2a($_GET['f']);
		
		CurrentUser::$path = $this->file;
		
		if(is_file($this->file)){
			$this->image_class="";
			$this->boards_class="";
			$this->imagepanel	=	new ImagePanel($this->file);
			$this->boardpanel	=	new BoardPanel(dirname($this->file));
		}else{
			$this->image_class="hidden";
			$this->boards_class="";
			$this->imagepanel	=	new ImagePanel();
			$this->boardpanel	=	new BoardPanel($this->file);
		}

		$this->menubar 		= 	new MenuBar();
	}
	
	public function toHTML(){
		$this->header();
		echo "<body>";
		
		$this->menubar->toHTML();

		echo "<div id='container'>\n";
		
			echo "<div class='layout_boards $this->boards_class'>\n";
			$this->boardpanel->toHTML();
			echo "</div>\n";
		
			echo "<div class='layout_image $this->image_class'>\n";
			$this->imagepanel->toHTML();
			echo "</div>\n";
		
		echo "</div>\n";
		
		echo "</body>";
	}

	
	private function header(){
		echo "<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>\n";

		echo "<head>\n";
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>\n";
		echo "<title>PhotoShow</title>\n";
		echo "<meta name='author' content='Thibaud Rohmer'>\n";
		echo "<link href='http://fonts.googleapis.com/css?family=Quicksand:300' rel='stylesheet' type='text/css'>\n";
		echo "<link rel='stylesheet' href='src/stylesheet.css' type='text/css' media='screen' charset='utf-8'>\n";
		echo "</head>";
	}	
}
?>