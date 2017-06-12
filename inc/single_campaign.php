
<?php

	$campaignid = $_GET['campaignid'];

	global $wpdb;
	$campaignInfo = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "campaigns WHERE id = $campaignid;");

	if(empty($campaignInfo)){
		echo '<a class="back_link" href="' . home_url() . '/wp-admin/admin.php?page=campaigns">Back to Campaigns</a>';
		exit();
	}

?>

<div class="px_container">

	<ul class="px_breadcrumb">
		<li><a href="admin.php?page=campaigns">Campaigns</a></li>
		<li>&#10095;&#10095;</li>
		<li><a href="#"><?php echo $campaignInfo[0]->title; ?></a></li>
	</ul>

	<ul class="updateForm">
		<li><label>Title: </label></li>
		<li><input id="title_input" type="text" value="<?php echo $campaignInfo[0]->title; ?>"></li>
		<li><label>Description: </label></li>
		<li><textarea id="desc_input"><?php echo $campaignInfo[0]->description; ?></textarea></li>		
		<li><label>Outcomes: </label></li>
		<li>
			<input type="text" placeholder="Enter a selling point..." id="input_sellingpoint">
			<button id="add_btn"></button>
		</li>
		<li>
			<ul id="selling_points" data-campaignid="<?php echo $campaignid; ?>">
				<?php

					global $wpdb;
					$outcomes = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "outcomes WHERE campaign_id = $campaignid;");

					foreach ($outcomes as $outcome) {
						echo '<li data-outcomeid="' . $outcome->id . 
						'"><span></span><p>'. $outcome->title . '</p><a class="btn designFunnel" href="?page=single-outcome&outcomeid=' . $outcome->id . '&campaignid=' . $campaignid . '&campaign_name=' . $campaignInfo[0]->title . '">Design Funnel</a></li>';
					}

				?>
			</ul>
		</li>
		<li>
			<button id="update_campaign" class="edit">Update Campaign</button>
			<button id="delete_campaign" class="delete">Delete Campaign</button>
		</li>
	</ul>

	<div id="notices">
		<div></div>
		<p class="errorNotice"></p>
	</div>

</div>