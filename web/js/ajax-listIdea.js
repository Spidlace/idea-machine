jQuery(function($){
	$(".list-idea a.afficher-la-suite").click(function(){
	    if(typeof id_user == 'undefined') id_user = '';

	    page_data = parseInt($(this).attr('data-page'));
	    page_max_data = parseInt($(this).attr('data-page-max'));
	    $als = $(this);

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
		        }
		    });    
	    }
	    
	    return false;
	});
})