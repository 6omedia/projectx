jQuery(document).ready(function($){

	(function(){

		var ip = $('#tracking_info').data('ip');
		var page_url = $('#tracking_info').data('curr_page');
		var title = $('#tracking_info').data('page_title');
		var outcomeid = $('#tracking_info').data('outcomeid');
		var campaignid = $('#tracking_info').data('campaignid');
		var px_post_id = $('#tracking_info').data('px_post_id');
		var funnel_position = $('#tracking_info').data('funnel_position');

		$.ajax({
	        type: "post",
	        dataType: "json",
	        url: trackAjax.ajaxurl,
	        data: {
	        	action: "px_track_page", 
	        	ip: ip,
	        	page_url: page_url,
	        	title: title,
	        	outcomeid: outcomeid,
	        	px_post_id: px_post_id,
	        	funnel_position: funnel_position,
	        	campaignid: campaignid
	        //	outcomes: JSON.stringify(outcomes)
	        },
	        success: function(response) {

	        	// console.log('response: ', response);
	        	if(response.success == 1){
	        		// successMethod(campaign_title, response.campaignid);
	        	}

	     	},
	     	error: function(a,b,c){
	     		// console.log(a,b,c);
	     	}
	  	});

	})();

	$('.ad_track').on('click', function(){

		var advert_id = $(this).data('advert_id');
		var px_post_id = $(this).data('post_id');
		var page_url = $(this).data('page_url');
		var ip = $(this).data('ip');

		// ajax call
		$.ajax({
	        type: "post",
	        dataType: "json",
	        url: trackAjax.ajaxurl,
	        data: {
	        	action: "px_track_click",
	        	advert_id: advert_id,
	        	px_post_id: px_post_id,
	        	page_url: page_url,
	        	ip: ip
	        },
	        success: function(response) {

	        	console.log('response: ', response);
	        	if(response.success == 1){
	        		// successMethod(campaign_title, response.campaignid);
	        	}

	     	}
	  	});

	});

});