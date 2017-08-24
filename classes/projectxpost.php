<?php

	include('post_content_blocks.php');

	class ProjectXPost {

		var $campaign_postTypes;
		var $cb_postTypes;

		function topAd($content){

			// check if campaigns
			global $post;

			if(!is_single()){
				return $content;
			}

			if(!in_array($post->post_type, $this->campaign_postTypes))
				return $content;

			$post_id = get_the_ID();

			$funnel_position = get_post_meta($post_id, 'funnel_position');

			if(empty($funnel_position)){
				return $content;
			}

			$funnel_position = $funnel_position[0];
			$outcome = get_post_meta($post_id, 'px_outcome');
			
			if(empty($outcome)){
				return $content;
			}

			$outcome = explode('###', $outcome[0]);
			$o_id = $outcome[0];
			$o_title = $outcome[1];
			// $o_fp = $outcome[2];

			$page_url = $_SERVER['REQUEST_URI'];
			$ip = $_SERVER['REMOTE_ADDR'];

			global $wpdb;
			$ad_link = $wpdb->get_results("SELECT id, top_ad, top_link FROM " . $wpdb->prefix . "px_adverts WHERE outcome_id = $o_id AND funnel_position = '$funnel_position';");

			if(!empty($ad_link)){
				if($ad_link[0]->top_link != ''){
					$ad = '<a class="ad_track"';
				 	$ad .= ' data-post_id="' . $post_id . '"';
				 	$ad .= ' data-page_url="' . $page_url . '"';
				 	$ad .= ' data-ip="' . $ip . '"';
				 	$ad .= ' data-advert_id="' . $ad_link[0]->id . '"';
				 	$ad .= ' href="' . $ad_link[0]->top_link . '"><img src="' . $ad_link[0]->top_ad .'"></a>';
					$content = $ad . $content;

					return $content;
				}
			}

			return $content; 

		}

		function sideAd(){

			// check if campaigns
			global $post;

			if(!is_single()){
				return;
			}

			if(!in_array($post->post_type, $this->campaign_postTypes))
				return;

			$funnel_position = get_post_meta(get_the_ID(), 'funnel_position');

			if(empty($funnel_position)){
				return;
			}

			$funnel_position = $funnel_position[0];
			$outcome = get_post_meta(get_the_ID(), 'px_outcome');
			$page_url = $_SERVER['REQUEST_URI'];
			$ip = $_SERVER['REMOTE_ADDR'];

			if(!empty($outcome)){
				$outcome = explode('###', $outcome[0]);
				$o_id = $outcome[0];
				$o_title = $outcome[1];
				// $o_fp = $outcome[2];

				global $wpdb;
				$ad_link = $wpdb->get_results("SELECT id, side_ad, side_link FROM " . $wpdb->prefix . "px_adverts WHERE outcome_id = $o_id AND funnel_position = '$funnel_position';");
			
			 	if(!empty($ad_link)){
				 	$ad = '<a class="ad_track px_sidead" href="' . $ad_link[0]->side_link . '" ';
				 	$ad .= ' data-post_id="' . get_the_ID() . '"';
				 	$ad .= ' data-ip="' . $ip . '"';
				 	$ad .= ' data-page_url="' . $page_url . '"';
				  	$ad .= ' data-advert_id="' . $ad_link[0]->id . '"';
				 	$ad .= '><img src="' . $ad_link[0]->side_ad .'"></a>';
					echo $ad;
			 	}
			}
			
		}

		function the_content_blocks($content){
			
			global $post;

			if(!in_array($post->post_type, $this->cb_postTypes))
				return $content;

			$post_id = get_the_ID();

			global $wpdb;
			$cbs = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_content_blocks WHERE post_id = $post_id ORDER BY id;");

			foreach ($cbs as $block) {
			
				$blockColor = $block->block_color;

				if($blockColor != '#ffffff'){
					$content .= '<div class="a_content_block" style="background: ' . $blockColor . '; padding: 10px;">';
				}else{
					$content .= '<div class="a_content_block">';
				}

				if(method_exists($this->contentBlocks, 'output_' . $block->block_type))
					$content .= call_user_func(array($this->contentBlocks, 'output_' . $block->block_type), $block->block_content);

				$content .= '</div>';

			}

			return $content;

		}

		function bottomAd($content){
			
			global $post;

			if(!is_single()){
				return $content;
			}

			if(!in_array($post->post_type, $this->campaign_postTypes))
				return $content;

			$funnel_position = get_post_meta(get_the_ID(), 'funnel_position');

			if(empty($funnel_position)){
				return $content;
			}

			$funnel_position = $funnel_position[0];
			$outcome = get_post_meta(get_the_ID(), 'px_outcome');
			$page_url = $_SERVER['REQUEST_URI'];
			$ip = $_SERVER['REMOTE_ADDR'];

			if(empty($outcome)){
				return $content;
			}

			$outcome = explode('###', $outcome[0]); 
			$o_id = $outcome[0];
			$o_title = $outcome[1];
			// $o_fp = $outcome[2];

			global $wpdb;
			$ad_link = $wpdb->get_results("SELECT id, bottom_ad, bottom_link FROM " . $wpdb->prefix . "px_adverts WHERE outcome_id = $o_id AND funnel_position = '$funnel_position';");

		    if(!empty($ad_link)){
		    	if($ad_link[0]->bottom_link != ''){
		    		$ad = '<a class="ad_track" href="' . $ad_link[0]->bottom_link . '"';
			 		$ad .= ' data-post_id="' . get_the_ID() . '"';
				 	$ad .= ' data-page_url="' . $page_url . '"';
				 	$ad .= ' data-ip="' . $ip . '"';
				 	$ad .= ' data-advert_id="' . $ad_link[0]->id . '"';
				 	$ad .= '><img src="' . $ad_link[0]->bottom_ad .'"></a>';
				 	$content .= $ad;
					return $content;
		    	}
		 	}

		 	return $content;

		}

		function meta_box_setup(){

			/* Add meta boxes on the 'add_meta_boxes' hook. */
		  	add_action( 'add_meta_boxes', array($this, 'add_meta_box') );

		  	/* Save post meta on the 'save_post' hook. */
			add_action( 'save_post', array($this, 'save_meta_data'), 10, 2 );

		}

		function add_meta_box($post_type){

			$options = new PxOptions();
			$postTypes = $options->get_campaigns_post_types();

			if(in_array($post_type, $postTypes)){

				add_meta_box(
					'px_campaigns_box',      // Unique ID
					esc_html__( 'Campaign', 'example' ),    // Title
					array($this, 'output_campaign_meta_box'),   // Callback function
					'',         // Admin page (or post type)
					'side',         // Context
					'high'         // Priority
				);

			}

		}

		/* Display the post meta box. */
		function output_campaign_meta_box( $object, $box ) { ?>

		  <?php wp_nonce_field( basename( __FILE__ ), 'px_campaign_nonce' ); ?>

			<?php

				$c = get_post_meta( $object->ID, 'px_campaign', true );
				$c = explode('###', $c);
				$o = get_post_meta( $object->ID, 'px_outcome', true );
				$o = explode('###', $o);
				$fp = get_post_meta( $object->ID, 'funnel_position', true );
			?>

			<div class="campaign_box" data-outcomeid="<?php echo $o[0]; ?>" data-funnelposition="<?php echo $fp; ?>">

		 	<label for="px_campaign"><?php _e( "Campaign", 'example' ); ?></label>
		 	<select name="px_campaign" id="px_campaign_select">
		 		<option value="<?php echo get_post_meta( $object->ID, 'px_campaign', true ); ?>">- select campaign -</option>

		 		<?php

		 			global $wpdb;
					$all_campaigns = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "campaigns;");
					
					foreach ($all_campaigns as $single_campaign) {

						$selected = '';

						if($c[1] == $single_campaign->title){
							$selected = 'selected';
						}

						echo '<option data-campaignid="' . $single_campaign->id . '" value="' . $single_campaign->id . '###' . $single_campaign->title . '" ' . $selected . '>' . $single_campaign->title  . '</option>';					
					}

		 		?>

		 	</select>
		 	<br>
		 	<p id="outcome_box">
			 	<label>Outcomes</label>
			 	<select name="px_outcome" id="px_outcome_select">
			 		<option value="<?php echo get_post_meta( $object->ID, 'px_outcome', true ); ?>">- select outcome -</option>

			 		<?php

			 			global $wpdb;

			 			if($o[0] != ''){

			 				$outcomes = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "outcomes WHERE campaign_id=" . $c[0]);
				 			foreach ($outcomes as $outcome) { 
				 			
			 					$selected = '';

								if($o[1] == $outcome->title){
									$selected = 'selected';
								}

				 				echo '<option data-outcomeid="' . $o[0] . '" value="' . $outcome->id . '###' . $outcome->title . '" ' . $selected . '>' . $outcome->title . '</option>';
				 			
				 			}

			 			}

			 		?>

			 	</select>
		 	</p>
		 	<div id="outcome_spin" class="spin-relative"></div>
		 	<label>Funnel Position</label>

		 	<?php

		 		$fps = ['Awareness', 'Research', 'Comparison', 'Purchase'];

		 		foreach ($fps as $fp) {

		 			$checked = '';

		 			if(strtolower($fp) == get_post_meta( $object->ID, 'funnel_position', true )){
		 				$checked = 'checked';
		 			}

		 			echo '<input type="radio" name="funnel_position" value="' . strtolower($fp) . '" ' . $checked . '> ' . $fp . ' <br/>';
		 		}

		 	?>

		  </div>
		<?php }

		function save_meta_data( $post_id, $post ){

			/* Verify the nonce before proceeding. */
			if ( !isset( $_POST['px_campaign_nonce'] ) || !wp_verify_nonce( $_POST['px_campaign_nonce'], basename( __FILE__ ) ) )
				return $post_id;

			/* Get the post type object. */
		  	$post_type = get_post_type_object( $post->post_type );

		  	/* Check if the current user has permission to edit the post. */
		  	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		    	return $post_id;

		    /* Get the posted data and sanitize it for use as an HTML class. */
		  	$new_campaign = $_POST['px_campaign']; // ( isset( $_POST['px_campaign'] ) ? : '' );
		  	$new_outcome = $_POST['px_outcome'];
		  	$new_funnel_position = $_POST['funnel_position'];

			/* Get the meta key. */
		 	$meta_key = 'px_campaign';  
		 	$outcome_meta_key = 'px_outcome';	
		 	$fp_meta_key = 'funnel_position';

		 	/* Get the meta value of the custom field key. */
		  	$campaign = get_post_meta( $post_id, $meta_key, true );
		  	$outcome = get_post_meta( $post_id, $outcome_meta_key, true );
		  	$funnel_position = get_post_meta( $post_id, $fp_meta_key, true );

		  	/* If a new meta value was added and there was no previous value, add it. */
		  	if ( $new_campaign && '' == $campaign )
		    	add_post_meta( $post_id, $meta_key, $new_campaign, true );

		    /* If the new meta value does not match the old value, update it. */
			elseif ( $new_campaign && $new_campaign != $campaign )
				update_post_meta( $post_id, $meta_key, $new_campaign );

			/* If there is no new meta value but an old value exists, delete it. */
			elseif ( '' == $new_campaign && $campaign )
				delete_post_meta( $post_id, $meta_key, $campaign );



		    if ( $new_outcome && '' == $outcome )
		    	add_post_meta( $post_id, $outcome_meta_key, $new_outcome, true );

		    elseif ( $new_outcome && $new_outcome != $outcome )
				update_post_meta( $post_id, $outcome_meta_key, $new_outcome );

			elseif ( '' == $new_outcome && $outcome )
				delete_post_meta( $post_id, $outcome_meta_key, $outcome );



		    if ( $new_funnel_position && '' == $funnel_position )
		    	add_post_meta( $post_id, $fp_meta_key, $new_funnel_position, true );

			elseif ( $new_funnel_position && $new_funnel_position != $funnel_position )
				update_post_meta( $post_id, $fp_meta_key, $new_funnel_position );

			elseif ( '' == $new_funnel_position && $funnel_position )
				delete_post_meta( $post_id, $fp_meta_key, $funnel_position );

			// update stats table
			$campaignid = explode('###', $new_campaign);
			$campaignid = $campaignid[0];
			$outcomeid = explode('###', $new_outcome);
			$outcomeid = $outcomeid[0];

			global $wpdb;
			$wpdb->query("UPDATE " . $wpdb->prefix . "px_stats SET funnel_position='$new_funnel_position', campaign_id='$campaignid', outcome_id='$outcomeid' WHERE post_id=$post_id");

		}

		function list_outcomes(){

			$campaign_id = $_POST['campaign_id'];

			global $wpdb;
			$the_outcomes = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "outcomes WHERE campaign_id=$campaign_id");

			$response['outcomes'] = $the_outcomes;
			$response['success'] = 1;
			echo json_encode($response);
		    die();

		}

		function nextFunnelPos($fp){

			$allFps = ['awareness', 'research', 'comparison', 'purchase'];
			$pos = array_search($fp, $allFps);

			$next_pos = $pos + 1;

			if($next_pos >= sizeof($allFps)){
				return 'end';
			}else{
				return $allFps[$next_pos];
			}

		}

		function next_post_in_funnel($content){

			$postId = get_the_ID();

			$outcome = get_post_meta( $postId, 'px_outcome', true );
			$funnel_position = get_post_meta( $postId, 'funnel_position', true );

			$nextFp = $this->nextFunnelPos($funnel_position);

			if($nextFp != 'end'){

				$args = array(
				   'meta_query' => array(
						array(
						   'key' => 'px_outcome',
						   'value' => $outcome,
						   'compare' => 'LIKE',
						),
						array(
						   'key' => 'funnel_position',
						   'value' => $nextFp,
						   'compare' => 'LIKE',
						)
				   ),
				   'numberposts' => 3
				);
				$posts = get_posts($args);

				if(!empty($posts)){
					
					$nextPosts = '<div class="px_nextposts">';
				
						foreach ($posts as $post) { 

							$nextPosts .= '<div class="px_nextpost">';
								$nextPosts .= '<a href="' . $post->guid . '">';
								$nextPosts .= '<img src="' . get_the_post_thumbnail_url($post->ID) . '">';
								$nextPosts .= '<p>' . $post->post_title . '</p>';
								$nextPosts .= '</a>';
							$nextPosts .= '</div>';

						}

					$nextPosts .= '</div>';

					return $content . $nextPosts;

				}

				return $content;

			}

		}

		function __construct(){

			$options = new PxOptions();
			$this->campaign_postTypes = $options->get_campaigns_post_types();
			$this->cb_postTypes = $options->get_cb_post_types();
			$this->contentBlocks = new PostContentBlocks();

			add_filter( 'the_content', array($this, 'topAd'), 10 );
			add_action( 'dynamic_sidebar_before', array($this, 'sideAd') );
			add_filter( 'the_content', array($this, 'the_content_blocks'), 11 );
			add_filter( 'the_content', array($this, 'bottomAd'), 12 );
			add_filter( 'the_content', array($this, 'next_post_in_funnel'), 14 );

			// add_filter( 'the_content', array($this, 'modalBox'), 12 );

			add_action( 'load-post.php', array($this, 'meta_box_setup') );
			add_action( 'load-post-new.php', array($this, 'meta_box_setup') );
			add_action( 'wp_ajax_list_outcomes', array($this, 'list_outcomes') );
			add_action( 'wp_ajax_nopriv_list_outcomes', array($this, 'list_outcomes') );

		}

	}

?>