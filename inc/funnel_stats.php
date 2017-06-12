
<?php

	global $wpdb;
//	$pages = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_stats;");
	$campaigns = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "campaigns;");

?>

<div class="px_container">

	<h1>Funnel Stats</h1>

	<div class="fs_campaigns_box">
		<h2>Campaigns</h2>
		<ul id="fs_campaigns">
			<?php
				foreach ($campaigns as $campaign) {
					echo '<li data-campaignid="' . $campaign->id . '">' . $campaign->title . '</li>';
				}
			?>
		</ul>
	</div>
	<div class="main_stats">

		<h2 id="campaign_title"></h2>

		<div id="select_outcomes"></div>

		<ul id="fs_filters">
			<li class="current_funnel_filter">All</li>
			<li>Awareness</li>
			<li>Research</li>
			<li>Comparison</li>
			<li>Purchase</li>
		</ul>

		<?php global $plugin_url; ?>

		<div class="table_container">
			<img class="spin" src="<?php echo $plugin_url; ?>/img/spin.gif">
			<table class="pxtable pxStatsTable">
				<tr>
					<th data-sort_ascending="true">Page</th>
					<th data-sort_ascending="true">Page Views</th>
					<th data-sort_ascending="true">Clicks</th>
					<th data-sort_ascending="true">Conversion %</th>
				</tr>
			</table>
		</div>
	</div>

</div>