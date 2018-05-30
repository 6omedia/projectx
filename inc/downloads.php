<?php

	$response = $this->downloadManager->addDownload();

	// if(isset($_POST['csvdownload'])){
	// 	$this->downloadManager->email_csv_download(array(
	// 	  array(1,2,3,4), // this array is going to be the first row
	// 	  array(1,2,3,4)), // this array is going to be the second row
	// 	  "numbers.csv"
	// 	);
	// }

?>

<div class="px_container" id="downloadsPage">

	<div id="notices">
		<?php if(isset($response['error'])){ ?>
			<p class="errorNotice" <?php if($response['error'] != '') echo 'style="display: block"' ?>><?php echo $response['error']; ?></p>
		<?php }else{ ?>
			<div <?php if($response != '') echo 'style="display: block"' ?>><?php echo $response; ?></div>
		<?php } ?>
	</div>

	<h1>Downloads</h1>

	<h2>Privacy Policy</h2>
	<div id="ppQuill">
		<?php echo stripslashes(get_option('px_privacypolicy')); ?>
	</div>
	<button id="updateBtn">Update Privacy Policy</button>

	<h2>Downloads</h2>

	<form id="uploadForm" action="" enctype="multipart/form-data" method="post">
		<table>
			<tr>
				<th>File</th>
				<th>Campaign</th>
				<th>Outcome</th>
				<th>Funnel Position</th>
				<th></th>
			</tr>
			<tr>
				<td>
					<input type="file" name="thefile" id="fileInput">
				</td>
				<td>
					<select id="dl_campaign" name="campaign">
						<option value="">- select campaign -</option>

						<?php 

							global $wpdb;
							$campaigns = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "campaigns;");

							foreach ($campaigns as $campaign) {
								
								echo '<option value="' . $campaign->id . '###' . $campaign->title . '">' . $campaign->title . '</option>';
						
							}

						?>

					</select>
				</td>
				<td>
					<select id="dl_outcome" name="outcome" disabled>
						<option value="">- select outcome -</option>

						
					</select>
					<div class="spin-relative"></div>
				</td>
				<td>
					<select id="dl_funnelpos" name="funnelpos">
						<option value="">- select funnel position -</option>
						<option value="awareness">Awareness</option>
						<option value="research">Research</option>
						<option value="comparison">Comparison</option>
						<option value="purchase">Purchase</option>
					</select>
				</td>
				<td>
					<button type="submit" id="sendBtn" class="btn_style">
						Add File for Download
					</button>
				</td>
			</tr>
		</table>

	</form>

	<table class="downloadsTable pxtable">
		<tr>
			<th>File Name</th>
			<th>Campaign</th>
			<th>Outcome</th>
			<th>Funnel Position</th>
			<th>Emails Captured</th>
			<th></th>
		</tr>

		<?php

			$dl = $wpdb->prefix . "px_downloads";
			$el = $wpdb->prefix . "px_email_list";

			global $wpdb;
			$downloads = $wpdb->get_results("SELECT $dl.id, $dl.filename, $dl.campaign, $dl.outcome, $dl.funnel_position, COUNT($el.email) AS captured_emails FROM " . $wpdb->prefix . "px_email_list RIGHT JOIN " . $wpdb->prefix . "px_downloads ON $dl.id = $el.download_id GROUP BY $dl.id");

			foreach ($downloads as $download) { ?>

				<tr>
					<td>
						<a href=""><?php echo $download->filename; ?></a>
					</td>
					<td>
						<?php echo $download->campaign; ?>
					</td>
					<td>
						<?php echo $download->outcome; ?>
					</td>
					<td>
						<?php echo $download->funnel_position; ?>
					</td>
					<td>
						<?php echo $download->captured_emails; ?>
					</td>
					<td>
						<form action="<?php echo home_url(); ?>/wp-content/plugins/projectx/csv_email.php" method="POST">
							<input type="hidden" name="csvdownload" value="<?php echo $download->id; ?>">
							<button class="export"></button>
							<span class="minus-span" data-download_id="<?php echo $download->id; ?>"></span>
						</form>
					</td>
				</tr>
		
		<?php } ?>

	</table>

</div>