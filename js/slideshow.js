
jQuery(document).ready(function($){

	class SlideShowFront {

		displaySlide(){
			this.slides.hide();
			$(this.slides[this.currentSlideIndex]).show();
		}

		prevSlide(){
			if(this.currentSlideIndex > 0){
				this.currentSlideIndex = this.currentSlideIndex - 1;
			}else{
				this.currentSlideIndex = this.slides.length - 1;
			}
			
			this.displaySlide();
		}

		nextSlide(){
			if(this.currentSlideIndex < this.slides.length - 1){
				this.currentSlideIndex = this.currentSlideIndex + 1;
			}else{
				this.currentSlideIndex = 0;
			}

			this.displaySlide();
		}

		controls(){

			var thisSs = this;

			this.prev.on('click', function(){
				thisSs.prevSlide();
			});
			this.next.on('click', function(){
				thisSs.nextSlide();
			});
		}

		constructor(containerElem, slides, prev, next){
			this.containerElem = containerElem;
			this.slides = slides;
			this.prev = prev;
			this.next = next;
			this.currentSlideIndex = 0;

			this.controls();		
			this.displaySlide();
		}

	}

	function setUpSlideShows(){

		var slideShowElems = $('.px_slideShow');
		
		slideShowElems.each(function(){

			var slides = $(this).children('.pxSlide');
			var prev = $(this).children('.prev');
			var next = $(this).children('.next');
			// console.log('Prev ', prev);
			var ss = new SlideShowFront($(this), slides, prev, next);

		});

	}

	class Popup {

		popUp(message){

			const thisClass = this;

			let modal = $('<div>', {"class": "c_modal"});
			let box = $('<div>', {"class": "box"});
			box.html(message);

			// box.append(msg);
			modal.append(box);

			$('body').append(modal);
			
			$('.c_modal').on('click', function(e){

				if($(e.target).is('.box') || $(e.target).is('button') || $(e.target).is('input')){
		            e.preventDefault();
		            return;
		        }

				thisClass.popDown();

			});

		}

		popDown(){

			$('.c_modal').remove();
			$('.c_modal').off();

		}

		// constructor(positiveFunc, negativeFunc){
		// 	this.positiveFunc = positiveFunc;
		// 	this.negativeFunc = negativeFunc;
		// }

	}

	setUpSlideShows();

	/************************************************ 

		Downloads

	*************************************************/

	$('.pxdownloadbtn').on('click', function(e){

		e.preventDefault();

		var emailReq = $(this).data('emailreq');
		var form = $(this).next();
		var thankyou = form.next('.px_thanks');

		if(emailReq == 'checked'){

			$(this).slideUp(200);
			form.slideDown(200);
		
		}else{

			form.submit();
			thankyou.show();

		}

	});

	$('.px_pp').on('click', function(){

		var ppLink = $(this);
		var spin = $(this).prev('.px_spin');
		var popUp = new Popup();

		// start spinning
		spin.show();
		ppLink.hide();

		$.ajax({
	        type: "post",
	        dataType: "json",
	        url: frontAjax.ajaxurl,
	        data: {
	        	action: "get_privacy_policy"
	        },
	        success: function(response) {

	        	if(response.success == 1){
	        		spin.hide();
					ppLink.show();
					popUp.popUp(response.privacypolicy);
	        	}else{
	        		spin.hide();
					ppLink.show();
					popUp.popUp(response.privacypolicy);
	        	}

         	}
      	});

	});

	$('.pxenteremail').on('click', function(e){

		e.preventDefault();

		// var download_id = $(this).data('downloadid');
		var emailInput = $(this).prev(); 
		var email = emailInput.val();

		var sendBtn = $(this);
		var spin = $(this).next();

		var form = $(this).parent();
		var thankyou = form.next('.px_thanks');

		$('.px_error').remove();
		emailInput.removeClass('px_invalid');

		if(email.indexOf('@') == -1 || email.indexOf('.') == -1){
			emailInput.addClass('px_invalid');
			emailInput.parent().append('<p class="px_error">Invalid Email</p>');
			return;
		}

		spin.show();
		sendBtn.hide();

		form.submit();
		form.hide();
		thankyou.show();

	});

	/************************************************ 

		Gallery

	*************************************************/

	function LightBox(){
		var thisLb = this;
		this.imgs = $('.cb_gallery img');
		this.imgIndex = null;
		this.modal = $('<div class="cb_lightbox"></div>');
		this.box = $('<div class="box"></div>');
		this.left = $('<div class="left">');
		this.right = $('<div class="right">');

		this.image = $('<img src="">');

		this.left.on('click', function(){
			thisLb.prev();
		});

		this.right.on('click', function(){
			thisLb.next();
		});

		this.box.append(this.left);
		this.box.append(this.image);
		this.box.append(this.right);
		this.thumbnails = this.thumbnails();
		this.box.append(this.thumbnails);
		this.modal.append(this.box);
		this.modal.on('click', function(e){
			if($(e.target).is($('.left')) || $(e.target).is($('.right')) || $(e.target).is($('img')) ){
				return e.preventDefault();
			}
			$(this).hide();
		});
		this.modal.hide();
		$('body').append(this.modal);
	}

	LightBox.prototype.thumbnails = function(){

		var thisLb = this;

		var ul = $('<ul class="list cb_gallerythumbnails"></ul>');

		for(i=0; i<thisLb.imgs.length; i++){
			ul.append('<li><img src="' + $(thisLb.imgs[i]).attr('src') + '"></li>');
		}

		ul.children('li').on('click', function(){
			thisLb.openImg($(this).index());
		});

		return ul;

	};

	LightBox.prototype.prev = function(){

		if(this.imgIndex > 0){
			this.openImg(this.imgIndex - 1);
		}

	};

	LightBox.prototype.next = function(){

		if(this.imgIndex <= this.imgs.length - 1){
			this.openImg(this.imgIndex + 1);
		}

	};

	LightBox.prototype.openImg = function(index){

		this.imgIndex = index;

		if(this.imgIndex <= 0){
			this.left.hide();
		}else{
			this.left.show();
		}

		if(this.imgIndex >= this.imgs.length - 1){
			this.right.hide();
		}else{
			this.right.show();
		}

		var lis = $(this.thumbnails).children();
		lis.removeClass('cb_current_img');

		var currentImg = lis.get(this.imgIndex);

		$(currentImg).addClass('cb_current_img');

		this.image.attr('src', $(this.imgs[this.imgIndex]).attr('src'));
		this.modal.fadeIn(300);

	};

	var lightBox = new LightBox();

	$('.cb_gallery img').on('click', function(e){
		lightBox.openImg($(this).parent().index());
	});

});