<?php

	class PostContentBlocks {

		function output_title($block){
			return '<h1>' . stripslashes(json_decode($block)) . '</h1>';
		}

		function output_h2($block){
			return '<h2>' . stripslashes(json_decode($block)) . '</h2>'; 
		}

		function output_h3($block){
			return '<h3>' . stripslashes(json_decode($block)) . '</h3>';
		}

		function output_paragraph($block){ 
			return stripslashes(json_decode($block));
		}

		function output_quote($block){ 
			return '<blockquote>' . json_decode($block) . '<blockquote>';
		}

		function output_hr($block){
			return '<hr>';
		}

		function output_link($block){
			$blockArray = json_decode($block);
			return '<a href="' . $blockArray->link_url . '">' . $blockArray->link_text . '</a>';
		}

		function output_preface($block){

			$blockArray = json_decode($block);
			$title = $blockArray->title;
			$content = $blockArray->preface_content;
			$img_src = $blockArray->img_src;
			$img_alt = $blockArray->img_alt;
			
			$preface = '<h2>' . $title . '</h2>';
			$preface .=  $content;
			$preface .= '<img src="' . $img_src . '" alt="' . $img_alt . '">';

			return $preface;

		}

		function output_image($block){
			
			$blockArray = json_decode($block);
			$img_src = $blockArray->img_src;
			$img_alt = $blockArray->img_alt;

			return '<img src="' . $img_src . '" alt="' . $img_alt . '">';

		}

		function output_embed($block){
			return stripslashes(json_decode($block));
		}

		function output_gallery($block){
			
			$galleryArray = json_decode($block);

			$gallery = '<ul class="cb_gallery cols3 cb_gallery_style1">';

			foreach ($galleryArray as $img) {

				$imgArray = explode('###', $img);

				$gallery .= '<li><img src="' . $imgArray[0] . '"alt="' . $imgArray[1] . '"></li>';

			}

			$gallery .= '</ul>';

			return $gallery;

		}

		function output_video($block){

			$wpVideo = wp_video_shortcode(
				array(
					'src' => json_decode($block) 
				)
			);

			return $wpVideo;
			
		}

		function output_advert($block){ 

			global $wpdb;

			$adId = json_decode($block);
			$advert = '';

			if($adId != ''){
				$advert = $wpdb->get_results("SELECT id, ad_link, ad_img FROM " . $wpdb->prefix . "px_content_adverts WHERE id=" . $adId);
			}

			if($advert != ''){

				$ad = '<a class="ad_track"';
			 	$ad .= ' data-post_id="' . get_the_ID() . '"';
			 	$ad .= ' data-page_url="' . $_SERVER['REQUEST_URI'] . '"';
			 	$ad .= ' data-ip="' . $_SERVER['REMOTE_ADDR'] . '"';
			 	$ad .= ' data-advert_id="' . $advert[0]->id . '"';
			 	$ad .= ' href="' . $advert[0]->ad_link . '"><img src="' . $advert[0]->ad_img .'">';
			 	$ad .= '</a>';

				return $ad;
			}

			return '';

		}

		function output_slideshow($block){ 

			$jsonArray = json_decode(stripslashes($block));

			$slideShow = '<div class="px_slideShow">';
				$slideShow .= '<div class="prev"></div>';

				foreach ($jsonArray as $slide) {

					$slide = json_decode($slide); 

					$slideShow .= '<div class="pxSlide">';
						$slideShow .= '<img src="' . $slide->imgSrc . '" alt="' . $slide->imgAlt . '">'; 
						$slideShow .= '<div>';
							$slideShow .= $slide->content;
						$slideShow .= '</div>';
					$slideShow .= '</div>';

				}

				$slideShow .= '<div class="next"></div>';
			$slideShow .= '</div>';

			return $slideShow;
			
		}

		function output_rating($block){
			
			$rating = json_decode($block);

			$theRating = '<div class="px_rating">';
				$theRating .= '<meta itemprop="worstRating" content="0">';
				$theRating .= '<meta itemprop="ratingValue" content="' . $rating . '">';
				$theRating .= '<meta itemprop="bestRating" content="10">';
				$theRating .= '<ul class="starList">';

			  		for($i=1; $i<6; $i++){
			  			if($i > $rating){
			  				$theRating .= '<li style="filter: grayscale(100%)"></li>';
			  			}else{
			  				$theRating .= '<li></li>';
			  			}
			  		}

	  			$theRating .= '</ul>';
  			$theRating .= '</div>';

  			return $theRating;

		}

		function allowDownload($file_url){

			if (file_exists($file_url)) {

			   header('Content-Description: File Transfer');
			   header('Content-Type: application/octet-stream');
			   header('Content-Disposition: attachment; filename='.basename($file_url));
			   header('Expires: 0');
			   header('Cache-Control: must-revalidate');
			   header('Pragma: public');
			   header('Content-Length: ' . filesize($file_url));
			   ob_clean();
			   flush();
			   readfile($file_url);

			   return true;
			
			}

		}

		function output_download($block){
			
			global $wpdb;

			$downloadObj = json_decode($block);
			$downloaded = false;

			$downloadId = $downloadObj->download;
			$emailRequired = '';

			if(property_exists($downloadObj, 'email_required'))
	        	$emailRequired = 'checked';

			$download = '';

			if($downloadId != ''){
				$download = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_downloads WHERE id='" . $downloadId . "'");
			}

			if(isset($_POST['downloademail'])){

				$email = $_POST['downloademail']; 

				$emailFromDb = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "px_email_list WHERE email='" . $email . "'");

				if(empty($emailFromDb)){

					$wpdb->insert($wpdb->prefix . 'px_email_list', array(
	  					'email' => $email,
	  					'download_id' => $downloadId
					));

				}

				// todo: ad to user emails in px_users

				$filename = $download[0]->filename;
				$file_url = getcwd() . '/wp-content/plugins/projectx/downloads/' . $filename;

				$download = $this->allowDownload($file_url);

			}

			if($download != ''){
				
				$dw = '<div class="px_download">';
					
					$dw .= '<h3>' . $downloadObj->heading . '</h3>';
					$dw .= '<div class="pxbtn pxdownloadbtn" data-emailreq="' . $emailRequired . '">Download</div>';

					$dw .= '<form action="" method="post" class="form">';
						$dw .= '<input type="email" name="downloademail" class="px_email">';
						$dw .= '<button class="pxbtn pxenteremail" name="downloadid" value="' . $downloadId . '" data-downloadid="' . $downloadId . '">Enter</button>';
						$dw .= '<div class="px_spin"></div>';
					$dw .= '</form>';

					$dw .= '<div class="px_thanks">Thankyou, your download has started</div>';

					$dw .= '<p>Our blah de blah blah in accrodance with blah blahs <div class="px_spin"></div><span class="px_pp">privacy policy</span></p>';

				$dw .= '</div>';

				return $dw;

			}

		}

	}


?>