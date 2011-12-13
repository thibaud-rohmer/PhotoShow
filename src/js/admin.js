/**
 * This file implements admin.
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

	$(".menu_title").draggable({
		cursor: 		"move",
		zIndex: 		1000,
		helper: 		'clone',
		appendTo: 		'body',
		scroll: 		false,
		revert: 		true
	});

	$(".directory").draggable({
		cursor: 		"move",
		zIndex: 		1000,
		opacity: 		0.5,
		helper: 		'clone',
		appendTo: 		'body',
		scroll: 		false,
		revert: 		true
	});

	$(".item").draggable({
		cursor: 		"move",
		zIndex: 		1000,
		helper: 		'clone',
		cursorAt: 		{left:50,top:64},
		opacity: 		0.5,
		appendTo: 		'body',
		scroll: 		false,
		revert: 		true
	});

	$(".menu_title").droppable({
		hoverClass: "hovered",
		drop: 		function(event, ui){
						var dragg = ui.draggable;

						dragg.draggable('option','revert',false);
						from  = dragg.children(".path").text();
						to 	  = $(this).children(".path").text();

						if($(dragg).hasClass("item")){
							$(".panel,.linear_panel").load(".?t=Adm&a=Mov&j=Pan",{'pathFrom' : from,'pathTo' : to, 'move':'directory'},init_menu);	
						}else{
							$(".menu").load(".?t=Adm&a=Mov&j=Men",{'pathFrom' : from,'pathTo' : to, 'move':'directory'},init_menu);						
						}
					}
	});

	$(".bin").droppable({
		hoverClass: "hovered",
		drop: 		function(event, ui){
						var dragg = ui.draggable;
						dragg.draggable('option','revert',false);

						file  = dragg.children(".path").text();

						if($(dragg).hasClass("item")){
							$(".panel,.linear_panel").load("?t=Adm&a=Del&j=Pan",{'del' : file },init_panel);
						}else{
							$("#page").load("?t=Adm&a=Del&j=Pag",{'del' : file },function(){
								init_panel();
								init_infos();
								init_admin();
							});
						}

					}
	});

	$(".accountitem").draggable({
		cursor: 		"move",
		zIndex: 		1000,
		helper: 		'clone',
		cursorAt: 		{left:25,top:25},
		appendTo: 		'body',
		scroll: 		false,
		revert: 		true
	});

	$(".groupitem").droppable({
		hoverClass: 	"hovered",
		drop: 			function(event,ui){

							var dragg = ui.draggable;
							if($(dragg).hasClass("accountitem")){
								dragg.draggable('option','revert',false);
								acc = dragg.children(".name").text();
								group = $(this).children(".name").text();
								$(".center").load("?t=Adm&a=AGA&j=Acc",{'acc' : acc, 'group' : group },init_admin);
							}
						}
	})

	$(".rmacc").click(function(){
		group 	= $(this).parent().parent().children(".name").text();
		acc 	= $(this).parent().children(".accname").text();
		$(".center").load("?t=Adm&a=AGR&j=Acc",{'acc' : acc, 'group' : group },init_admin);
	});

	$(".rmgroup").click(function(){
		acc		= $(this).parent().parent().children(".name").text();
		group 	= $(this).parent().children(".groupname").text();
		$(".center").load("?t=Adm&a=AGR&j=Acc",{'acc' : acc, 'group' : group },init_admin);
	});

	$(".addgroup").submit(function(){
		$(".center").load($(this).attr('action') + "&j=Acc",{"group": $(this).find("input[type='text']").val() },init_admin);
		return false;
	});
}

function init_infos(){

	$('.dropzone').fileUploadUI({
		uploadTable: 		$('#files'),
		downloadTable: 		$('#files'),
		buildUploadRow: 	function (files, index) {
			return $('<tr><td>' + files[index].name + '<\/td>' +
					'<td class="file_upload_progress"><div><\/div><\/td>' +
					'<td class="file_upload_cancel">' +
					'<button class="ui-state-default ui-corner-all" title="Cancel">' +
					'<span class="ui-icon ui-icon-cancel">Cancel<\/span>' +
					'<\/button><\/td><\/tr>');
		},
		buildDownloadRow: 	function (file) {
								return;
							},
	});

	init_forms();

}

function init_forms(){

	$(".adminrights form").submit(function(){
		$.post($(this).attr('action') + "&j=Jud",$(this).serialize(),function(data){
			alert("Rights have been set, my lord.");
			$('.adminrights').html(data);
			init_forms();
		});
		
		return false;
	});
}
