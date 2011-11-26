/**
 * This file implements admin.
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


$("document").ready(function(){

	$(".panel > .dir .dir").draggable({
		containment: 	".panel > .dir",
		cursor: 		"move",
		revert: 		true
	});

	$(".dir span").droppable({
		hoverClass: "hovered",
		drop: 		function(event, ui){
						var dragg = ui.draggable;
						if(window.confirm("Do you want to move " + dragg.children("span").text() + " to "+$(this).text() + " ?")){

							dragg.draggable('option','revert',false);
							from  = dragg.children("span").attr("class").split(' ')[0];
							to 	  = $(this).attr("class").split(' ')[0];
							$(".panel").load(".?t=Adm&a=Mov&j=1",{'pathFrom' : from,'pathTo' : to, 'move':'directory'});

						}else{
							// not paf.
						}
					}
	});

	$(".panel > .dir .dir span").dblclick(function(){
		$(".foc").parents("span").text($(".foc").val());

		oldname = $(this).text();
		oldpath = $(this).attr("class").split(' ')[0];
		newpath = $(this).parent().parent().children("span").first().attr("class").split(' ')[0];
		$(this).html("<form class='js'><input class='foc' type='text' value='" + $(this).text() + "'></input></form>");

		$("form").submit(function(){
			newname = $(this).children(".foc").val();
			if(window.confirm("Do you want to rename " + oldname + " to "+ newname + " ?")){

				from  = oldpath;
				to 	  = newpath+"/"+newname;
//				alert("move "+from+" to "+to);
				$(".panel").load(".?t=Adm&a=Mov&j=1",{'pathFrom' : from,'pathTo' : to, 'move':'rename'});

			}else{
				$(this).html(oldname);
			}
			return false;
		});
		$(".foc").focus();
	});

});