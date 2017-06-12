
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
			console.log('Prev ', prev);
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

	// Downloads

	$('.pxdownloadbtn').on('click', function(){

		var form = $(this).next();

		$(this).slideUp(200);
		form.slideDown(200);

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

		// window.location.href = 'downloads';

		// $.ajax({
	 //        type: "post",
	 //        dataType: "json",
	 //        url: frontAjax.ajaxurl,
	 //        data: {
	 //        	action: "download_file",
	 //        	download_id: download_id,
	 //        	email: email
	 //        },
	 //        success: function(data) {

	 //        	console.log(data);

	 //        	if(data.success == 1){
	 //        		// hide spin
		// 			// start btn
	 //        	}

  //        	},
  //        	error: function(a,b,c){
  //        		console.log(a,b,c);
  //        	}
  //     	});

	});

});