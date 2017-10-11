<?php 

/* 
 * Plugin Name: Projext X
 * Plugin URI: http://6omedia.co.uk
 * Description: Create and manage campaigns
 * Version: 1.0
 * Author: Mike Rockett
 * Author URI:
 * License: GPL2
*/

/*************************************
	CREATE TABLES UPON ACTIVATION
**************************************/

include('classes/content_blocks.php');
include('classes/projectxpost.php');
include('classes/campaigns.php');
include('classes/downloads.php');
include('classes/excludedips.php');
include('classes/tracking_helpers.php');
include('classes/tracker.php');
include('classes/funnel_stats.php');

$plugin_url = WP_PLUGIN_URL . '/projectx';
$options = array();

class ProjectX {

	private $plugin_url;

	function setup_database_tables(){

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$table_name = $wpdb->prefix . 'campaigns';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			title varchar(60) DEFAULT '' NOT NULL,
			description varchar(1000) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		$table_name = $wpdb->prefix . 'outcomes';

		$sql_outcomes = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			campaign_id int(11) NOT NULL,
			title varchar(60) DEFAULT '' NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		dbDelta( $sql_outcomes );

		$table_name = $wpdb->prefix . 'px_adverts';

		$sql_adverts = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			outcome_id int(11) NOT NULL,
			top_ad varchar(255) DEFAULT '' NOT NULL,
			top_link varchar(255) DEFAULT '' NOT NULL,
			side_ad varchar(255) DEFAULT '' NOT NULL,
			side_link varchar(255) DEFAULT '' NOT NULL,
			bottom_ad varchar(255) DEFAULT '' NOT NULL,
			bottom_link varchar(255) DEFAULT '' NOT NULL,
			funnel_position varchar(30) DEFAULT '' NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		dbDelta( $sql_adverts );

		$table_name = $wpdb->prefix . 'px_content_adverts';

		$sql_sc_adverts = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			title varchar(60) DEFAULT '' NOT NULL,
			outcome_id int(11) NOT NULL,
			ad_link varchar(255) DEFAULT '' NOT NULL,
			ad_img varchar(255) DEFAULT '' NOT NULL,
			funnel_position varchar(30) DEFAULT '' NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		dbDelta( $sql_sc_adverts );

		$table_name = $wpdb->prefix . 'px_content_blocks';

		$sql_cb = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			post_id mediumint(9) NOT NULL,
			block_content TEXT NOT NULL,
			list_position int(11) NOT NULL,
			block_type varchar(30) DEFAULT '' NOT NULL,
			block_color varchar(30) DEFAULT '' NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		dbDelta( $sql_cb );

		/* tracking tables */

		$table_name = $wpdb->prefix . 'px_stats';

		$sql_stats = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			post_id varchar(10) NOT NULL,
			page_url varchar(255) NOT NULL,
			page_title varchar(255) NOT NULL,
			funnel_position varchar(30) NOT NULL,
			campaign_id mediumint(9) NOT NULL, 
			outcome_id mediumint(9) NOT NULL,
			page_views int(11) NOT NULL,
			clicks int(11) NOT NULL,
			total_users int(11) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		dbDelta( $sql_stats );

		// Page Visits

		$table_name = $wpdb->prefix . 'px_page_visits';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id mediumint(9) NOT NULL,
			page_url varchar(255) NOT NULL,
			time_viewed TIMESTAMP NOT NULL,
			outcome_id mediumint(9) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		dbDelta( $sql );

		// Ad Clicks

		$table_name = $wpdb->prefix . 'px_ad_clicks';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id mediumint(9) NOT NULL,
			page_url varchar(255) NOT NULL,
			post_id varchar(10) NOT NULL, 
			advert_id varchar(255) NOT NULL,
			time_clicked TIMESTAMP NOT NULL,
			outcome_id mediumint(9) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		dbDelta( $sql );

		// Users

		$table_name = $wpdb->prefix . 'px_users';

		$sql_stats = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			ip varchar(50) NOT NULL,
			email varchar(90) NOT NULL,
			blocked TINYINT(1) NOT NULL DEFAULT 0,
			description varchar(100) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		dbDelta( $sql_stats );

		// Downloads

		$table_name = $wpdb->prefix . 'px_downloads';

		$sql_stats = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			filename varchar(100) NOT NULL,
			captured_emails int(11) NOT NULL,
			funnel_position varchar(30) NOT NULL,
			campaign_id mediumint(9) NOT NULL, 
			outcome_id mediumint(9) NOT NULL,
			campaign varchar(50) NOT NULL, 
			outcome varchar(50) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		dbDelta( $sql_stats );

		// Email List

		$table_name = $wpdb->prefix . 'px_email_list';

		$sql_stats = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			email varchar(100) NOT NULL,
			download_id int(11) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		dbDelta( $sql_stats );

	}

