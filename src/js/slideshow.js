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
var timer = {
    id : 0,

    make : function ( fun, delay ) {
		if ((typeof id  !== 'undefined') && (id > 0)) {
			 clearInterval(id);
		}
		
        id = setInterval.apply(
            window,
            [ fun, delay ].concat( [].slice.call(arguments, 2) )
        );

        return id;
    },
	
    clear : function () {
		if (typeof id  !== 'undefined') {
			var old = id;
			id = 0;
			return clearInterval( old );
		}
    }
};

function toggleFullScreen() {
  var doc = window.document;
  var docEl = doc.getElementById("image_panel");

  var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
  var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

  if(!isFullScreen()) {
    requestFullScreen.call(docEl);
  }
  else {
    cancelFullScreen.call(doc);
  }
}

function isFullScreen() {
  var doc = window.document;
  return (doc.fullscreenElement || doc.mozFullScreenElement || doc.webkitFullscreenElement || doc.msFullscreenElement);
}

function show_image(){	
	slideshow_status = -1;
	$(".image_panel").css("position","fixed");
	$(".image_panel").css("z-index",5000);
	$(".image_panel").animate({bottom:'0'},200);
	hide_links();
	
	if(!isFullScreen()) {
	  toggleFullScreen();
	}
}

function start_slideshow(){	
	$(".image_panel").css("position","fixed");
	$(".image_panel").css("z-index",5000);
	$(".image_panel").animate({bottom:'0'},200);
	
	if(!isFullScreen()) {
	  toggleFullScreen();
	}
	
	play_slideshow();
}

function run_slideshow(){
	timer.clear();
	$("#next a").click();
}

function play_pause_slideshow(){
	if(slideshow_status == 1){
		pause_slideshow();
	}else{
		play_slideshow();
	}
}

function play_slideshow(){
	//timer = setInterval('run_slideshow()',3000);
	timer.make('run_slideshow()',3000);
	slideshow_status = 1;
	
	hide_links();
}

function pause_slideshow(){
	timer.clear();
	slideshow_status = 2;
	
	hide_links();
}

function stop_slideshow(){
	timer.clear();
	slideshow_status = 0;

	$(".image_panel").animate({bottom:'150'},200);
	$(".image_panel").css("position","absolute");
	$(".image_panel").css("z-index",50);
	
	if(isFullScreen()) {
	  toggleFullScreen();
	}	
	
	show_links();
}

function init_slideshow_panel(){
	$("#image_bar #pause").hide();
	$("#image_bar #play").hide();
	$("#image_bar #stop").hide();

	$("#img").unbind();
	$("#img").click(function(){
		show_image();
		return false;
	});

	$("#slideshow").unbind();
	$("#slideshow").click(function(){
		start_slideshow();
		return false;
	});
	
	$("#stop").unbind();
	$("#stop").click(function(){
		stop_slideshow();
		return false;
	});
	
	$("#pause").unbind();
	$("#pause").click(function(){
		pause_slideshow();
		return false;
	});
	
	$("#play").unbind();
	$("#play").click(function(){
		play_slideshow();
		return false;
	});
}

function show_links(){
	$('#image_bar #back').show();
	$('#image_bar #img').show();
	$('#image_bar #get').show();
	$('#image_bar #slideshow').show();
	$('#image_bar #pause').hide();
	$('#image_bar #play').hide();
	$('#image_bar #stop').hide();
	$('#image_bar #prev').show();
	$('#image_bar #next').show();
}

function hide_links(){
	$('#image_bar #back').hide();
	$('#image_bar #img').hide();
	$('#image_bar #get').hide();
	$('#image_bar #slideshow').hide();
	$('#image_bar #stop').show();
	if(slideshow_status == -1){
		// show image full scren
		$('#image_bar #play').hide();
		$('#image_bar #pause').hide();
		$('#image_bar #prev').show();
		$('#image_bar #next').show();	
	} else if(slideshow_status == 1){
		// play slideshow
		$('#image_bar #pause').show();
		$('#image_bar #play').hide();
		//$('#image_bar #prev').hide();
		//$('#image_bar #next').hide();	
	}else{
		//pause slideshow
		$('#image_bar #play').show();
		$('#image_bar #pause').hide();
		$('#image_bar #prev').show();
		$('#image_bar #next').show();	
	}	
}