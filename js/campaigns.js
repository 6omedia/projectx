
jQuery(document).ready(function($){

		var submitBtn = $('#submit');
		var addBtn = $('#add_btn');
		var sellingPointList = $('#selling_points');
		var input_sellingpoint = $('#input_sellingpoint');
		var outcomesToDelete = [];
		var advertsToDelete = [];

		addBtn.on('click', function(){
			addSellingPoint();
		});

		$('#selling_points').on('click', 'span', function(){
			outcomesToDelete.push($(this).parent().data('outcomeid'));
			$(this).parent().remove();
		});

		submitBtn.on('click', function(){
			createCampaign();
		});

		function removeSellingPoint(){

			alert('vncdjkvbds');

		}

		function addSellingPoint(){

			var sellingPoint = input_sellingpoint.val();

			if(sellingPoint != ''){
				$('#notices .errorNotice').hide();
				sellingPointList.append('<li data-outcomeid="null"><span></span>' + sellingPoint + '</li>');
			}else{
				displayNotice('Selling point cant be empty...', true);
			}

		}

		function createCampaign(){

			var campaign_title = $('#c_title').val();
			var description = $('#c_desc').val();
			var outcomes = [];

			$('#selling_points li').each(function( index ){
				outcomes.push($(this).text());
			});

			// console.log(outcomes);

			if(campaign_title == ''){
				displayNotice('Campaign title missing...', true);
			}else{

				$.ajax({
			        type: "post",
			        dataType: "json",
			        url: cAjax.ajaxurl,
			        data: {
			        	action: "create_campaign", 
			        	campaign_title: campaign_title,
			        	description: description,
			        	outcomes: JSON.stringify(outcomes)
			        },
			        success: function(response) {

			        	console.log('response: ', response);
			        	if(response.success == 1){
			        		successMethod(campaign_title, response.campaignid);
			        	}

		         	}
		      	});

			}  

		}

		function successMethod(campaign, campaignid){

			// add campaign added message
			// $('#notices div').html('Campaign Added').slideDown(800).delay(3000).slideUp(600);

			displayNotice("Campaign Added", false);

			// update list of campaigns
			$('#campaignList').append('<li><a href="?page=single-campaign&campaignid=' + campaignid + '">' + campaign + '</a></li>');

		}

		function displayNotice($notice, $error){
			
			$('#notices .errorNotice').hide();

			if($error){
				$('#notices .errorNotice').html($notice).slideDown(800).delay(3000); //.slideUp(600);
			}else{
				$('#notices div').html($notice).slideDown(800).delay(3000).slideUp(600);
			}
			
		}

		/* Single Campaign Page */

		$('#update_campaign').on('click', function(){
			updateCampaign();			
		});

		$('#delete_campaign').on('click', function(){
			deleteCampaign();			
		});

		function deleteCampaign(){

			var campaign_id = $('#selling_points').data('campaignid');

			$.ajax({
		        type: "post",
		        dataType: "json",
		        url: cAjax.ajaxurl,
		        data: {
		        	action: "delete_campaign", 
		        	campaign_id: campaign_id
		        },
		        // processData: false,
		        success: function(response) {

		        	console.log('response: ', response);
		        	if(response.success == 1){
		        		$('.updateForm').html('');
		        		displayNotice("Campaign Deleted", false);
		        		$('.px_container').append('<a class="back_link" href="admin.php?page=campaigns">Back to Campaigns</a>');
		        	}

	         	}
	      	});

		}

		function updateCampaign(){

			var campaign_title = $('#title_input').val();
			var description = $('#desc_input').val();
			var outcomesToAdd = [];

			$('#selling_points li').each(function( index ){

				var outcomeId = $(this).data('outcomeid');

				if(outcomeId == null){
					outcomesToAdd.push($(this).text());
				}				
				
			});

			// Ajax here to update campaign and outcomes

			var campaign_id = $('#selling_points').data('campaignid');

			$.ajax({
		        type: "post",
		        dataType: "json",
		        url: cAjax.ajaxurl,
		        data: {
		        	action: "update_campaign", 
		        	campaign_id: campaign_id,
		        	campaign_title: campaign_title,
		        	description: description,
		        	outcomesToAdd: JSON.stringify(outcomesToAdd),
		        	outcomesToDelete: JSON.stringify(outcomesToDelete)
		        },
		        // processData: false,
		        success: function(response) {

		        	// console.log('response: ', response);
		        	if(response.success == 1){
		        		displayNotice("Campaign Updated", false);
		        	}

	         	}
	      	});

		}

		/**** Post Page ****/

		// console.log('vdsvds');

		// $('#px_campaign_select').on('change', function(){

		// 	console.log('changed');

		// 	var campaign_id = $(this).find(":selected").data('campaignid');

		// 	$.ajax({
		//         type: "post",
		//         dataType: "json",
		//         url: cAjax.ajaxurl,
		//         data: {
		//         	action: "list_outcomes", 
		//         	campaign_id: campaign_id
		//         },
		//         // processData: false,
		//         success: function(response) {

		//         	console.log('response: ', response);
		//         	if(response.success == 1){
		//         		optionsString = '';

		//         		for(var i=0; i<response.outcomes.length; i++){
		//         			optionsString += '<option data-outcomeid="' + response.outcomes[i].id + '" value="' + response.outcomes[i].id + '###' + response.outcomes[i].title + '">' + response.outcomes[i].title  + '</option>';
		//         		}

		//         		$('#px_outcome_select').append(optionsString);
		//         	}
	 //         	}
	 //      	});

		// });

		////// Single outcome page

		/* Tabs */

		$('#tabs_nav li').on('click', function(){

			var tabId = $(this).data('tab-id');

			$('#tabs_nav li').removeClass('currentTab');
			$('.tab').hide();

			$(this).addClass('currentTab');
			$('#' + tabId).show();

		});

		var media_uploader = null;

		// function open_media_uploader_image(imgElem, displayImg)
		function open_media_uploader_image(imgElem, displayImg)
		{
		    media_uploader = wp.media({
		        frame:    "post", 
		        state:    "insert", 
		        multiple: false
		    });

		    media_uploader.on("insert", function(){
		        var json = media_uploader.state().get("selection").first().toJSON();

		        var image_url = json.url;
		        var image_caption = json.caption;
		        var image_title = json.title;

		        imgElem.val(image_url);

		        // displayImg.append('<img src="' + image_url + '">');

		    });

		    media_uploader.open();

		    console.log(media_uploader);
		}

		// Awareness

		$('#awareness_t_ad_btn').on('click', function(){
			open_media_uploader_image($('#awareness_t_upload'));
		});

		$('#awareness_s_ad_btn').on('click', function(){
			open_media_uploader_image($('#awareness_s_upload'));
		});

		$('#awareness_b_ad_btn').on('click', function(){
			open_media_uploader_image($('#awareness_b_upload'));
		});

		// Research

		$('#research_t_ad_btn').on('click', function(){
			open_media_uploader_image($('#research_t_upload'));
		});

		$('#research_s_ad_btn').on('click', function(){
			open_media_uploader_image($('#research_s_upload'));
		});

		$('#research_b_ad_btn').on('click', function(){
			open_media_uploader_image($('#research_b_upload'));
		});

		// Comparison

		$('#comparison_t_ad_btn').on('click', function(){
			open_media_uploader_image($('#comparison_t_upload'));
		});

		$('#comparison_s_ad_btn').on('click', function(){
			open_media_uploader_image($('#comparison_s_upload'));
		});

		$('#comparison_b_ad_btn').on('click', function(){
			open_media_uploader_image($('#comparison_b_upload'));
		});

		// Purchase

		$('#purchase_t_ad_btn').on('click', function(){
			open_media_uploader_image($('#purchase_t_upload'));
		});

		$('#purchase_s_ad_btn').on('click', function(){
			open_media_uploader_image($('#purchase_s_upload'));
		});

		$('#purchase_b_ad_btn').on('click', function(){
			open_media_uploader_image($('#purchase_b_upload'));
		});

		/* Set Adverts */

		$('#awareness_set_ad').on('click', function(){
			set_ads('awareness', $('#content_ad_list li'));
		});

		$('#research_set_ad').on('click', function(){
			set_ads('research', $('#research_c_ad_list li'));
		});

		$('#comparison_set_ad').on('click', function(){
			set_ads('comparison', $('#comparison_c_ad_list li'));
		});

		$('#purchase_set_ad').on('click', function(){
			set_ads('purchase', $('#purchase_c_ad_list li'));
		});

		function set_ads(funnel_position, ad_list){

			var outcome_id = $('#tabs_nav').data('outcomeid');
			var outcome_title = $('#outcome_title_input').val();

			var top_ad = $('#' + funnel_position + '_t_upload').val();
			var side_ad = $('#' + funnel_position + '_s_upload').val();
			var bottom_ad = $('#' + funnel_position + '_b_upload').val();
			var top_link = $('#' + funnel_position + '_t_link').val();
			var side_link = $('#' + funnel_position + '_s_link').val();
			var bottom_link = $('#' + funnel_position + '_b_link').val();

			var advertsToAdd = [];

			ad_list.each(function( index ){

				var outcomeId = $(this).data('advertid');
				var imglink = $(this).children('img');

				var src = $(imglink[0]).attr('src');
				var link = $(this).data('advertlink');
				var title = $(this).data('adtitle');

				if(outcomeId == null){
					advertsToAdd.push(src + '###' + link + '###' + title);
				}				
				
			});

			$.ajax({
		        type: "post",
		        dataType: "json",
		        url: cAjax.ajaxurl,
		        data: {
		        	action: "set_ads", 
		        	outcome_id: outcome_id,
		        	outcome_title: outcome_title,
		        	top_ad: top_ad,
		        	side_ad: side_ad,
		        	bottom_ad: bottom_ad,
		        	top_link: top_link,
		        	side_link: side_link,
		        	bottom_link: bottom_link,
		        	funnel_position: funnel_position,
		        	advertsToAdd: JSON.stringify(advertsToAdd),
		        	advertsToDelete: JSON.stringify(advertsToDelete)
		        },
		        // processData: false,
		        success: function(response) {

		        	console.log('response: ', response);
		        	if(response.success == 1){
		        		displayNotice("Adverts Set", false);
		        	}

	         	}
	      	});
		}

		/* Extra content adverts */

		$('#awareness_upload_btn').on('click', function(){
			open_media_uploader_image($('#content_ad_img_input'));
		});

		$('#add_ad_btn').on('click', function(){
			add_content_advert();
		});

		$('#content_ad_list').on('click', 'span', function(){
			advertsToDelete.push($(this).parent().data('advertid'));
			$(this).parent().remove();
		});



		$('#research_upload_btn').on('click', function(){
			open_media_uploader_image($('#content_ad_img_research'));
		});

		$('#research_ad_btn').on('click', function(){
			add_ca_research();
		});

		$('#research_c_ad_list').on('click', 'span', function(){
			advertsToDelete.push($(this).parent().data('advertid'));
			$(this).parent().remove();
		});



		$('#comparison_upload_btn').on('click', function(){
			open_media_uploader_image($('#content_ad_img_comparison'));
		});

		$('#comparison_ad_btn').on('click', function(){
			add_ca_comparison();
		});

		$('#comparison_c_ad_list').on('click', 'span', function(){
			advertsToDelete.push($(this).parent().data('advertid'));
			$(this).parent().remove();
		});



		$('#purchase_upload_btn').on('click', function(){
			open_media_uploader_image($('#content_ad_img_purchase'));
		});

		$('#purchase_ad_btn').on('click', function(){
			add_ca_purchase();
		});

		$('#purchase_c_ad_list').on('click', 'span', function(){
			advertsToDelete.push($(this).parent().data('advertid'));
			$(this).parent().remove();
		});

		function add_content_advert(){

			var img_link = $('#content_ad_img_input').val();
			var ad_link = $('#content_ad_link_input').val();
			var title = $('#content_ad_title_input').val();

			var item = '<li data-advertid="null" data-advertlink="' + ad_link + '" data-adtitle="' + title + '">';
			item += '<p>' + title + '</p>';
			item += '<span></span> <img src="' + img_link + '"></li>';

			$('#content_ad_list').append(item);

		}

		function add_ca_research(){

			var img_link = $('#content_ad_img_research').val();
			var ad_link = $('#content_ad_link_research').val();
			var title = $('#content_ad_title_research').val();

			var item = '<li data-advertid="null" data-advertlink="' + ad_link + '" data-adtitle="' + title + '">';
			item += '<p>' + title + '</p>';
			item += '<span></span> <img src="' + img_link + '"></li>';

			$('#research_c_ad_list').append(item);

		}

		function add_ca_comparison(){

			var img_link = $('#content_ad_img_comparison').val();
			var ad_link = $('#content_ad_link_comparison').val();
			var title = $('#content_ad_title_comparison').val();

			var item = '<li data-advertid="null" data-advertlink="' + ad_link + '" data-adtitle="' + title + '">';
			item += '<p>' + title + '</p>';
			item += '<span></span> <img src="' + img_link + '"></li>';

			$('#comparison_c_ad_list').append(item);

		}

		function add_ca_purchase(){

			var img_link = $('#content_ad_img_purchase').val();
			var ad_link = $('#content_ad_link_purchase').val();
			var title = $('#content_ad_title_research').val();

			var item = '<li data-advertid="null" data-advertlink="' + ad_link + '" data-adtitle="' + title + '">';
			item += '<p>' + title + '</p>';
			item += '<span></span> <img src="' + img_link + '"></li>';

			$('#purchase_c_ad_list').append(item);

		}

	});