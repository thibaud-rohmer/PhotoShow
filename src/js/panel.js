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

$("document").ready(function(){
	init_infos();
	init_panel();
	init_admin();
});