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

		?>

		<div class="px_sidead">
			<a href="<?php echo $instance['pxad_link']; ?>">
				<img src="<?php echo $instance['pxad_img']; ?>">
			</a>
		</div>

		<?php
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