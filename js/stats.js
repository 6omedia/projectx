jQuery(document).ready(function($){

	const funnel_filters = $('#fs_filters li');
	const ff_all_btn = funnel_filters[0];
	const ff_awareness_btn = funnel_filters[1];
	const ff_research_btn = funnel_filters[2];
	const ff_comparison_btn = funnel_filters[3];
	const ff_purchase_btn = funnel_filters[4]; 

	const spiner = $('.spin');
	const table = $('table');
	const tBody = $('#main_stats table tbody');
	let sortAscending = true;
	let gsortBy = 0;

	let currentCampaign = {
		title: '',
		id: ''
	};

	let currentOutcome = {
		title: '',
		id: ''
	};

	let currentFilter = 'awareness';

	// functions
	function filterByCampaign(campaignid, selectedCampaign){

		currentOutcome.id = '';
		spin();
		changeSelectedCampaign(selectedCampaign);
		getOutcomes(campaignid);
		getStats(campaignid, '', '', 'campaign');

	}

	function filterByOutcome(outcomeid){

		spin();
		let filter = '';

		if(currentFilter != 'All'){
			filter = currentFilter;
		}

		getStats(currentCampaign.id, outcomeid, filter, 'outcome');

	}

	function filterByFunnel(funnelPosition){

		currentFilter = funnelPosition;
		spin();
		changeSelectedFunnel(funnelPosition);

		if(funnelPosition == 'All'){
			funnelPosition = '';
		}

		getStats(currentCampaign.id, currentOutcome.id, funnelPosition, 'funnel');

	}

	function changeSelectedCampaign(currentElement){

		$('#fs_campaigns li').each(function(){
			$(this).removeClass('current_funnel_filter');
		});

		currentElement.addClass('current_funnel_filter');

	}

	function changeSelectedFunnel(funnelPosition){

		if(funnelPosition == 'all'){

			funnel_filters.each(function(index){
				$(this).removeClass('current_funnel_filter');
			});

			$(ff_all_btn).addClass('current_funnel_filter');
		}else{

			funnel_filters.each(function(index){
				if($(this).text() != funnelPosition){
					$(this).removeClass('current_funnel_filter');
				}else{
					$(this).addClass('current_funnel_filter');
				}
			});
		}

	}

	function getStats(campaignid, outcomeid, funnelPosition, filter_type){

		$.ajax({
	        type: "post",
	        dataType: "json",
	        url: statsAjax.ajaxurl,
	        data: {
	        	action: "px_get_stats",
	        	campaignid: campaignid,
	        	outcomeid: outcomeid,
	        	funnel_position: funnelPosition.toLowerCase(),
	        	filter_type: filter_type
	        },
	        success: function(response) {

	        	console.log('response: ', response);
	        	if(response.success == 1){
	        		loadTableData(response['pages'], response['page_views'], response['page_clicks']);
	        		// stopSpin();
	        	}

         	}
      	});
	
	}

	function getOutcomes(campaignid){

		$.ajax({
	        type: "post",
	        dataType: "json",
	        url: statsAjax.ajaxurl,
	        data: {
	        	action: "px_get_outcomes",
	        	campaignid: campaignid
	        },
	        success: function(response) {

	        	console.log('response: ', response);
	        	if(response.success == 1){
	        		// loadTableData(response['pages']);
	        		loadOutcomesSelect(response.outcomes);
	        		stopSpin();
	        	}

         	}
      	});

	}

	function spin(){
		spiner.show();
		table.css('opacity', '0.2');
	}

	function stopSpin(){
		spiner.hide();
		table.css('opacity', '1');
	}

	function loadTableData(pages, views, clicks){

		$('#campaign_title').html(currentCampaign.title);
		$(fs_filters).show();
		table.show();

		$('table tr:not(:first)').remove();

		let string = '';

		$(pages).each(function(i){
			const conversion = (pages[i].clicks / views[i][0].page_views) * 100;
			string += '<tr>';
			string += '<td>' + pages[i].page_title + '</td>';
			string += '<td>' + views[i][0].page_views + '</td>';
			string += '<td>' + clicks[i][0].page_clicks + '</td>';
			string += '<td>' + Math.round(conversion) + '%</td>';
			string += '</tr>';
		});

		table.append(string);

		stopSpin();

	}


	function loadOutcomesSelect(outcomes){

		$('#select_outcomes').empty();

		let string = '<select id="outcome_box">';
		string += '<option value="">- All Outcomes -</option>';

		$(outcomes).each(function(i){
			string += '<option value="' + outcomes[i].id + '">' + outcomes[i].title + '</option>';
		});

		string += '</select>';

		$('#select_outcomes').append(string);

	}

	$(ff_all_btn).on('click', function(){
		filterByFunnel($(this).text());
	});	

	$(ff_awareness_btn).on('click', function(){
		filterByFunnel($(this).text());
	});

	$(ff_research_btn).on('click', function(){
		filterByFunnel($(this).text());
	});

	$(ff_comparison_btn).on('click', function(){
		filterByFunnel($(this).text());
	});

	$(ff_purchase_btn).on('click', function(){
		filterByFunnel($(this).text());
	});

	$('#fs_campaigns li').on('click', function(){
		currentCampaign.title = $(this).text();
		currentCampaign.id = $(this).data('campaignid'); 
		filterByCampaign($(this).data('campaignid'), $(this));
	});

	$('#select_outcomes').on('change', '#outcome_box', function(){
		currentOutcome.id = $(this).val();
		filterByOutcome(currentOutcome.id);
	});


	// sorting

	const tableHeads = $('.table_container table th');

	const head_page = tableHeads[0];
	const head_views = tableHeads[1];
	const head_clicks = tableHeads[2];
	const head_convertion = tableHeads[3];

	$(head_page).on('click', function(){

		arrowToggle($(this));
		sortTable($(this).data('sort_ascending'), $(this));
		$(this).data('sort_ascending', !sortAscending);

	});

	$(head_views).on('click', function(){

		arrowToggle($(this));
		sortTable($(this).data('sort_ascending'), $(this));
		$(this).data('sort_ascending', !sortAscending);

	});

	$(head_clicks).on('click', function(){

		arrowToggle($(this));
		sortTable($(this).data('sort_ascending'), $(this));
		$(this).data('sort_ascending', !sortAscending);

	});

	$(head_convertion).on('click', function(){

		arrowToggle($(this));
		sortTable($(this).data('sort_ascending'), $(this));
		$(this).data('sort_ascending', !sortAscending);

	});

	function arrowToggle(th){
		if(!th.hasClass('ascending')){
			th.addClass('ascending');
		}else{
			th.removeClass('ascending');
		}
	}

	function sortTable(sortDirection, sortBy){

		gsortBy = $(sortBy).index();

		sortAscending = sortDirection;

		let rows = $('.table_container table tr').get();
		rows.shift();

		$('table tr:not(:first)').remove();

		if(gsortBy == 0){
			sortedRows = rows.sort(cmp);
		}else{
			sortedRows = rows.sort(cmpNums);
		}

		$(rows).each(function(i){
			$('.table_container table').append('<tr>' + rows[i].innerHTML + '</tr>');
		});

	}

	function cmp(a, b) {

		firstName = $(a).find('td').get(gsortBy); 
		firstName = $(firstName).text().toLowerCase();
		secondName = $(b).find('td').get(gsortBy);
		secondName = $(secondName).text().toLowerCase();

		if (firstName < secondName){ 
			return (sortAscending) ? -1 : 1 
		}else if (firstName > secondName) { 
			return (sortAscending) ? 1 : -1 
		}
		else { 
			return 0 
		}
	}

	function cmpNums(a, b){

		firstName = $(a).find('td').get(gsortBy); 
		firstName = $(firstName).text().toLowerCase();
		secondName = $(b).find('td').get(gsortBy);
		secondName = $(secondName).text().toLowerCase();

		if(sortAscending){
			return parseInt(firstName) - parseInt(secondName);
		}else{
			return parseInt(secondName) - parseInt(firstName);	
		}
		
	}

});