
	$("#menuLink").click(function(){
	    $("#layout").toggleClass("active");
	});

	$(".menuright-link").click(function(){
	    $("#menuright,.menuright-link").toggleClass("active");
	});

	$(".buttongroup-vertical .pure-button").click(function(){
		$(this).parent().children(".button-hidden").toggleClass("hidden");
	});

	$('#menu').animate({
	      scrollTop: ($('li.menu_title ul.selected').offset().top-$(window).height()/2)
	    }, 500);