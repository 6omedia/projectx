jQuery(document).ready(function($){

	const addBtn = $('#add_btn');
	const iplist = $('#ex_ip_list');
	const input_ip = $('#input_ip');
	const input_desc = $('#input_desc');

	function add_ip(){

		const ip = input_ip.val();
		const description = input_desc.val();

		if(ip != ''){

			spin();
			
			$.ajax({
		        type: "post",
		        dataType: "json",
		        url: ipAjax.ajaxurl,
		        data: {
		        	action: "px_add_excluded_ip", 
		        	ip: ip,
		        	description: description
		        },
		        success: function(response){

		        	stopSpin();

		        	console.log('response: ', response);
		        	if(response.success == 1){
		        		if(response.user_exists == 0){
		        			$('#notices .errorNotice').hide();
							let string = '<li data-ipid="' + response.ipid + '">';
							string += '<span class="minus-span"></span>';
							string += ip + ' - ';
							string += description;
							string += '</li>';
							iplist.prepend(string);
		        		}else{
		        			location.reload();
		        		}
		        	}

	         	}
	      	});
		
		}else{
			displayNotice('IP cant be empty...', true);
		}

	}

	function displayNotice($notice, $error){
			
		$('#notices .errorNotice').hide();

		if($error){
			$('#notices .errorNotice').html($notice).slideDown(800).delay(3000); //.slideUp(600);
		}else{
			$('#notices div').html($notice).slideDown(800).delay(3000).slideUp(600);
		}
		
	}

	function spin(){
		$('.spin').show();
	}

	function stopSpin(){
		$('.spin').hide();
	}

	function remove_ip(ipid, index){

		console.log(index);

		spin();

		$.ajax({
	        type: "post",
	        dataType: "json",
	        url: ipAjax.ajaxurl,
	        data: {
	        	action: "px_remove_excluded_ip", 
	        	ipid: ipid
	        },
	        success: function(response){

	        	stopSpin();
	        	console.log('response: ', response);
	        	
	        	if(response.success == 1){

	        		$('#ex_ip_list li').get(index).remove();
	        		stopSpin();

	        	}
	        	
         	}
      	});

	}

	addBtn.on('click', function(){
		add_ip();
	});

	iplist.on('click', 'span', function(){

		// outcomesToDelete.push($(this).parent().data('outcomeid'));
		// $(this).parent().remove();

		var thisli = $(this).parent();
		remove_ip($(this).parent().data('ipid'), thisli.index());
	
	});

});