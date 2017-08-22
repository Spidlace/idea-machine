jQuery(function($){
	$('.open-connect').click(function(){
		$(".menu-right-co .connect-wrapper form").stop().slideToggle("slow");
	})

	$(document).ready(function(){
		width = $("html").width();
		
		if(width <= 600){
			$('.toggleMenu a.icon-menu').click(function(){
				$('.menu-right-co').slideToggle("slow");
				return false;
			})
		}
	})

	$(window).resize(function(){
		width = $("html").width();
		if(width > 600){
			$('.menu-right-co').css('display', 'block');
		}
	});
})