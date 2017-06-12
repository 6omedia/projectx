<?php

	class TrackingHelpers {

		function px_find_user($ip){

			global $wpdb;
			$user = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "px_users WHERE ip = '$ip';");

			if(!empty($user)){
				return $user[0]->id;
			}else{
				return 'none';
			}

		}

		function px_find_page($page_url){

			global $wpdb;
			$page = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "px_stats WHERE page_url='$page_url';");

			if(!empty($page)){
				return $page[0]->id;
			}else{
				return 'none';
			}

		}

	}

?>