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

require_once realpath(dirname(__FILE__).'/../src/settings.php');


$settings=get_settings();
$feeds=array('albums','comments');

echo "<div id='rss'>\n<div class='inc_title'>RSS Feeds</div>\n";

foreach ($feeds as $f){
	if(isset($settings["rss_$f"]) && file_exists($settings["rss_$f"])){
		$path=$settings["rss_$f"];
		echo "<div class='button orange'><a href='$path'>$f</a></div>\n";
	}
}

echo "</div>\n";
?>