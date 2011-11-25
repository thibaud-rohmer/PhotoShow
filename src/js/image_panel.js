function load_image(img){
		$(".image_panel").load(img);
}

function init_image_panel(){

	$("#bigimage a, #image_bar #back").unbind();
	$(".linear_panel .item a, #image_bar #next a, #image_bar #prev a").unbind();
	$(".linear_panel").unbind();

	// On clicking the bigimage
	$("#bigimage a, #image_bar #back").click(function(){

		// Edit layout
		$(".image_panel").slideUp();
		$(".linear_panel").addClass("panel").removeClass("linear_panel");

		init_panel();
		return false;
	});

	// On clicking an item
	$(".linear_panel .item a").click(function(){
		$(".linear_panel .selected").removeClass("selected");
		$(this).parent().addClass("selected");
		load_image($(this).attr("href")+"&j=1");
		return false;
	});

	// On clicking NEXT
	$("#image_bar #next a").click(function(){
		load_image($(this).attr("href")+"&j=1");

		var curr_select = $(".linear_panel .selected");
		var new_select 	= curr_select.next();

		if(! new_select.length){
			new_select = curr_select.parent().next().children(".item").first();
		}

		if(! new_select.length){
			new_select = $(".linear_panel .item").last();
		}

		curr_select.removeClass("selected");
		new_select.addClass("selected");
		
		return false;
	});

	// On clicking PREV
	$("#image_bar #prev a").click(function(){
		load_image($(this).attr("href")+"&j=1");

		var curr_select = $(".linear_panel .selected");
		var new_select 	= curr_select.prev();

		if(! new_select.length){
			new_select = curr_select.parent().prev().children(".item").last();
		}

		if(! new_select.length){
			new_select = $(".linear_panel .item").first();
		}

		curr_select.removeClass("selected");
		new_select.addClass("selected");
		
		return false;
	});



	// On mousewheelling
	$(".linear_panel").mousewheel(function(event,delta){
		this.scrollLeft -= delta * 30;
		event.preventDefault();
	});

}

$("document").ready(function(){
	init_image_panel();
});