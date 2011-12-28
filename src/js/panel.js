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
 function init_panel(){

	$(".panel .item a").unbind();

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

	$(".dir_img").mousemove(function(e){
		var i = $(this).children(".alt_dir_img");
		var x = Math.floor(i.length * (e.pageX - $(this).offset().left) / $(this).width());
		var img = $(i[x]).text();

		e = $(this);
		if(e.children(".img_bg").text() != img){
			e.children(".img_bg").html(img);
			$.get("?t=Thb&f="+img,function(){
				$(e).css("background-image","url(\"?t=Thb&f="+img+"\")");			
			});
		}
	});


	init_admin();
}

function init_hiders(){
	$("#infos_hide").click(function(){
		if ( $('.infos').is(':visible')){
			$('.infos').hide("slide",{direction:"right"},600);
			$(this).animate({right:'0'},600);
			$(".center").animate({right:'12'},600);
		}else{
			$('.infos').show("slide",{direction:"right"},600);
			$(this).animate({right:'249'},600);
			$(".center").animate({right:'260'},600);
		}
	});

	$("#menu_hide").click(function(){
		if ( $('.menu').is(':visible')){
			$('.menu').hide("slide",{direction:"left"},600);
			$(this).animate({left:'0'},600);
			$(".center").animate({left:'12'},600);
		}else{
			$('.menu').show("slide",{direction:"left"},600);
			$(this).animate({left:'240'},600);
			$(".center").animate({left:'250'},600);
		}
	});
}

function info_hide() {
if ( $('.infos').is(':visible')){
$('.infos').hide("slide",{direction:"right"},600);
$('#infos_hide').animate({right:'0'},600);
$(".center").animate({right:'12'},600);
}else{
$('.infos').show("slide",{direction:"right"},600);
$('#infos_hide').animate({right:'249'},600);
$(".center").animate({right:'260'},600);
}
}

function menu_hide() {
if ( $('.menu').is(':visible')){
$('.menu').hide("slide",{direction:"left"},600);
$('#menu_hide').animate({left:'0'},600);
$(".center").animate({left:'12'},600);
}else{
$('.menu').show("slide",{direction:"left"},600);
$('#menu_hide').animate({left:'240'},600);
$(".center").animate({left:'250'},600);
}
}

$("document").ready(function(){
	init_infos();
	init_panel();
	init_hiders();
	$(".menu").scrollTo($(".menu .selected:last"));
});