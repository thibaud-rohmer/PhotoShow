/**
 * This file implements the slideshow.
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

var slideshow_status = 0;
var timer = 0;

function run_slideshow(){
	$("#next a").click();
}

function start_slideshow(){
	slideshow_status = 1;
	timer = setInterval('run_slideshow()',3000);
	$(".image_panel").css("position","fixed");
	$(".image_panel").css("z-index",1000);
	$(".image_panel").animate({bottom:'0'},200);
	hide_links();
}

function stop_slideshow(){
	slideshow_status = 0;
	clearInterval(timer);
	$(".image_panel").animate({bottom:'120'},200);
	$(".image_panel").css("position","absolute");
	$(".image_panel").css("z-index",50);
	$("#slideshow").unbind();
	show_links();
}

function toggle_slideshow(){
	if(slideshow_status == 1){
		stop_slideshow();
	}else{
		start_slideshow();
	}
}

function init_slideshow_panel(){
	$("#slideshow").unbind();

	$("#slideshow").click(function(){
		toggle_slideshow();
		return false;
	});

	$("#back").click(function(){
		stop_slideshow();
	});
}

function show_links(){
	$('#image_bar #prev').show();
	$('#image_bar #back').show();
	$('#image_bar #next').show();
}

function hide_links(){
	$('#image_bar #prev').hide();
	$('#image_bar #back').hide();
	$('#image_bar #next').hide();
}