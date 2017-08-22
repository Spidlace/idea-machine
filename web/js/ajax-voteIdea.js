jQuery(function($){
	$("body").on("click", ".choix-vote button", function(){
	    $choix_button = $(this);
	    $vote_strong = $choix_button.closest('.vote-idea').find('span.nbr-votes strong');
		$choix_button.closest('.choix-vote').remove();

		if($(this).hasClass('plusun')){
			var vote = 1;
		} else if($(this).hasClass('moinsun')){
			var vote = -1;
		}

		var item_id = $choix_button.closest('.choix-vote').attr('data-item-id');


	    if((vote == 1 || vote == -1) && (item_id != undefined || item_id != null)){
	    	var data_vote = { vote : vote, item_id : item_id};
		    $.ajax({
		        type: "POST",
		        url: am_platform_add_vote,
		        data: data_vote,
		        cache: false,
		        success: function(data){
		        	$vote_strong.html(data);
		        }
		    });    
	    }
	    
	    return false;
	});
})