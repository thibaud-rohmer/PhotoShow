function init_panel(){

	$(".panel .item a").unbind();

	// On clicking an item
	$(".panel .item a").click(function(){

		// Select item
		$(".panel .selected").removeClass("selected");
		$(this).parent().addClass("selected");

		// Load image
		$(".image_panel").load($(this).attr("href")+"&j=1")
		
		// Edit layout
		$(".panel").hide().addClass("linear_panel").removeClass("panel");
		$(".image_panel,.linear_panel").fadeIn("slow");
		return false;

	});
}

$("document").ready(function(){
	init_panel();
});