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

session_start();

function __autoload($class){
	require_once(realpath(dirname(__FILE__)."/src/classes/$class.class.php"));
}

function exception_handler($exception) {
  echo "<div class='exception'>Exception : " , $exception->getMessage(), "</div>\n";
}
set_exception_handler('exception_handler');

try{
	Settings::init();
	CurrentUser::init();	
}catch(Exception $e){
	// User is not logged. Should we display a form then ?
}

switch (CurrentUser::$action){
	
	case "Page":
				$page = new Page();
				$page->toHTML();
				break;
				
	case "Img":
				Provider::Image(CurrentUser::$path);
				break;
				
	case "Thb":
				Provider::Image(CurrentUser::$path,true);
				break;
	case "Zip":
				Provider::Zip(CurrentUser::$path);
				break;
}

?>