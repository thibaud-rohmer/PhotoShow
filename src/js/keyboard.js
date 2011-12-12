/**
 * This file implements the keyboard shortcuts.
 * 
 * Javascript
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package	  PhotoShow
 * @category  Website
 * @author	  Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright 2011 Thibaud Rohmer
 * @license	  http://www.gnu.org/licenses/
 * @link	  http://github.com/thibaud-rohmer/PhotoShow
 */
$("document").ready(function(){
	$("body").keydown(function(event){

		// Right
		if(event.which == 39){
			$("#image_bar #next a").click();
			event.preventDefault();
		}

		// Left
		if(event.which == 37){
			$("#image_bar #prev a").click();
			event.preventDefault();
		}

				// Left
		if(event.which == 27){
			$("#image_bar #back a").click();
			event.preventDefault();
		}

	});

});