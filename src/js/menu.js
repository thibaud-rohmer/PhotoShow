
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

		return false;
	});

});