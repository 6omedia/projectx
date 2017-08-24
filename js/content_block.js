+(function(){

	$ = jQuery;

	function ContentBlock(blockType, index, blockBody = []){
		this.index = index;
		this.blockType = blockType;

		this.li = $('<li></li>');
		this.block = $('<div>', {"class": "content_block " + this.blockType});
		this.block.append(this.buildHeader());
		this.block.append(this.buildBody(blockBody));
		this.li.append(this.block);
	}

	ContentBlock.prototype.buildHeader = function(){

		var blockHeader = $('<div>', {"class": "cb_header"});
		var bh_p = $('<p>', {"class": "cb_head_p"}).text(this.blockType);	
		var bg_color = $('<input type="color" value="#ffffff" name="px_blocks[' + this.index + '][blockcolor]">');
		var span_del = $('<span>', {"class": "minus-span"});

		blockHeader.append(bh_p);
		blockHeader.append(span_del);
		blockHeader.append(bg_color);

		return blockHeader;

	}

	ContentBlock.prototype.buildBody = function(blockBody){

		this.block.append('<input type="hidden" name="px_blocks[' + this.index + '][blocktype]" value="' + this.blockType + '">');

		for(var i=0; i<blockBody.length; i++){
			this.block.append(blockBody[i]);
		}

	}

	ContentBlock.prototype.getBlock = function(){
		return this.li;
	}

	/******************************
		Gallery
	*******************************/

	function Gallery(insertImgBtn, galleryList, blockIndex){
		
		this.insertImgBtn = insertImgBtn;
		this.galleryList = galleryList;
		this.blockIndex = blockIndex;

		var thisGallery = this;

		this.insertImgBtn.on('click', function(){
			thisGallery.addImgToGalley();
		});

	}

	Gallery.prototype.addImgToGalley = function(src, alt){

		var thisGallery = this;

		insertMedia('', '', '', function(imgSrc, imgAlt){

			var li = $('<li></li>');
			var imgInput = $('<input type="hidden" value="' + imgSrc + '###' + imgAlt + '" name="px_blocks[' + thisGallery.blockIndex + '][content][]">');
			var removeOverlay = $('<div class="removeImgOverlay">Remove</div>');
			var img = $('<img src="' + imgSrc + '">');

			li.append(imgInput);
			li.append(removeOverlay);
			li.append(img);

			thisGallery.galleryList.append(li);

		});

	}

	/******************************
		Slide Show
	*******************************/

	function SlideShow(left, right, quill_options, blockIndex){

		var thisSs = this;

		this.left = left;
		this.right = right;
		this.quill_options = quill_options; 
		this.blockIndex = blockIndex;
		this.quill = '';

		this.slideList = left.children('.slideList');
		this.newSlideBtn = left.children('.btnNewSlide');

		this.buildEditingSide();

		this.slideList.sortable({
			connectWith: ".connectedSortable",
			cancel: "input,textarea,button,select,option,p,span"
		});

		this.slideList.on('click', 'li', function(){
			thisSs.loadSlide($(this).children('input').val(), $(this).data('index'));
			thisSs.updateSlideBtn.data('index', $(this).data('index'));
		});

		this.newSlideBtn.on('click', function(){
			thisSs.buildEditingSide();
		});

		this.slideList.on('click', 'li span', function(){
			$(this).parent().remove();
		});

	}

	SlideShow.prototype.buildEditingSide = function(){

		var thisSs = this;

		var title = '<input type="text" class="slideTitle" placeholder="Slide Title...">';
		var imgSrc = '<input type="hidden" class="img_src">';
		var imgAlt = '<input type="hidden" class="img_alt">';
		var imgUploadBtn = '<div class="btn upload_img_btn">Upload Image</div>';
		var imgElement = '<img class="imgBlockImg" src="../wp-content/plugins/projectx/img/landscape.png">';

		var quillDiv = document.createElement('div');
		var textarea = $('<textarea>', {
							'style': 'display: none'
						});

		this.addSlideBtn = $('<div class="btn add_slide">Add This Slide</div>');
		this.updateSlideBtn = $('<div class="btn update_slide">Update This Slide</div>');

		this.addSlideBtn.on('click', function(){
			thisSs.addSlide();
		});

		this.updateSlideBtn.on('click', function(){

			var slideIndex = $(this).data('index');
			thisSs.addSlide(true, slideIndex);

		});

		this.right.empty();

		this.right.append(title);
		this.right.append(imgSrc);
		this.right.append(imgAlt);
		this.right.append(imgUploadBtn);
		this.right.append(imgElement);
		this.right.append(quillDiv);
		this.right.append(textarea);
		this.right.append(this.addSlideBtn);
		this.right.append(this.updateSlideBtn);

		this.addSlideBtn.show();
		this.updateSlideBtn.hide();

		var quill = new Quill(quillDiv, this.quill_options);

		quill.on('text-change', function() {
	  		var html = quill.root.innerHTML;
			textarea.val(html);
		});

		this.quill = quill;

	}

	SlideShow.prototype.actionMsg = function(btn, msg){
		$(btn).insertAfter('<span class="px_ss_msg>' + msg + '</span>');
		// delay, fadeout, remove
	};

	SlideShow.prototype.addSlide = function(update, index){

		var slideTitle = $(this.right).children('.slideTitle').val();

		var slide = {
			title: slideTitle,
			content: $(this.right).children('textarea').val(),
			imgSrc: $(this.right).children('.img_src').val(),
			imgAlt: $(this.right).children('.img_alt').val()
		};

		var theJson = JSON.stringify(slide).replace(/'/g, '&rsquo;');

		if(update){

			var li = this.slideList.find("[data-index='" + index + "']");
			li.children('input').val(theJson);
			li.children('p').html(slideTitle);

		}else{

			var li = $('<li data-index="' + this.slideList.children().length + '"></li>');
		
			var input = $("<input name='px_blocks[" + this.blockIndex + "][content][]' type='hidden' value='" + theJson + "'>");
			
			var grip = $('<div class="px_grip"></div>');
			var p = $('<p>' + slideTitle + '</p>');
			var span = $('<span>x</span>');

			li.append(input);
			li.append(grip);
			li.append(p);
			li.append(span);

			this.slideList.append(li);

		}

	}

	SlideShow.prototype.loadSlide = function(data, index){

		var slideObj = JSON.parse(data);

		this.right.children('.slideTitle').val(slideObj.title);
		this.right.children('.img_src').val(slideObj.imgSrc);
		this.right.children('.img_alt').val(slideObj.imgAlt);
		this.right.children('img').attr('src', slideObj.imgSrc);
		// this.right.children('textarea').val(slideObj.content);
		
		this.quill.root.innerHTML = slideObj.content;

		// hide add slide btn
		this.addSlideBtn.hide();

		this.updateSlideBtn.data('slideIndex', index);
		this.updateSlideBtn.show();

	}

	window.contentBlock = ContentBlock; 
	window.gallery = Gallery;
	window.slideShow = SlideShow;

})();