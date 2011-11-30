function init_admin(){
	// Dummy function
}

function init_infos(){

	$(".dropzone").unbind();

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

function init_menu(){
		/**
	 * Clicking on an item in the menu
	 */
	$("#menu a").click(function(){

		// Change selected item
		$(".menu .selected").removeClass("selected");
		$(this).parents(".menu_item").addClass("selected");			

		// Load page
		$(".panel").load($(this).attr("href")+"&j=Pan",init_panel);
		$(".infos").load($(this).attr("href")+"&j=Inf",init_infos);

		update_url($(this).attr("href"),$(this).text());
		return false;
	});

	init_admin();

}

function update_url(url,name){
	var stateObj = { foo: "bar" };
	history.pushState(stateObj, "PhotoShow - " + name, url);
}

$("document").ready(function(){
	init_menu();
	init_infos();
	init_panel();
});


