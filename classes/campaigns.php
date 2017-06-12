<?php

	class Campaigns {

		function create_campaign(){

			$title = $_POST['campaign_title'];
			$desc = $_POST['description'];
			$outcomes = str_replace('&quot;', '"', $_POST['outcomes']);
			$outcomes = json_decode(stripslashes($outcomes));

			global $wpdb;
			$result = $wpdb->insert($wpdb->prefix . 'campaigns', array(
			    'title' => $title,
			    'description' => $desc
			));

			$cId = $wpdb->insert_id;

			// inset outcomes

			foreach($outcomes as $outcome){

				global $wpdb;
				$wpdb->insert($wpdb->prefix . 'outcomes', array(
				    'campaign_id' => $cId,
				    'title' => $outcome
				));

			}

			$response['campaignid'] = $cId;
		    $response['success'] = $result;
		    echo json_encode($response);

		    die();

		}

		function update_campaign(){

			$id = $_POST['campaign_id'];
			$title = $_POST['campaign_title']; 
			$desc = $_POST['description'];

			$outcomesToAdd = str_replace('&quot;', '"', $_POST['outcomesToAdd']);
			$outcomesToAdd = json_decode(stripslashes($outcomesToAdd));

			$outcomesToDelete = str_replace('&quot;', '"', $_POST['outcomesToDelete']);
			$outcomesToDelete = json_decode(stripslashes($outcomesToDelete));

			// update campaign
			global $wpdb;
			$wpdb->update(
				$wpdb->prefix . 'campaigns',
				array(
					'title' => $title,
					'description' => $desc
				),
				array(
					'id' => $id
				)
			);

			// delete outcomes
			foreach ($outcomesToDelete as $outcome) {
				if($outcome != null){
					$wpdb->delete( $wpdb->prefix . 'outcomes', array( 'id' => $outcome ) );
			   	}
			}

			// add outcomes
			foreach($outcomesToAdd as $outcome) {

				global $wpdb;
				$wpdb->insert($wpdb->prefix . 'outcomes', array(
				    'campaign_id' => $id,
				    'title' => $outcome
				));

			}

			$response['success'] = 1;
			echo json_encode($response);
		    die();

		}

		function delete_campaign(){

			global $wpdb; 
			$wpdb->delete( $wpdb->prefix . 'campaigns', array( 'id' => $_POST['campaign_id'] ) );
			$wpdb->delete( $wpdb->prefix . 'outcomes', array( 'campaign_id' => $_POST['campaign_id'] ) );

			$response['success'] = 1;
			echo json_encode($response);
		    die();

		}

		function set_ads(){

			$outcome_title = $_POST['outcome_title'];

			$advertsToAdd = str_replace('&quot;', '"', $_POST['advertsToAdd']);
			$advertsToAdd = json_decode(stripslashes($advertsToAdd));

			$advertsToDelete = str_replace('&quot;', '"', $_POST['advertsToDelete']);
			$advertsToDelete = json_decode(stripslashes($advertsToDelete));

			$outcomeid = $_POST['outcome_id'];
			$top_ad = $_POST['top_ad'];
			$side_ad = $_POST['side_ad'];
			$bottom_ad = $_POST['bottom_ad'];
			$funnel_position = $_POST['funnel_position'];
			$top_link = $_POST['top_link'];
			$side_link = $_POST['side_link'];
			$bottom_link = $_POST['bottom_link'];

			global $wpdb;
			$wpdb->update(
				$wpdb->prefix . 'outcomes',
				array(
					'title' => $outcome_title
				),
				array(
					'id' => $outcomeid
				)
			);

			// Check if ad has been set

			$adId = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "px_adverts WHERE outcome_id = '$outcomeid' AND funnel_position = '$funnel_position';");

			if(empty($adId)){

				global $wpdb;
				$wpdb->insert( $wpdb->prefix . 'px_adverts', array(
				    'outcome_id' => $outcomeid,
				    'top_ad' => $top_ad,
				    'side_ad' => $side_ad,
				    'bottom_ad' => $bottom_ad,
				    'funnel_position' => $funnel_position,
					'top_link' => $top_link,
				    'side_link' => $side_link,
				    'bottom_link' => $bottom_link
				));

			}else{

				// update
				global $wpdb;
				$wpdb->update(
					$wpdb->prefix . 'px_adverts',
					array(
						'top_ad' => $top_ad,
					    'side_ad' => $side_ad,
					    'bottom_ad' => $bottom_ad,
					    'funnel_position' => $funnel_position,
					    'top_link' => $top_link,
					    'side_link' => $side_link,
					    'bottom_link' => $bottom_link
					),
					array(
						'id' => $adId[0]->id
					)
				);

			}

			// delete adverts
			foreach ($advertsToDelete as $advert) {

				if($advert != null){
					$wpdb->delete( $wpdb->prefix . 'px_content_adverts', array( 'id' => $advert ) );
			   	}
			
			}

			// add adverts
			foreach($advertsToAdd as $advert) {

				$advert = explode('###', $advert);

				global $wpdb;
				$wpdb->insert($wpdb->prefix . 'px_content_adverts', array(
				    'outcome_id' => $outcomeid,
				    'ad_link' => $advert[1],
				    'ad_img' => $advert[0],
				    'funnel_position' => $funnel_position,
				    'title' => $advert[2]
				));
			}

			$response['adid'] = $adId;
			$response['success'] = 1;
			echo json_encode($response);
		    die();

		}

		function px_get_outcomes(){

			$campaignid = $_POST['campaignid'];

			global $wpdb;
			$outcomes = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "outcomes WHERE campaign_id='$campaignid';");

			$response['outcomes'] = $outcomes;
			$response['success'] = 1;
			echo json_encode($response);
		    die();

		}

		function __construct(){

			add_action( 'wp_ajax_create_campaign', array($this, 'create_campaign') );
			add_action( 'wp_ajax_nopriv_post_create_campaign', array($this, 'create_campaign' ) );
			add_action( 'wp_ajax_update_campaign', array($this, 'update_campaign'));
			add_action( 'wp_ajax_nopriv_post_update_campaign', array($this, 'update_campaign' ));
			add_action( 'wp_ajax_delete_campaign', array($this, 'delete_campaign'));
			add_action( 'wp_ajax_nopriv_post_delete_campaign', array($this, 'delete_campaign' ));
			add_action( 'wp_ajax_set_ads', array($this, 'set_ads') );
			add_action( 'wp_ajax_nopriv_set_ads', array($this, 'set_ads') );
			add_action( 'wp_ajax_px_get_outcomes', array($this, 'px_get_outcomes') );
			add_action( 'wp_ajax_nopriv_px_get_outcomes', array($this, 'px_get_outcomes') );

		}

	}

?>