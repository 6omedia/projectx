<?php

	class ExcludedIps {

		function px_add_excluded_ip(){

			$ip = $_POST['ip'];
			$description = $_POST['description'];
			$response['success'] = 0;
			$response['user_exists'] = 0;

			$user = $this->trackingHelp->px_find_user($ip); 

			global $wpdb;

			if($user != 'none'){

				// block user
				$wpdb->update(
					$wpdb->prefix . 'px_users',
					array(
						'blocked' => 1,
						'description' => $description
					),
					array(
						'id' => $user
					)
				);

				$response['user_exists'] = 1;

			}else{

				// add user and blockem
				$wpdb->insert($wpdb->prefix . 'px_users', array(
				    'ip' => $ip,
				    'description' => $description,
				    'blocked' => 1
				));

			}

			$response['success'] = 1;
			echo json_encode($response);
		    die();

		}

		function px_remove_excluded_ip(){

			$ipid = $_POST['ipid'];
			$response['success'] = 0;

			global $wpdb;
			$wpdb->update(
				$wpdb->prefix . 'px_users',
				array(
					'blocked' => 0
				),
				array(
					'id' => $ipid
				)
			);

			$response['success'] = 1;
			echo json_encode($response);
		    die();

		}

		function px_get_excluded_ip(){

			$response['success'] = 0;

			global $wpdb;
			$exips = $wpdb->query( "SELECT * FROM " . $wpdb->prefix . "px_excluded_ips;" );

			foreach ($exips as $ip) {
					
			}

			$response['exips'] = $exips;
			$response['success'] = 1;
			echo json_encode($response);
		    die();

		}

		function __construct($trackingHelp){

			$this->trackingHelp = $trackingHelp;

			add_action( 'wp_ajax_px_add_excluded_ip', array($this, 'px_add_excluded_ip') );
			add_action( 'wp_ajax_nopriv_px_add_excluded_ip', array($this, 'px_add_excluded_ip') );
			add_action( 'wp_ajax_px_remove_excluded_ip', array($this, 'px_remove_excluded_ip') );
			add_action( 'wp_ajax_nopriv_px_remove_excluded_ip', array($this, 'px_remove_excluded_ip') );
			add_action( 'wp_ajax_px_get_excluded_ip', array($this, 'px_get_excluded_ip') );
			add_action( 'wp_ajax_nopriv_px_get_excluded_ip', array($this, 'px_get_excluded_ip') );

		}

	}

?>