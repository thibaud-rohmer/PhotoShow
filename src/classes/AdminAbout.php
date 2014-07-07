<?php
/**
 * This file implements the class AdminAbout.
 * 
 * PHP versions 4 and 5
 *
 * LICENSE:
 * 
 * This file is part of PhotoShow.
 *
 * PhotoShow is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhotoShow is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright 2011 Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

/**
 * AdminAbout
 *
 * About page
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */
 class AdminAbout
 {

 	/**
 	 * Create about page
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function __construct(){

 	}

 	/**
 	 * Display upload page on website
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function toHTML(){

 		echo "<div class='header'>";
 		echo "<h1>About</h1>";
 		echo "</div>";

 		echo "<h2>PhotoShow</h2>";
 		echo "<div class='pure-g'><div id='me' class='pure-u-1-5' style='text-align:right;'></div>";
 		echo "<div class='pure-u-4-5'><ul>";
 		echo "<a href='http://www.photoshow-gallery.com'>PhotoShow-Gallery.com</a><br/>\n";
 		echo "<a href='https://github.com/thibaud-rohmer/PhotoShow'>PhotoShow on GitHub</a><br/>\n";
 		echo "<a href='https://github.com/thibaud-rohmer/PhotoShow/wiki/Tips'>Tips !</a><br/>\n";
 		echo "</ul></div></div>\n";

 		echo "<h2>Me</h2>";
 		echo "<div class='pure-g'><div id='me' class='pure-u-1-5' style='text-align:right;'><img src='inc/me.jpg' width='100px' style='border-radius:5px; -moz-border-radius:5px;'></div>";
 		
 		echo "<div class='pure-u-4-5'><ul>";
 		echo "<a href='mailto:thibaud.rohmer@gmail.com'>email</a><br/>\n";
 		echo "<a href='http://twitter.com/#osi_iien'>Twitter</a><br/>\n";
 		echo "<a href='https://github.com/thibaud-rohmer/'>GitHub</a><br/>\n";
 		echo "<a href='https://plus.google.com/114933352963292387937/about'>Google Profile</a><br/>\n";
 		echo "</ul></div></div>\n";

 		echo "<h2>If you like PhotoShow ... </h2>";

 		echo "<div class='pure-g'><div id='me' class='pure-u-1-5' style='text-align:right;'></div>";
 		echo "<div class='pure-u-4-5'><ul>";
 		echo "Spread the word ! Tell it to your friends :)<br/>\n";
		echo "Make sure to go to <a class='pure-button pure-button-primary' href='www.photoshow-gallery.com'>PhotoShow-Gallery</a> and like/+1/tweet the page.<br/>";
 		echo "<br/>";
 		echo '	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="EJCH63L4226YN">
		<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>';
	 		echo "</ul>\n";
	 	echo "</div>";	 	
	 	echo "</div>";
 	}

 }
 ?>