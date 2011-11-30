/**
 * This file implements image_panel.
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
 * @link	  http://github.com/thibaud-rohmer/PhotoShow-v2
 */

/**
 * Initialise the image panel
 */
function init_image_panel(){

	$("#bigimage a, #image_bar #back").unbind();
	$(".linear_panel .item a, #image_bar #next a, #image_bar #prev a").unbind();
	$(".linear_panel").unbind();

	// On clicking the bigimage
	$("#bigimage a, #image_bar #back").click(function(){
		// Edit layout
		$(".image_panel,.linear_panel").slideUp(function(){
			$(".linear_panel").addClass("panel").removeClass("linear_panel").fadeIn("normal",function(){
				init_panel();
				update_url($(".menu .selected:last a").attr("href"),$(".menu .selected:last a").text());
			});
		});

		return false;
	});

	// On clicking an item
	$(".linear_panel .item a").click(function(){
		$(".linear_panel .selected").removeClass("selected");
		$(this).parent().addClass("selected");
		update_url($(this).children("a").attr("href"),"Image");

		$(".image_panel").load($(this).attr("href")+"&j=Pan",function(){
			init_image_panel();
		});
		return false;
	});

	// On clicking NEXT
	$("#image_bar #next a").click(function(){
		$(".image_panel").load($(this).attr("href")+"&j=Pan",function(){
						init_image_panel();
		});

		var curr_select = $(".linear_panel .selected");
		var new_select 	= curr_select.next();

		if(! new_select.length){
			new_select = curr_select.parent().next().children(".item").first();
		}

		if(! new_select.length){
			new_select = $(".linear_panel .item").last();
		}

		update_url(new_select.children("a").attr("href"),"Image");

		curr_select.removeClass("selected");
		new_select.addClass("selected");

		return false;
	});

	// On clicking PREV
	$("#image_bar #prev a").click(function(){
		$(".image_panel").load($(this).attr("href")+"&j=Pan",function(){
			init_image_panel();	
		});

		var curr_select = $(".linear_panel .selected");
		var new_select 	= curr_select.prev();

		if(! new_select.length){
			new_select = curr_select.parent().prev().children(".item").last();
		}

		if(! new_select.length){
			new_select = $(".linear_panel .item").first();
		}

		update_url(new_select.children("a").attr("href"),"Image");

		curr_select.removeClass("selected");
		new_select.addClass("selected");

		return false;
	});

	// On mousewheelling
	$(".linear_panel").mousewheel(function(event,delta){
		if($(".linear_panel").is(":visible")){
			this.scrollLeft -= delta * 30;
			event.preventDefault();
		}
	});

	$(".linear_panel").scrollTo($(".linear_panel .selected"));
}

$("document").ready(function(){
	init_image_panel();
});