
$("document").ready(function(){
	$("body").keydown(function(event){

		// Right
		if(event.which == 39){
			$("#image_bar #next a").click();
			goto_selected();

			event.preventDefault();
		}

		// Left
		if(event.which == 37){
			$("#image_bar #prev a").click();
			goto_selected();
	
			event.preventDefault();
		}

	});

});