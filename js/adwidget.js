jQuery(document).ready(function($){

	$('.adWidgetSelect').on('change', function(){

		var spin = $(this).prev();
		spin.show();

		var outcomeBox = $(this).parent().next();

		var campaign = $(this).val();

		$.ajax({
			url: adwidgetAjax.ajaxurl,
			method: 'POST',
			dataType: 'json',
			data: {
				action: 'get_outcomes',
				campaign: campaign
			},
			success: function(data){
				spin.hide();
				fillOutcomesDropDown(data.outcomes, outcomeBox);
			},
			error: function(a, b, c){
				spin.hide();
				console.log(a, b, c);
			}
		});

	});

	$('.adWidgetOutcomes').on('change', function(){

		var outcomeBox = $(this).parent();
		var outcome = $(this).val();
		var spin = $(this).prev();

		$.ajax({
			url: adwidgetAjax.ajaxurl,
			method: 'POST',
			dataType: 'json',
			data: {
				action: 'get_ads',
				outcome: outcome
			},
			success: function(data){
				spin.hide();
				console.log(data);
				fillAds(data.ads, outcomeBox);
			},
			error: function(a, b, c){
				spin.hide();
				console.log(a, b, c);
			}
		});

	});

	$('.widget-content').on('click', '.px_widget_ads img', function(){
		var link = $(this).data('link');
		var img = $(this).attr('src');
		$(this).parent().children().removeClass('selected');
		$(this).addClass('selected');
		var topLevel = $(this).parent().parent().children();
		$(topLevel[0]).val(link);
		$(topLevel[1]).val(img);
	});

	function fillOutcomesDropDown(outcomes, outcomeBox){

		var selectBox = outcomeBox.children('select');
		selectBox.empty();
		$(selectBox).append('<option value="">- select -</option>');

		for(i=0; i<outcomes.length; i++){
			var option = '';
			option += '<option value="' + outcomes[i].id + '">' + outcomes[i].title + '</option>';
			$(selectBox).append(option);
		}

		outcomeBox.show();

	}

	function fillAds(ads, outcomeBox){

		outcomeBox.next('.px_widget_ads').remove();
		let adBox = '<div class="px_widget_ads">';

		for(i=0; i<ads.length; i++){
			adBox += '<img data-link="' + ads[i].side_link +'" src="' + ads[i].side_ad + '">';
		}

		adBox += '</div>';

		$(adBox).insertAfter( outcomeBox );

	}

});