<?php

	class FunnelStats {

		function px_get_stats(){

			$campaignid = $_POST['campaignid'];
			$outcomeid = $_POST['outcomeid'];
			$funnel_position = $_POST['funnel_position'];
			$filter_type = $_POST['filter_type'];

			$pages = '';

			$page_views = [];
		 	$page_clicks = [];

			global $wpdb;

			// get blocked user page views
			$results = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_users WHERE blocked=0;");
			$users = '(';
			$resCount = count($results);
			foreach ($results as $result) {	
				if(0 === --$resCount){
					$users .= $result->id;
				}else{
					$users .= $result->id . ', ';
				}	
			}
			$users .= ')';

			if($outcomeid == ''){

				if($funnel_position == ''){
					$pages = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_stats WHERE campaign_id='$campaignid';");
				}else{
					$pages = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_stats WHERE campaign_id='$campaignid' AND funnel_position='$funnel_position';");
				}

			}else{

				if($funnel_position == ''){
					$pages = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_stats WHERE outcome_id='$outcomeid';");
				}else{
					$pages = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_stats WHERE outcome_id='$outcomeid' AND funnel_position='$funnel_position';");
				}

			}
					
			foreach ($pages as $page) {
				$page_views[] = $wpdb->get_results("SELECT COUNT(*) AS page_views FROM " . $wpdb->prefix . "px_page_visits WHERE page_url = '$page->page_url' AND user_id IN " . $users);
				$page_clicks[] = $wpdb->get_results("SELECT COUNT(*) AS page_clicks FROM " . $wpdb->prefix . "px_ad_clicks WHERE page_url = '$page->page_url' AND user_id IN " . $users);
			}
				
			$response['page_clicks'] = $page_clicks;
			$response['page_views'] = $page_views;
			$response['users'] = $users;
			$response['pages'] = $pages;
			$response['success'] = 1;
			echo json_encode($response);
		    die();

		}

		function __construct(){

			add_action( 'wp_ajax_px_get_stats', array($this, 'px_get_stats') );
			add_action( 'wp_ajax_nopriv_px_get_stats', array($this, 'px_get_stats') );

		}

	}

?>