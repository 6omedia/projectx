+(function(){

	/*
		
		open_media_uploader_image(imgSrcInput, imgAltInput, imgElem)

	*/

	$ = jQuery;

	function insertMedia(imgSrcInput, imgAltInput, imgElem, callback) {

		// console.log(imgSrcInput, imgAltInput, imgElem);

	    media_uploader = wp.media({
	        frame:    "post", 
	        state:    "insert", 
	        multiple: false
	    });

	    media_uploader.on("insert", function(){

	        var json = media_uploader.state().get("selection").first().toJSON();

	        var image_alt = json.alt;
	        var image_url = json.url;
	        var image_caption = json.caption;
	        var image_title = json.title;

	        if(!callback){

	        	imgSrcInput.val(image_url);
		        imgAltInput.val(image_alt);
			    imgElem.attr('src', image_url).attr('alt', image_alt);

	        }else{

	        	callback(image_url, image_alt);

	        }

	    });

	    media_uploader.open();

	}	

	function loadQuills(quill_options){

		$('.load_Quill').each(function(){

			var quill = new Quill(this, quill_options);
			var textarea = $(this).next();

			quill.root.innerHTML = textarea.val();

			quill.on('text-change', function() {
		  		var html = quill.root.innerHTML;
				textarea.val(html);
			});

		});

	}

	/* Rating Block */

	function changeRatingDisplay(li, rating){

		if(rating == ''){
			rating = $(li).index() + 1;
		}

		console.log('rating: ', rating);

		var starList = $(li).parent();
		var starChildren = $(starList).children();

		starChildren.css('filter', 'grayscale(100%)');

		for(var i=0; i<rating; i++){
			$(starChildren[i]).css('filter', 'grayscale(0%)');
		}

	}

	/* Adverts */

	function loadAdvertOptions(selectBox, outcomeId, funnelPosition, spin){

		selectBox.append('<option value="">- select advert -</option>');

		$.ajax({
	        type: "post",
	        dataType: "json",
	        url: cblocksAjax.ajaxurl,
	        data: {
	        	action: "get_content_adverts", 
	        	outcome_id: outcomeId,
	        	funnel_position: funnelPosition
	        },
	        success: function(response) {

	        	if(response.success == 1){
	        		
	        		var options = '';

	        		for(i=0; i<response.ads.length; i++){
	        			options += '<option value="' + response.ads[i].id + '">' + response.ads[i].title + '</option>';
	        		}

	        		selectBox.append(options);
	        		selectBox.show();
	        		spin.hide();

	        	}

         	}
      	});

	}

	function loadDownloadOptions(selectBox, outcomeId, funnelPosition, spin){

		selectBox.append('<option value="">- select advert -</option>');

		$.ajax({
	        type: "post",
	        dataType: "json",
	        url: cblocksAjax.ajaxurl,
	        data: {
	        	action: "get_filtered_downloads", 
	        	outcome_id: outcomeId,
	        	funnel_position: funnelPosition
	        },
	        success: function(response) {

	        	// console.log('res ', response);

	        	if(response.success == 1){

	        		var options = '';

	        		for(i=0; i<response.downloads.length; i++){
	        			options += '<option value="' + response.downloads[i].id + '">' + response.downloads[i].filename + '</option>';
	        		}

	        		selectBox.append(options);

	        		selectBox.show();
	        		spin.hide();

	        	}

         	},
         	error: function(a, b, c){
         		console.log(a, b, c);
         	}
      	});

	}

	// insertMedia = insertMedia;

	window.insertMedia = insertMedia;
	window.loadQuills = loadQuills;
	window.changeRatingDisplay = changeRatingDisplay;
	window.loadAdvertOptions = loadAdvertOptions;
	window.loadDownloadOptions = loadDownloadOptions;

})();