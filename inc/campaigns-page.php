
<div class="px_container">

	<h1>Campaigns</h1>

	<div class="add_new_box">
		<h2>Add a Campaign</h2>
		<ul>
			<li><label>Campaign Title</label></li>
			<li><input id="c_title" type="text"></li>
			<li><label>Description</label></li>
			<li><textarea id="c_desc"></textarea></li>
			<li><label>Add selling points to campaign</label></li>
			<li>
				<input type="text" placeholder="Enter a selling point..." id="input_sellingpoint">
				<button id="add_btn"></button>
				<ul id="selling_points">
					
				</ul>
			</li>
			<li><button id="submit">Create Campaign</button></li>
			<div id="notices">
				<div></div>
				<p class="errorNotice"></p>
			</div>
		</ul>
	</div>

	<div class="campaign_list_box">
		<h2>Current Campaigns</h2>
		<ul id="campaignList" class="c_list">

			<?php

				global $wpdb;
				$campaigns = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "campaigns;");
				
				foreach ($campaigns as $campaign) {
					echo '<li><a href="?page=single-campaign&campaignid=' . $campaign->id . '">' . $campaign->title . '</a></li>';
				}

			?>

		</ul>
	</div>

</div>