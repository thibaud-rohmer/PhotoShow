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


function init_forms(){
	$(".rename").submit(function(){
		pathFrom = $(this).children("fieldset").children("input").attr("class");
		pathTo 	 = $(this).children("fieldset").attr("class") + "/" + $(this).children("fieldset").children("input").val();
		$(".panel").load("?t=Adm&a=Mov&j=1",{"pathFrom": pathFrom,"pathTo":pathTo,"move":"rename"});

		return false;
	});

	$(".create").submit(function(){
		newdir = $(this).children("fieldset").children("#foldername").val();
		path = $(this).children("fieldset").children("input[type='hidden']").val();
		$(".panel").load("?t=Adm&a=Upl&j=1",{ "path":path, "newdir": newdir});
		return false;
	});

	$(".adminrights form").submit(function(){
		$.post($(this).attr('action') + "&j=1",$(this).serialize(),function(data){
			$('.adminrights').html(data,init_forms());
		});
		
		return false;
	});
}

$("document").ready(function(){

	$(".dir .title").draggable({
		cursor: 		"move",
		zIndex: 		1000,
		helper: 		'clone',
		appendTo: 		'body',
		scroll: 		false,
		revert: 		true
	});

	$(".title").droppable({
		hoverClass: "hovered",
		drop: 		function(event, ui){
						var dragg = ui.draggable;
						if(window.confirm("Do you want to move " + dragg.children("span").text() + " to "+$(this).children("span").text() + " ?")){

							dragg.draggable('option','revert',false);
							from  = dragg.children("span").attr("class");

							to 	  = $(this).children("span").attr("class");

							$(".panel").load(".?t=Adm&a=Mov&j=1",{'pathFrom' : from,'pathTo' : to, 'move':'directory'});

						}else{
							// not paf.
						}
					}
	});

	$(".bin").droppable({
		hoverClass: "hovered",
		drop: 		function(event, ui){
						var dragg = ui.draggable;
						if(window.confirm("Do you want to delete " + dragg.children("span").text() + " ?")){

							dragg.draggable('option','revert',false);
							file  = dragg.children("span").attr("class");

							$(".panel").load(".?t=Adm&a=Del&j=1",{'del' : file });

						}else{
							// not paf.
						}
					}
	});


	$(".title").click(function(event){

		$(this).parent().toggleClass("open").children(".subdirs").toggle("normal");
		val = $(this).children("span").attr("id");
		
		$(".infos").load("?t=Inf&j=1&f="+val,function(){

			$(".thmb").draggable({
				cursor: 		"move",
				cursorAt: 		{left: 30, top: 30},
				opacity: 		0.6,
				zIndex: 		1000,
				helper: 		'clone',
				appendTo: 		'body',
				scroll: 		false,
				revert: 		true
			});

			$('.dropzone').fileUploadUI({
				uploadTable: $('#files'),
				downloadTable: $('#files'),
				buildUploadRow: function (files, index) {
					return $('<tr><td>' + files[index].name + '<\/td>' +
							'<td class="file_upload_progress"><div><\/div><\/td>' +
							'<td class="file_upload_cancel">' +
							'<button class="ui-state-default ui-corner-all" title="Cancel">' +
							'<span class="ui-icon ui-icon-cancel">Cancel<\/span>' +
							'<\/button><\/td><\/tr>');
				},
				buildDownloadRow: function (file) {
				return;
				}
			});

			init_forms();
		});
	});


});