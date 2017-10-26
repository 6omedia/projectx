<div class="px_container">

	<h1>Project X</h1>

	<?php

		/*

			OPTIONS

			'px_cb_post_types'
			'px_campaigns_post_types'

		*/

		$cb_post_types = [];
		$campaigns_post_types = [];
		$jollyfrog_api = '';

		if(isset($_POST['px_options_submitted'])){
			$hidden_field = esc_html($_POST['px_options_submitted']);
			if($hidden_field == "Y"){
				
				if(isset($_POST['cb_post_types']))
					$options['px_cb_post_types'] = $_POST['cb_post_types'];

				if(isset($_POST['campaigns_post_types']))
					$options['px_campaigns_post_types'] = $_POST['campaigns_post_types'];

				if(isset($_POST['jollyfrog_api']))
					$options['px_jollyfrog_api'] = $_POST['jollyfrog_api'];
				
				update_option('px_options', $options);

			}
		}

		$options = get_option('px_options');
		
		if($options != ''){
			
			if(isset($options['px_cb_post_types'])){
				if($options['px_cb_post_types'] != ''){
					$cb_post_types = $options['px_cb_post_types'];
				}
			}

			if(isset($options['px_campaigns_post_types'])){
				if($options['px_campaigns_post_types'] != ''){
					$campaigns_post_types = $options['px_campaigns_post_types'];
				}
			}

			if(isset($options['px_jollyfrog_api'])){
				if($options['px_jollyfrog_api'] != ''){
					$jollyfrog_api = $options['px_jollyfrog_api'];
				}
			}

		}

		$postTypes = get_post_types(array(
			'_builtin' => false,
			'public' => true
		));

		$postTypes['post'] = 'post';
		$postTypes['page'] = 'page';

	?>

	<form name="px_options_form" action="" method="post" class="pxForm">

		<input type="hidden" name="px_options_submitted" value="Y">
	
		<div class="formCol">

			<h2>Content Block Options</h2>

			<h3>Post Types to use content blocks</h3>
			<ul class="postTypeOptions">

			<?php

				foreach ($postTypes as $postType) { 
					
					$checked = '';

					if(in_array($postType, $cb_post_types)){
						$checked = 'checked';					
					}

					?>

					<li>
						<input class="checkBoxInput" name="cb_post_types[]" type="checkbox" value="<?php echo $postType; ?>" <?php echo $checked; ?>>
						<label><?php echo $postType; ?></label>
					</li>

			<?php } ?>

			</ul>

		</div>

		<div class="formCol">

			<h2>Campaign Options</h2>

			<h3>Post Types to use campaigns</h3>
			<ul class="postTypeOptions">

			<?php

				foreach ($postTypes as $postType) { 
					
					$checked = '';

					if(in_array($postType, $campaigns_post_types)){
						$checked = 'checked';					
					}

					?>

					<li>
						<input class="checkBoxInput" name="campaigns_post_types[]" type="checkbox" value="<?php echo $postType; ?>" <?php echo $checked; ?>>
						<label><?php echo $postType; ?></label>
					</li>

			<?php } ?>

			</ul>

		</div>

		<div class="jollyFrog">
			<p>If you have an account with JollyFrog you can add your API key below</p>
			<input type="text" name="jollyfrog_api" placeholder="JollyFrog API Key" value="<?php echo $jollyfrog_api; ?>">
		</div>
		<button type="submit">Save Options</button>
	</form>

	<a href="admin.php?page=campaigns">Create and manage campaigns</a><br/>
	<br/>
	<a href="admin.php?page=funnel_stats">View statistics</a><br/>
	<br/>
	<a href="admin.php?page=excluded_ips">Manage excluded IP address's</a>

</div>