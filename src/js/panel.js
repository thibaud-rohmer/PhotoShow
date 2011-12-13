function init_panel(){

	$(".panel .item a").unbind();

	// On clicking an item
	$(".panel .item a").click(function(){

		// Select item
		$(".panel .selected").removeClass("selected");
		$(this).parent().addClass("selected");

		// Load image
		$(".image_panel").load($(this).attr("href")+"&j=Pan",function(){
			init_image_panel($(this).attr("href"));	
		});

		// Load infos
		$(".infos").load($(this).attr("href")+"&j=Inf",function(){
			init_image_panel($(this).attr("href"));	
		});
		
		update_url($(this).attr("href"));

		// Edit layout
		$(".panel").hide().addClass("linear_panel").removeClass("panel");
		$(".image_panel,.linear_panel").slideDown("fast",function(){
			$(".image_panel a").css("height","100%");
		});


		return false;

	});

	$(".dir_img").mousemove(function(e){
		var i = $(this).children(".alt_dir_img");
		var x = Math.floor(i.length * (e.pageX - $(this).offset().left) / $(this).width());
		var img = $(i[x]).text();

		e = $(this);
		if(e.children(".img_bg").text() != img){
			e.children(".img_bg").html(img);
			$.get("?t=Thb&f="+img,function(){
				$(e).css("background-image","url(\"?t=Thb&f="+img+"\")");			
			});
		}
	});


	init_admin();
}

function init_hiders(){
	$("#infos_hide").click(function(){
		if ( $('.infos').is(':visible')){
			$('.infos').hide("slide",{direction:"right"},600);
			$(this).animate({right:'0'},600);
			$(".center").animate({right:'12'},600);
		}else{
			$('.infos').show("slide",{direction:"right"},600);
			$(this).animate({right:'249'},600);
			$(".center").animate({right:'260'},600);
		}
	});

	$("#menu_hide").click(function(){
		if ( $('.menu').is(':visible')){
			$('.menu').hide("slide",{direction:"left"},600);
			$(this).animate({left:'0'},600);
			$(".center").animate({left:'12'},600);
		}else{
			$('.menu').show("slide",{direction:"left"},600);
			$(this).animate({left:'240'},600);
			$(".center").animate({left:'250'},600);
		}
	});
}

$("document").ready(function(){
	init_infos();
	init_panel();
	init_hiders();
	$(".menu").scrollTo($(".menu .selected:last"));
});