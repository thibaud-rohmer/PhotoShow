function init_panel(){

	$(".panel .item a").unbind();

	// On clicking an item
	$(".panel .item a").click(function(){

		// Select item
		$(".panel .selected").removeClass("selected");
		$(this).parent().addClass("selected");

		// Load image
		$(".image_panel").load($(this).attr("href")+"&j=Pan",function(){
			init_image_panel();	
		});
		
		// Edit layout
		$(".panel").hide().addClass("linear_panel").removeClass("panel");
		$(".image_panel,.linear_panel").slideDown("fast",function(){
			$(".image_panel a").css("height","100%");
		});
		return false;

	});

	init_admin();
}

function update_url(url,name){
	var stateObj = { foo: "bar" };
	history.pushState(stateObj, "PhotoShow - " + name, url)
}

$("document").ready(function(){
	init_panel();
});