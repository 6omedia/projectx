<?php

	class PostContentBlocks {

		function output_title($block){
			echo '<h1>' . json_decode($block) . '</h1>';
		}

		function output_h2($block){
			echo '<h2>' . json_decode($block) . '</h2>'; 
		}

		function output_h3($block){
			echo '<h3>' . json_decode($block) . '</h3>';
		}

		function output_paragraph($block){ 
			echo stripslashes(json_decode($block));
		}

		function output_quote($block){ 
			echo '<blockquote>' . json_decode($block) . '<blockquote>';
		}

		function output_hr($block){
			echo '<hr>';
		}

		function output_link($block){
			$blockArray = json_decode($block);
			echo '<a href="' . $blockArray->link_url . '">' . $blockArray->link_text . '</a>';
		}

		function output_preface($block){

			$blockArray = json_decode($block);
			$title = $blockArray->title;
			$content = $blockArray->preface_content;
			$img_src = $blockArray->img_src;
			$img_alt = $blockArray->img_alt;
			
			echo '<h2>' . $title . '</h2>';
			echo $content;
			echo '<img src="' . $img_src . '" alt="' . $img_alt . '">';

		}

		function output_image($block){
			
			$blockArray = json_decode($block);
			$img_src = $blockArray->img_src;
			$img_alt = $blockArray->img_alt;

			echo '<img src="' . $img_src . '" alt="' . $img_alt . '">';

		}

		function output_embed($block){
			echo stripslashes(json_decode($block));
		}

		function output_gallery($block){
			
			$galleryArray = json_decode($block);

			?>

			<ul class="cb_gallery cols3 cb_gallery_style1">

			<?php foreach ($galleryArray as $img) {

				$imgArray = explode('###', $img);

				?>

				<li><img src="<?php echo $imgArray[0]; ?>" alt="<?php echo $imgArray[1]; ?>"></li>

			<?php } ?>

			</ul>

		<?php }

		function output_video($block){

			$wpVideo = wp_video_shortcode(
				array(
					'src' => json_decode($block) 
				)
			);

			echo $wpVideo;
			
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

				echo $ad;
			}

		}

		function output_slideshow($block){ 

			$jsonArray = json_decode(stripslashes($block));

			?>

			<div class="px_slideShow">
				<div class="prev"></div>

				<?php foreach ($jsonArray as $slide) { ?>

					<?php $slide = json_decode($slide); ?> 

					<div class="pxSlide">
						<img src="<?php echo $slide->imgSrc; ?>" alt="<?php echo 'dfvcs';// $slide->imgAlt; ?>"> 
						<div>
							<?php echo $slide->content; ?>
						</div>
					</div>

				<?php } ?>

				<div class="next"></div>
			</div>
			
		<?php }

		function output_rating($block){
			
			$rating = json_decode($block);

			echo '<div class="px_rating">';
				echo '<meta itemprop="worstRating" content="0">';
				echo '<meta itemprop="ratingValue" content="' . $rating . '">';
				echo '<meta itemprop="bestRating" content="10">';
				echo '<ul class="starList">';

			  		for($i=1; $i<6; $i++){
			  			if($i > $rating){
			  				echo '<li style="filter: grayscale(100%)"></li>';
			  			}else{
			  				echo '<li></li>';
			  			}
			  		}

	  			echo '</ul>';
  			echo '</div>';

		}

		function output_download($block){
			
			global $wpdb;

			$downloadObj = json_decode($block);
			$downloaded = false;

			$downloadId = $downloadObj->download;
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

					// update 

					// $result = mysql_query("UPDATE " . $wpdb->prefix . "'px_downloads' SET captured_emails = captured_emails + 1 WHERE id = '" . $downloadId . "'");

					// $captured_emails = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "px_downloads WHERE id='" . $downloadId . "'");

					// $wpdb->update(
					// 	$wpdb->prefix . 'px_downloads',
					// 	array(
					// 		'captured_emails' => intval($captured_emails) + 1
					// 	),
					// 	array(
					// 		'id' => $downloadId
					// 	)
					// );

				}

				// todo: ad to user emails in px_users

				$filename = $download[0]->filename;
				$file_url = getcwd() . '/wp-content/plugins/projectx/downloads/' . $filename;

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

				   $downloaded = true;
				
				}

			}

			if($download != ''){ ?>
				
				<div class="px_download">
					
					<h3><?php echo $downloadObj->heading; ?></h3>
					<div class="pxbtn pxdownloadbtn">Download</div>

					<form action="" method="post" class="form">
						<input type="email" name="downloademail" class="px_email">
						<button class="pxbtn pxenteremail" name="downloadid" value="<?php echo $downloadId; ?>" data-downloadid="<?php echo $downloadId; ?>">Enter</button>
						<div class="px_spin"></div>
					</form>

					<div class="px_thanks">Thankyou, your download has started</div>

					<p>Our blah de blah blah in accrodance with blah blahs <div class="px_spin"></div><span class="px_pp">privacy policy</span></p>

				</div>

			<?php }

		}

	}


?>