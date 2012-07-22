function readyselect(){
	$("#multiselectbutton").unbind();
	$("#multiselectbutton").click(function(){
		$("#selection_overlay").show();
		$("#multiselectbutton").css("color","yellow");

		$(".selectzone").mousedown(function(init){
			// init .select
			var start = init

			$(".select").show();
			$(".select").css("left",start.pageX+"px");
			$(".select").css("top",start.pageY+"px");
			$(".select").width(0);
			$(".select").height(0);


			$(".selectzone").mousemove(function(e){
				if(e.pageX > start.pageX){
					$(".select").width((e.pageX - start.pageX)+"px");
					var startx 	= 	start.pageX;
					var endx 	=	e.pageX;
				}else{
					$(".select").css("left",e.pageX+"px");
					$(".select").width((- e.pageX + start.pageX)+"px");
					startx 	= 	e.pageX;
					endx 	=	start.pageX;
				}
				if(e.pageY > start.pageY){
					$(".select").height((e.pageY - start.pageY)+"px");
					starty 	= 	start.pageY;
					endy 	=	e.pageY;
				}else{
					$(".select").css("top",e.pageY+"px");
					$(".select").height((- e.pageY + start.pageY)+"px");
					starty 	= 	e.pageY;
					endy 	=	start.pageY;
				}
				select(startx,starty,endx,endy);
			});

			$(".selectzone").mouseup(function(sel){
				// stop .select
				$(".selectzone").unbind();
				$(".selectzone").mouseup(function(){
					$(".selectzone").unbind();
					$(".item.selected").removeClass("selected");
					$(".infos").load($(".menu .selected:last a").attr("href")+"&j=Inf",function(){
						init_infos();	
					});
				});
				$(".select").fadeOut("slow");
				$("#selection_overlay").hide();

				var loader = "?";
				$(".item.selected a").each(function(){
					loader = loader + $(this).attr("href").replace("?f=","&f[]=");
				});

				$(".infos").load(loader+"&j=Inf",function(){
					init_infos();
				});

				$("#multiselectbutton").css("color","white");

			});
		});
	});
}


function select(startx,starty,endx,endy){
	$(".item").each(function(){
		centrex = $(this).offset().left + $(this).width() / 2;
		centrey = $(this).offset().top + $(this).height() / 2;
		if(
			(
				centrex > startx - $(this).width() / 2
			&&	centrex < endx + $(this).width() / 2
			&&	centrey > starty - $(this).height() / 2
			&&	centrey < endy + $(this).height() / 2
			)
		){
			$(this).addClass("selected");
		}else{
			$(this).removeClass("selected")
		}
	})
}