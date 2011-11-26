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

	$(".dir .title").draggable({
		cursor: 		"move",
		containment:	".panel > .dir",
		revert: 		true
	});

	$(".newdir .title").draggable({
		cursor: 		"move",
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
            return $('<tr><td>' + file.name + '<\/td><\/tr>');
        }
    });

	$(".dropzone").droppable({
		hoverClass: "hovered",
		drop: 		function(event, ui){
						var dragg = ui.draggable;
						if($(dragg).parent().hasClass("newdir")){
							if(window.confirm("Do you want to create a new dir in "+$(this).parent().children("span").first().text() + " ?")){

								dragg.draggable('option','revert',false);
								path 	  = $(this).attr("id");
								$(".panel").load(".?t=Adm&a=Upl&j=1",{'path' : path,'newdir' : 'New Dir'});

							}else{
								// not paf.
							}
							return;
						}
						if(window.confirm("Do you want to move " + dragg.children("span").text() + " to "+$(this).parent().children("span").first().text() + " ?")){

							dragg.draggable('option','revert',false);
							from  = dragg.children("span").attr("id");
							to 	  = $(this).attr("id");
							$(".panel").load(".?t=Adm&a=Mov&j=1",{'pathFrom' : from,'pathTo' : to, 'move':'directory'});

						}else{
							// not paf.
						}
					}
	});


	$(".dropzone input").click(function(event){
		event.preventDefault();
		$(this).parent().parent().parent().children(".subdirs").toggle("normal");
	});



	$(".panel > .dir .dir span").dblclick(function(){
		$(".foc").parents("span").text($(".foc").val());

		oldname = $(this).text();
		oldpath = $(this).attr("id");
		newpath = $(this).parent().parent().parent().parent().children(".title").children("span").attr("id");

		$(this).html("<form class='js'><input class='foc' type='text' value='" + $(this).text() + "'></input></form>");

		$(".foc").focusout(function(){
			$(this).parent().parent().html(oldname);	
		});

		$("form").submit(function(){
			newname = $(this).children(".foc").val();
			if(window.confirm("Do you want to rename " + oldname + " to "+ newname + " ?")){

				from  = oldpath;
				to 	  = newpath+"/"+newname;
				$(".panel").load(".?t=Adm&a=Mov&j=1",{'pathFrom' : from,'pathTo' : to, 'move':'rename'});

			}else{
				$(this).html(oldname);
			}
			return false;
		});
		$(".foc").focus();
	});

});