
	$("#menuLink").click(function(){
	    $("#layout").toggleClass("active");
	});

	$(".menuright-link").click(function(){
	    $("#menuright,.menuright-link").toggleClass("active");
	});

	$(".buttongroup-vertical .pure-button").click(function(){
		$(this).parent().children(".button-hidden").toggleClass("hidden");
	});