	function add_plugin_menu(){

		add_menu_page(
			'Project X',
			'Project X',
			'manage_options',
			'project-x',
			array($this, 'px_options_page')
		);

		add_submenu_page(
			'project-x',
			'Campaigns',
			'Campaigns',
			'manage_options',
			'campaigns',
			array($this, 'px_campaigns_page')
		);

		add_submenu_page(
			'project-x',
			'Funnel Stats',
			'Funnel Stats',
			'manage_options',
			'funnel_stats',
			array($this, 'px_funnel_stats_page')
		);

		add_submenu_page(
			'project-x',
			'Excluded IP\'s',
			'Excluded IP\'s',
			'manage_options',
			'excluded_ips',
			array($this, 'px_exclude_ips_page')
		);

		add_submenu_page(
			'project-x',
			'Downloads',
			'Downloads',
			'manage_options',
			'downloads',
			array($this, 'px_downloads')
		);

		add_submenu_page (
			'null',
			'Campaigns',
			'',
			'manage_options',
			'single-campaign',
			array($this, 'px_campaigns_single')
		);

		add_submenu_page (
			'null',
			'Outcome',
			'',
			'manage_options',
			'single-outcome',
			array($this, 'px_outcome_single')
		);

	}

	function px_options_page(){

		if( !current_user_can( 'manage_options' ) ){
			wp_die( 'You do not have sufficient permissions to access this page' );
		}

		require( 'inc/options.php' );

	}

	function px_campaigns_page(){

		if( !current_user_can( 'manage_options' ) ){
			wp_die( 'You do not have sufficient permissions to access this page' );
		}

		require( 'inc/campaigns-page.php' );
		
	}

	function px_campaigns_single(){

		if( !current_user_can( 'manage_options' ) ){
			wp_die( 'You do not have sufficient permissions to access this page' );
		}

		require( 'inc/single_campaign.php' );	
	}

	function px_outcome_single(){

		if( !current_user_can( 'manage_options' ) ){
			wp_die( 'You do not have sufficient permissions to access this page' );
		}

		require( 'inc/single_outcome.php' );	

	}

	function px_funnel_stats_page(){

		if( !current_user_can( 'manage_options' ) ){
			wp_die( 'You do not have sufficient permissions to access this page' );
		}

		require( 'inc/funnel_stats.php' );	

	}

	function px_exclude_ips_page(){

		if( !current_user_can( 'manage_options' ) ){
			wp_die( 'You do not have sufficient permissions to access this page' );
		}

		require( 'inc/excluded_ips.php' );

	}

	function px_downloads(){

		if( !current_user_can( 'manage_options' ) ){
			wp_die( 'You do not have sufficient permissions to access this page' );
		}

		require( 'inc/downloads.php' );

	}

	function script_enqueuer() {

		if(isset($_GET['page'])){

	    	if( $_GET['page'] == 'campaigns' || $_GET['page'] == 'single-campaign' || $_GET['page'] == 'single-outcome' ){

	    		wp_register_script( "campaign_script", $this->plugin_url . '/js/campaigns.js', array('jquery') );
				wp_localize_script( "campaign_script", 'cAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));    
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'campaign_script' );

	    	}
	    	
	    }

	}

	function campaigns_styles(){

		wp_enqueue_style( 'quill', 'https://cdn.quilljs.com/1.2.4/quill.snow.css');
		wp_enqueue_style( 'campaigns_styles', $this->plugin_url . '/campaigns_style.css');

	}

