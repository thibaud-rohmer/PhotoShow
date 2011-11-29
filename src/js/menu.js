function init_infos(){
	$(".thmb").unbind();
	$(".dropzone").unbind();

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
		},
	});

	
	init_forms();
	init_admin();

}

function init_forms(){

	$(".adminrights form").submit(function(){
		$.post($(this).attr('action') + "&j=Jud",$(this).serialize(),function(data){
			$('.adminrights').html(data);
			init_forms();
		});
		
		return false;
	});
}


$("document").ready(function(){

	/**
	 * Clicking on an item in the menu
	 */
	$("#menu a").click(function(){

		// Change selected item
		$(".menu .selected").removeClass("selected");
		$(this).parents(".menu_item").addClass("selected");			

		// Load page
		$(".panel").load($(this).attr("href")+"&j=Pag",init_panel);
		$(".infos").load($(this).attr("href")+"&j=Inf",init_infos);

		return false;
	});

	init_infos();


});


