/**
 * This file implements the JS for panels.
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

 function init_dropzone(){
 	return;
 }

 function init_panel(){

	$(".panel .item a").unbind();

	init_dropzone();

	// On clicking an item
	$(".panel .item a").click(function(){

		// Select item
		$(".panel .selected").removeClass("selected");
		$(this).parent().addClass("selected");

		// Load image
		$(".image_panel").load($(this).attr("href")+"&j=Pan",function(){
			init_image_panel($(this).attr("href"));	
		});

		// Load infos
		$(".infos").load($(this).attr("href")+"&j=Inf",function(){
			init_image_panel($(this).attr("href"));	
		});
		
		update_url($(this).attr("href"));

		// Edit layout
		$(".panel").hide().addClass("linear_panel").removeClass("panel");
		$(".image_panel,.linear_panel").slideDown("fast",function(){
			$(".image_panel a").css("height","100%");
		});


		return false;

	});
}



$("document").ready(function(){
	init_panel();
	if ($(".menu .selected:last").length > 0) $(".menu").scrollTo($(".menu .selected:last"));
});