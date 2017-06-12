jQuery(document).ready(function($){

	// classes

	class Popup {

		startLoading(){
			this.yesBtn.hide();
			this.noBtn.hide();
			this.spin.show();
		}

		stopLoading(){
			this.yesBtn.show();
			this.noBtn.show();
			this.spin.hide();
		}

		popUp(message, customform){

			const thisClass = this;

			let modal = $('<div>', {"class": "c_modal"});
			let box = $('<div>', {"class": "box"});
			let msg = $('<p>').html(message);

			box.append(msg);

			if(customform !== undefined){

				box.append(customform);

			}else{
			
				box.append(this.yesBtn);
				box.append(this.noBtn);
				box.append(this.spin);

			}

			modal.append(box);

			$('body').append(modal);

			
			$('.c_modal').on('click', function(e){

				if($(e.target).is('.box') || $(e.target).is('button') || $(e.target).is('input')){
		            e.preventDefault();
		            return;
		        }

				thisClass.popDown();
			});

			if(customform !== undefined){
				this.positiveFunc();
			}

		}

		popDown(){

			$('.c_modal').remove();
			$('.c_modal').off();

		}

		constructor(positiveFunc, negativeFunc){
			var thisClass = this;
			this.positiveFunc = positiveFunc;
			this.negativeFunc = negativeFunc;
			this.yesBtn = $('<button>', {"class": "yesBtn"}).html('Yes').on('click', function(){
							thisClass.positiveFunc();
						});
			this.noBtn = $('<button>', {"class": "noBtn"}).html('No').on('click', function(){
							thisClass.negativeFunc();
						});
			this.spin = $('<div>', {"class": "loader"}).hide();
		}

	}

	// Functions

	function displayNotice($notice, $error){
		
		$('#notices .errorNotice').hide();

		if($error){
			$('#notices .errorNotice').html($notice).slideDown(800).delay(3000); //.slideUp(600);
		}else{
			$('#notices div').html($notice).slideDown(800).delay(3000).slideUp(600);
		}
		
	}

	function updatePrivacyPolicy(privacypolicy){

		$.ajax({
	        type: "post",
	        dataType: "json",
	        url: downloadsAjax.ajaxurl,
	        data: {
	        	action: "update_privacypolicy", 
	        	privacypolicy: privacypolicy
	        },
	        // processData: false,
	        success: function(response) {

	        	console.log('response: ', response);
	        	if(response.success == '1'){
	        		displayNotice("Privacy Policy Updated", false);
	        	}

         	}
      	});

	}

	function removeFile(filename, downloadId, callback){

		$.ajax({
	        type: "post",
	        dataType: "json",
	        url: downloadsAjax.ajaxurl,
	        data: {
	        	action: "remove_file", 
	        	filename: filename,
	        	downloadId: downloadId
	        },
	        // processData: false,
	        success: function(response) {

	        	callback(response);

	        	if(response.success == '1'){
	        		displayNotice("File Removed", false);
	        	}

         	},
         	error: function(){
         		callback('failed');
         	}
      	});

	}

	function getOutComeOptions(campaign){

		var spin = $('#uploadForm table .spin-relative');

		// hide select box
		outcomeSelectBox.hide();
		// start spinner
		spin.show();

		$.ajax({
			type: "post",
	        dataType: "json",
	        url: downloadsAjax.ajaxurl,
	        data: {
	        	action: "get_campaigns_outcomes", 
	        	campaign: campaign
	        },
			success: function(data)
			{
				console.log(data);
				// on success
				// append outcomes to outcome box
				buildOutcomesBox(data.outcomes);

				// enable outcome box
				outcomeSelectBox.attr('disabled', false);

				spin.hide();
				outcomeSelectBox.show();
			}
		});

	}

	function buildOutcomesBox(outcomes){

		var string = '';

		for(var i=0; i<outcomes.length; i++){
			string += '<option value="' + outcomes[i].id + '###' + outcomes[i].title + '">' + outcomes[i].title + '</option>';
		}

		outcomeSelectBox.empty();
		outcomeSelectBox.append(string);

	}

	// Starts Here 

	var updateBtn = $('#updateBtn');
	var uploadFile = $('#uploadFile');
	var fileInput = $('#fileInput');
	var table = $('.downloadsTable');

	var campaignSelectBox = $('#dl_campaign');
	var outcomeSelectBox = $('#dl_outcome');

	var quill = new Quill('#ppQuill', {
	    theme: 'snow'
  	});

	updateBtn.on('click', function(){
		updatePrivacyPolicy(quill.container.firstChild.innerHTML);
	});

	$('.downloadsTable').on('click', '.minus-span', function(){

		var downloadId = $(this).data('download_id');

		var filename = $(this).parent().siblings()[0];
		filename = $($(filename).children()[0]).text();

		var popUp =  new Popup(function(){

			this.startLoading();
			var thisPop = this;

			removeFile(filename, downloadId, function(data){

				if(data.success == '1'){
					thisPop.stopLoading();
					table.find("tr:gt(0)").remove();
					for(var i=0; i<data.downloads.length; i++){
						table.append(
							`<tr>
								<td><a href="">${data.downloads[i].filename}</a></td>
								<td>${data.downloads[i].campaign}</td>
								<td>${data.downloads[i].outcome}</td>
								<td>${data.downloads[i].funnel_position}</td>
								<td>${data.downloads[i].captured_emails}</td>
								<td>
									<span class="export"></span>
									<span class="minus-span"></span>
								</td>
							</tr>`
						);
					}
				}else{
					thisPop.stopLoading();
					displayNotice("Could Not Remove File", true);
				}
				thisPop.popDown();

			});

		},
		function(){

			this.popDown();

		});

		popUp.popUp('Are you sure you want to remove this file from the server?');

	});

	$('#dl_campaign').on('change', function(){
		var outcome = $(this).val();
		getOutComeOptions(outcome);
	});

	// uploadFile.on('click', function(){
	// 	// var fileData = fileInput.prop('files')[0];  
	// 	// uploadFileToServer(fileData);
	// });

});