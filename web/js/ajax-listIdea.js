jQuery(function($){
	$(".list-idea a.afficher-la-suite").click(function(){
	    $als = $(this);
	    $als.addClass('loader');
	    $als.append('<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><path d="M25.251,6.461c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615V6.461z"><animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/></path></svg>')
	    $als_svg = $als.find('svg');
	    
	    if(typeof id_user == 'undefined') id_user = '';

	    page_data = parseInt($(this).attr('data-page'));
	    page_max_data = parseInt($(this).attr('data-page-max'));

	    if(page_data > 0){
	    	var data_page = { page : page_data, page_max : page_max_data, user_id : id_user };
		    $.ajax({
		        type: "POST",
		        url: am_platform_get_other_idea,
		        data: data_page,
		        cache: false,
		        success: function(data){
		        	page_data = page_data+1;
		        	$als.attr('data-page', page_data);
		        	$('.list-idea ul').append(data);
		        	if(page_data == page_max_data){
		        		$als.remove();
		        	}
		        	$als.removeClass('loader');
		        	$als_svg.remove();
		        }
		    });    
	    }
	    
	    return false;
	});
})