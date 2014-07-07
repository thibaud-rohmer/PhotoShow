/**
 * This file implements image_panel.
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

/**
 * Initialise the image panel
 */
function init_image_panel(){

	$("#bigimage a, #image_bar #back").unbind();
	$(".linear_panel .item a, #image_bar #next a, #image_bar #prev a").unbind();
	$(".linear_panel").unbind();
	
	//If we are in a view mode were there is a linear panel and no image selected in that panel
	if ($('.linear_panel').length == 1 && $('.linear_panel .selected').length == 0){
		url = $('#image_big').css('background-image').replace(/^url|[\(\)\"]/g, '');
		url = url.slice(url.indexOf('f='));
		$('.linear_panel a[href$="' + url + '"]').parent().addClass("selected");
	}

	// On clicking the bigimage
	$("#bigimage a, #image_bar #back").click(function(){
		// Edit layout
		$(".image_panel,.linear_panel").slideUp(function(){
			$(".linear_panel").addClass("panel").removeClass("linear_panel").fadeIn("normal",function(){
				init_panel();
				update_url($("#back a").attr("href"),$(".header h1").text());
			});

			$(".infos").load($(".menu .selected:last a").attr("href")+"&j=Inf");
		});
		
		if(slideshow_status != 0){
			stop_slideshow();
		}
		
		return false;
	});

	// On clicking an item
	$(".linear_panel .item a").click(function(){
		$(".linear_panel .selected").removeClass("selected");
		$(this).parent().addClass("selected");
		update_url($(this).attr("href"),"Image");

		$(".image_panel").load($(this).attr("href")+"&j=Pan",function(){
			init_image_panel();
		});

		// Load infos
		$(".infos").load($(this).attr("href")+"&j=Inf");

		return false;
	});

	// On clicking NEXT
	$("#image_bar #next a").click(function(){
		var curr_select = $(".linear_panel .selected");
		var new_select 	= curr_select.next();

		if(! new_select.length){
			new_select = curr_select.parent().next().children(".item").first();
		}

		if(! new_select.length){
			new_select = $(".linear_panel .item").last();
		}
		
		new_url = new_select.children("a").attr("href");
		
		$(".image_panel").load(new_url + "&j=Pan",function(){
			update_url(new_url,"Image");
			
			curr_select.removeClass("selected");
			new_select.addClass("selected");
			
			init_image_panel();
			
			if(slideshow_status != 0){
				hide_links();
			}
		});
		 
		// Load infos
		$(".infos").load(new_url+"&j=Inf");

		return false;
	});


	// Photosphere
	$("#image_bar #photosphere").click(function(e){
		e.preventDefault();
		sphere = new Photosphere($("#imageurl").val());
		sphere.loadPhotosphere(document.getElementById("image_big"));
		return false;
	});



	// On clicking PREV
	$("#image_bar #prev a").click(function(){
		var curr_select = $(".linear_panel .selected");
		var new_select 	= curr_select.prev();
		
		if(! new_select.length){
			new_select = curr_select.parent().prev().children(".item").last();
		}
		
		if(! new_select.length){
			new_select = $(".linear_panel .item").first();
		}
		
		new_url = new_select.children("a").attr("href")
		
		$(".image_panel").load(new_url+"&j=Pan",function(){

			update_url(new_url,"Image");

			curr_select.removeClass("selected");
			new_select.addClass("selected");

			init_image_panel();

			if(slideshow_status != 0){
				hide_links();
			}
		});

		// Load infos
		$(".infos").load(new_url+"&j=Inf");

		return false;
	});

	// On mousewheelling
	$(".linear_panel").mousewheel(function(event,delta){
		if($(".linear_panel").is(":visible")){
			this.scrollLeft -= delta * 30;
			event.preventDefault();
		}
	});

	$(".linear_panel").scrollTo($(".linear_panel .selected")).scrollTo("-="+$(".linear_panel").width()/2);

	init_comments();
	init_slideshow_panel();
}

function init_comments(){
	$("#comments form").submit(function(){
		$.post($(this).attr('action') + "&j=Comm",$(this).serialize(),function(data){
			$('#comments').html(data);
			init_comments();
		});
		return false;
	});
}

$("document").ready(function(){
	init_image_panel();
});