/**
 * This file implements menu-related JS.
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
 function init_admin(){
	// Dummy function
}

function init_infos(){
	// Dummy function
}



function init_menu(){
		/**
	 * Clicking on an item in the menu
	 */
	$("#menu a").click(function(){

		// Change selected item
		$(".menu .selected").removeClass("selected");
		$(this).parents(".menu_item").addClass("selected");			

		hr = $(this).attr("href");
		// Load page
		if ($(".infos").length > 0){
			$(".infos").load(hr+"&j=Inf",function(){
				init_infos();
				$(".panel").load(hr+"&j=Pan",init_panel);
			}); 
		}else{
			$(".panel").load(hr+"&j=Pan",init_panel);
		}


		update_url($(this).attr("href"),$(this).text());
		return false;
	});
	init_menubar();
}


function init_menubar(){
	$("#menubar a").unbind();

	$("#menubar a.login").click(function(){
		$(".panel").load("?j=Log",function(){
			$(".inline").first().click(function(){
				$(".panel").load("?j=Reg");
				return false;
			});
		});
		return false;
	});

	$("#menubar a.register").click(function(){
		$(".panel").load("?j=Reg");
		return false;
	});
}

function update_url(url,name){
	if(typeof history.pushState == 'function') { 
		var stateObj = { foo: "bar" };
		history.pushState(stateObj, "PhotoShow - " + name, url);
	}
}


