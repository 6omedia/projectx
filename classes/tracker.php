<?php
	
	class Tracker {

		function px_enque_tracking(){

			if(is_single()){
				wp_register_script( "tracking_script", WP_PLUGIN_URL .'/projectx/js/tracking.js', array('jquery') );
				wp_localize_script( "tracking_script", 'trackAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))); 
				wp_enqueue_script( 'tracking_script' );
			}

		}

		function px_track_page(){

			$page_url = $_POST['page_url'];
			$ip = $_POST['ip'];
			$title = $_POST['title'];
			$outcomeid = $_POST['outcomeid'];
			$campaignid = $_POST['campaignid'];
			$post_id = $_POST['px_post_id'];
			$funnel_position = $_POST['funnel_position'];
			$time = date("Y-m-d H:i:s");

			global $wpdb;

			$user = $this->trackingHelp->px_find_user($ip); 
			$page = $this->trackingHelp->px_find_page($page_url);

			if($user != 'none'){

				$inserted = $wpdb->insert($wpdb->prefix . 'px_page_visits', array(
				    'user_id' => $user,
				    'page_url' => $page_url,
				    'time_viewed' => $time,
				    'outcome_id' => $outcomeid
				));

				if($inserted){
					if($page != 'none'){
						// update
						$wpdb->query("UPDATE " . $wpdb->prefix . "px_stats SET page_views = page_views+1 WHERE id=$page");			
						$wpdb->query("UPDATE " . $wpdb->prefix . "px_users SET page_views = page_views+1 WHERE id=$user");		

					}else{
						// insert
						$wpdb->insert($wpdb->prefix . 'px_stats', array(
						    'page_url' => $page_url,
						    'page_title' => $title,
						    'page_views' => 1,
						    'post_id' => $post_id,
						 	'funnel_position' => $funnel_position,
						 	'campaign_id' => $campaignid,
						 	'outcome_id' => $outcomeid
						));
					}
				}

			}else{

				// create user
				$wpdb->insert($wpdb->prefix . 'px_users', array(
				    'ip' => $ip
				));

				$lastid = $wpdb->insert_id;

				// insert pageview
				$inserted = $wpdb->insert($wpdb->prefix . 'px_page_visits', array(
				    'user_id' => $lastid,
				    'page_url' => $page_url,
				    'time_viewed' => $time,
				    'outcome_id' => $outcomeid
				));

				// update stats
				if($inserted){
					if($page != 'none'){
						// update
						$wpdb->query("UPDATE " . $wpdb->prefix . "px_stats SET page_views = page_views+1 WHERE id=$page");	
						$wpdb->query("UPDATE " . $wpdb->prefix . "px_users SET page_views = page_views+1 WHERE id=$user");			

					}else{
						// insert
						$wpdb->insert($wpdb->prefix . 'px_stats', array(
						    'page_url' => $page_url,
						    'page_title' => $title,
						    'page_views' => 1,
						    'post_id' => $post_id,
						    'funnel_position' => $funnel_position,
						    'campaign_id' => $campaignid,
						 	'outcome_id' => $outcomeid
						));
					}
				}

			}

			$response['success'] = 'Yeah all is good!'; // 1;
			echo json_encode($response);
		    die();

		}
		
		function px_track_click(){

			$post_id = $_POST['px_post_id'];
			$advert_id = $_POST['advert_id'];
			$page_url = $_POST['page_url'];
			$ip = $_POST['ip'];

			// $user_id = '';

			global $wpdb;

			$user_id = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "px_users WHERE ip='$ip';");

			// error_log('IP: ' . $ip);
			// error_log('User Id: ' . $user_id);

			$inserted = $wpdb->insert($wpdb->prefix . 'px_ad_clicks', array(
			    'user_id' => $user_id[0]->id,
			    'page_url' => $page_url,
			    'advert_id' => $advert_id,
			    'outcome_id' => '',
			    'post_id' => $post_id
			));

			if($inserted){
				$wpdb->query("UPDATE " . $wpdb->prefix . "px_stats SET clicks = clicks+1 WHERE page_url='$page_url'");
			}

			$response['success'] = 1;
			echo json_encode($response);
		    die();

		}

		function px_tracking_info(){

			$curr_page = $_SERVER['REQUEST_URI']; // $_SERVER['PHP_SELF'];// get page name
			$ip  =  $_SERVER['REMOTE_ADDR'];   // get the IP address
			$page_title = get_the_title(get_the_ID()); //get current page
			$px_post_id = get_the_ID(); 
			$campaignid = get_post_meta(get_the_ID(), 'px_campaign');

			if(!empty($campaignid)){
				$campaignid = explode('###', $campaignid[0]);
			}else
			{
				$campaignid[0] = ''; 
			}
			
			$outcomeid = get_post_meta(get_the_ID(), 'px_outcome');

			if(!empty($outcomeid)){
				$outcomeid = explode('###', $outcomeid[0]);
			}else{
				$outcomeid[0] = '';
			}

			$funnel_position = get_post_meta(get_the_ID(), 'funnel_position');

			echo '<div id="tracking_info" style="display: none;"';
			echo ' data-curr_page="' . $curr_page . '"';
			echo ' data-ip="' . $ip . '"';
			echo ' data-page_title="' . $page_title . '"';
			echo ' data-outcomeid="' . $outcomeid[0] . '"';
			echo ' data-px_post_id="' . $px_post_id . '"';
			echo ' data-campaignid="' . $campaignid[0] . '"';
			echo ' data-funnel_position="' . $funnel_position[0] . '"';
			echo '></div>';

		}

		function __construct($trackingHelp){

			$this->trackingHelp = $trackingHelp;

			add_action( 'wp_enqueue_scripts', array($this, 'px_enque_tracking') );
			add_action( 'wp_ajax_px_track_page', array($this, 'px_track_page') );
			add_action( 'wp_ajax_nopriv_px_track_page', array($this, 'px_track_page') );
			add_action( 'wp_ajax_px_track_click', array($this, 'px_track_click') );
			add_action( 'wp_ajax_nopriv_px_track_click', array($this, 'px_track_click') );
			add_action( 'wp_footer', array($this, 'px_tracking_info') );

		}

	}

?>