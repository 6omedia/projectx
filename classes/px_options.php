<?php

	class PxOptions {

		private $cb_post_types = [];
		private $campaigns_post_types = [];

		function get_cb_post_types(){
			return $this->cb_post_types;
		}

		function get_campaigns_post_types(){
			return $this->campaigns_post_types;
		}

		function __construct(){

			$options = get_option('px_options');

			if($options != ''){
				
				if(isset($options['px_cb_post_types'])){
					if($options['px_cb_post_types'] != ''){
						$this->cb_post_types = $options['px_cb_post_types'];
					}
				}

				if(isset($options['px_campaigns_post_types'])){
					if($options['px_campaigns_post_types'] != ''){
						$this->campaigns_post_types = $options['px_campaigns_post_types'];
					}
				}

			}

		}

	}

?>