	function front_end_cb_styles() {

	    wp_enqueue_style( 'frontend_styles', $this->plugin_url . '/css/front.css'); 

	    wp_register_script( "slideshow", $this->plugin_url . '/js/slideshow.js', array('jquery') );
	    wp_localize_script( "slideshow", 'frontAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));  
	    wp_enqueue_script( 'slideshow' );

	    // wp_register_script( "tracking", $this->plugin_url . '/js/tracking.js', array('jquery') );
	    // wp_localize_script( "tracking", 'frontAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));  
	    // wp_enqueue_script( 'tracking' );

	    // wp_register_script( "slideshow", $this->plugin_url . '/js/slideshow.js', array('jquery') );
	    // wp_enqueue_script( 'slideshow' );
	
	}

	function enqueue_media_uploader($hook){

	    wp_enqueue_media();

		if (  $hook == 'post-new.php' || $hook == 'post.php' ){

		    wp_register_script( "sortable",  '//code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery') );
		    wp_enqueue_script( 'sortable' );

		    wp_register_script( "sixom_editor", 'https://cdn.quilljs.com/1.2.4/quill.js', array('jquery') );
		    wp_enqueue_script( 'sixom_editor' );

		    wp_register_script( "content_block", $this->plugin_url . '/js/content_block.js', array('jquery') );
		    wp_localize_script( "content_block", 'cblocksAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));  
		    wp_enqueue_script( 'content_block' );

		    wp_register_script( "cbhelpers", $this->plugin_url . '/js/cbhelpers.js', array('jquery', 'content_block') );
		    wp_localize_script( "cbhelpers", 'cblocksAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))); 
		    wp_enqueue_script( 'cbhelpers');

		    wp_enqueue_script( 'app', $this->plugin_url . '/js/app.js', array('jquery', 'content_block', 'cbhelpers'));

	    }

	    if( $hook == 'widgets.php' ){

	    	wp_register_script( "adwidget", $this->plugin_url . '/js/adwidget.js', array('jquery'));
		    wp_localize_script( "adwidget", 'adwidgetAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
		    wp_enqueue_script( "adwidget" );

	    }

	    if(isset($_GET['page'])){

	    	if( $_GET['page'] == 'funnel_stats' ){
		    	wp_register_script( "stats_script", $this->plugin_url .'/js/stats.js', array('jquery') );
				wp_localize_script( "stats_script", 'statsAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))); 
				wp_enqueue_script( 'stats_script' );
		    }

		    if( $_GET['page'] == 'excluded_ips' ){
		    	wp_register_script( "ip_script", $this->plugin_url . '/js/excluded_ips.js', array('jquery') );
				wp_localize_script( "ip_script", 'ipAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))); 
				wp_enqueue_script( 'ip_script' );
		    }

		    if( $_GET['page'] == 'downloads' ){

		    	wp_register_script( "sixom_editor", 'https://cdn.quilljs.com/1.2.4/quill.js', array('jquery') );
		    	wp_enqueue_script( 'sixom_editor' );

		    	wp_register_script( "downloads", $this->plugin_url . '/js/downloads.js', array('jquery') );
				wp_localize_script( "downloads", 'downloadsAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))); 
				wp_enqueue_script( 'downloads' );

		    }
		    
	    }

	}

	function register_ad_widget() {
		require('classes/adwidget.php');
		register_widget( 'ProjectXAdWidget' );
	}

	function get_outcomes(){

		$campaign_id = $_POST['campaign'];

		global $wpdb;

		$outcomes = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'outcomes WHERE campaign_id=' . $campaign_id);

		$response['outcomes'] = $outcomes;
		echo json_encode($response);
		wp_die();

	}

	function get_ads(){

		$outcome_id = $_POST['outcome'];

		global $wpdb;

		$ads = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'px_adverts WHERE outcome_id=' . $outcome_id);

		$response['ads'] = $ads;
		echo json_encode($response);
		wp_die();

	}

	function __construct($plugin_url){

		$this->plugin_url = $plugin_url;

		register_activation_hook( __FILE__, array($this, 'setup_database_tables') );
		add_action( 'admin_menu', array($this, 'add_plugin_menu') );
		add_action( 'init', array($this, 'script_enqueuer') );
		add_action( 'admin_head', array($this, 'campaigns_styles') );
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_media_uploader') );
		add_action( 'wp_enqueue_scripts', array($this, 'front_end_cb_styles') );
		add_action( 'widgets_init', array($this, 'register_ad_widget') );
		add_action( 'wp_ajax_get_ads', array($this, 'get_ads') );
		add_action( 'wp_ajax_nopriv_get_ads', array($this, 'get_ads' ) );
		add_action( 'wp_ajax_get_outcomes', array($this, 'get_outcomes') );
		add_action( 'wp_ajax_nopriv_get_outcomes', array($this, 'get_outcomes' ) );

		$projectx_page = new ProjectXPost();
		$content_blocks = new ContentBlocks();
		$campaigns = new Campaigns();
		$this->downloadManager = new Downloads();
		$trackingHelp = new TrackingHelpers();
		$exIps = new ExcludedIps($trackingHelp);
		$tracker = new Tracker($trackingHelp);
		$funnelStats = new FunnelStats();

	}

}

$projectx = new ProjectX($plugin_url);

?>