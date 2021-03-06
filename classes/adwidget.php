<?php

class ProjectXAdWidget extends WP_Widget {

	/**
	* To create the example widget all four methods will be 
	* nested inside this single instance of the WP_Widget class.
	**/

	public function __construct() {
		$widget_options = array( 
		  'classname' => 'prox_ad_widget',
		  'description' => 'This is a widget to display ads from the project x plugin',
		);
		parent::__construct( 'prox_ad_widget', 'Project X Adverts', $widget_options );
	}

	public function widget( $args, $instance ) {

		$link = $instance['pxad_link'];
		$img = $instance['pxad_img'];

		if(!$link || !$img){

		//	$projectx_page->sideAd();


			/* START OF SIDEAD */

			// check if campaigns
			global $post;

			if(!is_single()){
				return;
			}

			$options = new PxOptions();

			if(!in_array($post->post_type, $options->get_campaigns_post_types()))
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

			/* END OF SIDEAD */

			?>

			<?php			

		}else{

			?>

			<div class="px_sidead">
				<a href="<?php echo $link; ?>">
					<img src="<?php echo $img; ?>">
				</a>
			</div>

			<?php

		}

	}

	public function form( $instance ) {
		$pxad_link = ! empty( $instance['pxad_link'] ) ? $instance['pxad_link'] : '';
		$pxad_img = ! empty( $instance['pxad_img'] ) ? $instance['pxad_img'] : '';

		global $wpdb;
		$campaigns = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'campaigns');

		?>

		<input name="<?php echo $this->get_field_name('pxad_link'); ?>" type="hidden" value="<?php echo esc_attr($pxad_link); ?>">
		<input name="<?php echo $this->get_field_name('pxad_img'); ?>" type="hidden" value="<?php echo esc_attr($pxad_img); ?>">

		<?php 

			if($pxad_img != ''){
				echo '<p>Current Ad</p>';
				echo '<img src="' . $pxad_img . '" width=100>';
			}

		?>

		<div class="px">
			<label>Select Campaign</label>
			<div class="spin"></div>
			<select class="adWidgetSelect">
				<option value="">- select -</option>
				<?php foreach ($campaigns as $campaign) { ?>
					<option value="<?php echo $campaign->id ?>"><?php echo $campaign->title ?></option>
				<?php } ?>
			</select>
		</div>

		<div class="px outcomeSelectCont">
			<label>Select Outcome</label>
			<div class="spin"></div>
			<select class="adWidgetOutcomes"></select>
		</div>

		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance[ 'pxad_link' ] = strip_tags( $new_instance[ 'pxad_link' ] );
		$instance[ 'pxad_img' ] = strip_tags( $new_instance[ 'pxad_img' ] );
		return $instance;
	}

}

?>