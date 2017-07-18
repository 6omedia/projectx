jQuery(document).ready(function($){

	function loadGalleries(){

		$('.gallery_list').each(function(){

			var blockIndex = $(this).data('blockindex');
			var gallery = new Gallery($(this).prev(), $(this), blockIndex);

		});

	}

	function loadSlideShows(quill_options){

		$('.slideList').each(function(){

			var blockIndex = $(this).data('blockindex');
			var right = $(this).parent().siblings('.right');

			var slideShow = new SlideShow($(this).parent(), right, quill_options, blockIndex);

		});

	}

	// Classes
	var ContentBlock = window.contentBlock;
	var Gallery = window.gallery;
	var SlideShow = window.slideShow;
	
	// Functions
	var insertMedia = window.insertMedia;
	var loadQuills = window.loadQuills;
	var changeRatingDisplay = window.changeRatingDisplay;
	var loadAdvertOptions = window.loadAdvertOptions;
	var loadDownloadOptions = window.loadDownloadOptions;

	// start
	var blockList = $('#content_blocks');
	var blockBtns = $('.contentblock_menu li div');
	var blockIndex = $('#content_blocks li').length;

	var quill_options = {
		modules: {
			toolbar: [
				[{ 'size': ['small', false, 'large', 'huge'] }],
				['bold', 'italic', 'underline', 'link'],
				[{ 'color': [] }, { 'background': [] }], 
				[{ 'align': [] }],
				[{ 'list': 'ordered'}, { 'list': 'bullet' }]
		    ]
		},
		placeholder: 'Type content here...',
	    theme: 'snow'
	};

	loadQuills(quill_options);
	loadGalleries();
	loadSlideShows(quill_options);

	///*** Events ***///

	$('#content_blocks').on('click', '.upload_img_btn', function(){

		var imgInput = $(this).siblings('.img_src');
		var imgAlt = $(this).siblings('.img_alt');
		var imgElement = $(this).next();

		insertMedia($(imgInput), $(imgAlt), imgElement);
	
	});

	$('#content_blocks').on('click', '.removeImgOverlay', function(){
		$(this).parent().remove();
	});

	$('#content_blocks').on('click', '.upload_video_btn', function(){

		var videoInput = $(this).next();

		insertMedia('', '', '', function(videoUrl){
			videoInput.val(videoUrl);
		});

	});

	$('#content_blocks').on('mouseover', '.starList li', function(){
		changeRatingDisplay($(this), '');
	});

	$('#content_blocks').on('mouseout', '.starList li', function(){
		changeRatingDisplay($(this), $(this).parent().prev().val());
	});

	$('#content_blocks').on('click', '.starList li', function(){

		var rating = $(this).index() + 1;
		var numInput = $(this).parent().prev();

		numInput.val(rating);

	});

	// Block Buttons

	$('#title_cb').on('click', function(){

		var contentArray = [
			'<input type="text" name="px_blocks[' + blockIndex + '][content]" class="cb_content title">'
		];

		var contentBlock = new ContentBlock('title', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());
		blockIndex++;

	});

	$('#h2_cb').on('click', function(){

		var contentArray = [
			'<input type="text" name="px_blocks[' + blockIndex + '][content]" class="cb_content h2">'
		];

		var contentBlock = new ContentBlock('h2', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());
		blockIndex++;
	
	});

	$('#h3_cb').on('click', function(){

		var contentArray = [
			'<input type="text" name="px_blocks[' + blockIndex + '][content]" class="cb_content h3">'
		];

		var contentBlock = new ContentBlock('h3', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());
		blockIndex++;
	
	});

	$('#paragraph_cb').on('click', function(){

		var quillDiv = document.createElement('div');
		var txtarea = $('<textarea>', {
							'name': 'px_blocks[' + blockIndex + '][content]',
							'class': 'cb_content paragraph',
							'style': 'display: none'
						});

		var contentArray = [
			quillDiv,
			txtarea
		];

		var contentBlock = new ContentBlock('paragraph', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());

		var quill = new Quill(quillDiv, quill_options);

		quill.on('text-change', function() {
	  		var html = quill.root.innerHTML;
			txtarea.val(html);
		});

		blockIndex++;
	
	});

	$('#quote_cb').on('click', function(){

		var contentArray = [
			'<textarea name="px_blocks[' + blockIndex + '][content]" class="cb_content quote"></textarea>'
		];

		var contentBlock = new ContentBlock('quote', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());
		blockIndex++;

	});

	$('#hr_cb').on('click', function(){

		var contentArray = [
			'<input name="px_blocks[' + blockIndex + '][content]" type="hidden" value="hr" class="cb_content hr">',
			'<hr>'
		];

		var contentBlock = new ContentBlock('hr', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());
		blockIndex++;

	});

	$('#link_cb').on('click', function(){

		var contentArray = [
			'<input placeholder="Link URL..." type="text" name="px_blocks[' + blockIndex + '][content][link_url]" class="cb_content link">',
			'<input placeholder="Link Text..." type="text" name="px_blocks[' + blockIndex + '][content][link_text]" class="cb_content link">'
		];

		var contentBlock = new ContentBlock('link', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());
		blockIndex++;

	});

	$('#preface_cb').on('click', function(){

		var titleInput = '<input placeholder="Title..." name="px_blocks[' + blockIndex + '][content][title]" type="text" class="cb_content preface">';
		var quillDiv = document.createElement('div');
		var textarea = $('<textarea>', {
							'name': 'px_blocks[' + blockIndex + '][content][preface_content]',
							'class': 'cb_content paragraph',
							'style': 'display: none'
						});

		var insertImgBtn = $('<div class="btn upload_img_btn">Insert Preface Image</div>');
		var imgInput = $('<input class="media_upload_input img_src" type="hidden" name="px_blocks[' + blockIndex + '][content][img_src]">');
		var imgAlt = $('<input class="media_upload_input img_alt" type="hidden" name="px_blocks[' + blockIndex + '][content][img_alt]">');
		var imgElement = $('<img src="../wp-content/plugins/projectx/img/landscape.png" class="imgBlockImg">');

		var contentArray = [
			titleInput,
			quillDiv,
			textarea,
			imgInput,
			imgAlt,
			insertImgBtn,
			imgElement
		];

		var contentBlock = new ContentBlock('preface', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());

		var quill = new Quill(quillDiv, quill_options);

		quill.on('text-change', function() {
	  		var html = quill.root.innerHTML;
			textarea.val(html);
		});

		blockIndex++;

	});

	$('#img_cb').on('click', function(){

		/*

			Order...
				- img_src
				- img_alt
				- button
				- img element

		*/

		var insertImgBtn = $('<div class="btn upload_img_btn">Insert Image</div>');
		var imgInput = $('<input type="hidden" name="px_blocks[' + blockIndex + '][content][img_src]" class="cb_content img_src">');
		var imgAlt = $('<input type="hidden" name="px_blocks[' + blockIndex + '][content][img_alt]" class="cb_content img_alt">');
		var imgElement = $('<img class="imgBlockImg" src="../wp-content/plugins/projectx/img/landscape.png">');

		var contentArray = [
			imgInput,
			imgAlt,
			insertImgBtn,
			imgElement
		];

		var contentBlock = new ContentBlock('image', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());

		blockIndex++;

	});

	$('#embed_cb').on('click', function(){

		var contentArray = [
			'<textarea class="embed" name="px_blocks[' + blockIndex + '][content]"></textarea>'
		];

		var contentBlock = new ContentBlock('embed', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());

		blockIndex++;

	});

	$('#shortcode_cb').on('click', function(){

		var contentArray = [
			'<textarea class="embed shortcode" name="px_blocks[' + blockIndex + '][content]"></textarea>'
		];

		var contentBlock = new ContentBlock('shortcode', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());

		blockIndex++;

	});

	$('#gallery_cb').on('click', function(){

		var imgUploadBtn = $('<div class="btn upload_gallery_img">Add Image to Gallery</div>');
		var galleryList = $('<ul class="gallery_list"></ul>');

		var contentArray = [
			imgUploadBtn,
			galleryList
		];

		var contentBlock = new ContentBlock('gallery', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());

		var gallery = new Gallery(imgUploadBtn, galleryList, blockIndex);

		blockIndex++;

	});

	$('#video_cb').on('click', function(){

		var contentArray = [
			'<div class="btn upload_video_btn">Upload Video</div>',
			'<input name="px_blocks[' + blockIndex + '][content]" value="" class="media_upload_input" type="text">'
		];

		var contentBlock = new ContentBlock('video', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());

		blockIndex++;

	});

	$('#advert_cb').on('click', function(){

		var select = $('<select name="px_blocks[' + blockIndex + '][content]" class="cb_content advert"></select>');
		var spin = $('<div class="spin-relative"></spin>');
		select.hide();
		spin.show();

		var contentArray = [select, spin];

		var outcomeId = $('.campaign_box').data('outcomeid'); 
		var funnelPosition = $('.campaign_box').data('funnelposition');

		loadAdvertOptions(select, outcomeId, funnelPosition, spin);

		var contentBlock = new ContentBlock('advert', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());

		blockIndex++;

	});

	$('#slideshow_cb').on('click', function(){

		var left = $('<div class="left"><h2>Slides</h2><div class="btnNewSlide">ADD NEW SLIDE</div><ul class="slideList ui-sortable"></ul></div>');
		var right = $('<div class="right"></div>');

		var contentArray = [
			left,
			right
		];

		var contentBlock = new ContentBlock('slideshow', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());

		var slideShow = new SlideShow(left, right, quill_options, blockIndex); 

		blockIndex++;

	});

	$('#rating_cb').on('click', function(){

		var contentArray = [
			'<input name="px_blocks[' + blockIndex + '][content]" style="display: none" value="0" type="number" min="0" max="5">',
			'<ul class="starList"><li style="filter: grayscale(100%)"></li><li style="filter: grayscale(100%)"></li><li style="filter: grayscale(100%)"></li><li style="filter: grayscale(100%)"></li><li style="filter: grayscale(100%)"></li></ul>'
		];

		var contentBlock = new ContentBlock('rating', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());

		blockIndex++;

	});

	$('#download_cb').on('click', function(){

		var select = $('<select name="px_blocks[' + blockIndex + '][content][download]" class="cb_content download"></select>');
		var spin = $('<div class="spin-relative"></spin>');
		select.hide();
		spin.show();

		var contentArray = [
			'<label>Heading for the download box</label>',
			'<input type="text" placeholder="Download our FREE guide to..." name="px_blocks[' + blockIndex + '][content][heading]">',
			select,
			spin, 
			'<label>Email required for download?</label>',
			'<input type="checkbox" class="checkBoxInput" name="px_blocks[' + blockIndex + '][content][email_required]">'
		];

		var outcomeId = $('.campaign_box').data('outcomeid'); 
		var funnelPosition = $('.campaign_box').data('funnelposition');

		loadDownloadOptions(select, outcomeId, funnelPosition, spin);

		var contentBlock = new ContentBlock('download', blockIndex, contentArray);
		blockList.append(contentBlock.getBlock());

		blockIndex++;

	});

	// sortable

	blockList.sortable({
		connectWith: ".connectedSortable",
		cancel: "input,textarea,button,select,option,.ql-editor"
	});

	$('#content_blocks').on('click', '.minus-span', function(){
		$(this).parent().parent().remove();
	});

	$('.embed').keydown(function (e){
	    var keycode1 = (e.keyCode ? e.keyCode : e.which);
	    if (keycode1 == 0 || keycode1 == 9) {
	        e.preventDefault();
	        e.stopPropagation();
	    }
	});


	/**** Post ****/

	$('#px_campaign_select').on('change', function(){

		const outcomeBox = $('#outcome_box');
		outcomeBox.hide();
		const spin = $('#outcome_spin');
		spin.show();

		$('#px_outcome_select').empty();

		var campaign_id = $(this).find(":selected").data('campaignid');

		if(campaign_id != ''){

			$.ajax({
		        type: "post",
		        dataType: "json",
		        url: cblocksAjax.ajaxurl,
		        data: {
		        	action: "list_outcomes", 
		        	campaign_id: campaign_id
		        },
		        // processData: false,
		        success: function(response) {

		        	// console.log('response: ', response);
		        	if(response.success == 1){
		        		optionsString = '';

		        		for(var i=0; i<response.outcomes.length; i++){
		        			optionsString += '<option data-outcomeid="' + response.outcomes[i].id + '" value="' + response.outcomes[i].id + '###' + response.outcomes[i].title + '">' + response.outcomes[i].title  + '</option>';
		        		}

		        		$('#px_outcome_select').append(optionsString);

		        		outcomeBox.show();
		        		spin.hide();
		        	}
	         	}
	      	});

		}

	});

	$('#content_blocks').on('change', 'input[type="color"]', function(){

		var bg = 'linear-gradient(#fff 97px,' + $(this).val() + ' 145px)';
		$(this).parent().parent().css('background', bg);

	});

});