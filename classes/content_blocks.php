<?php

	// include content block class

	include('px_options.php');

	class ContentBlocks {

		function add_content_block_buttons(){

			global $post;

			$options = new PxOptions();
			$postTypes = $options->get_cb_post_types();

			if(!in_array($post->post_type, $postTypes))
				return;

			add_meta_box('content_blocks_box', 'Content Blocks', array($this, 'cb_meta_box'), null, 'advanced');
			
		}

		function cb_meta_box(){ 

			$post_id = get_the_ID();

			global $wpdb;
			$content_blocks = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_content_blocks WHERE post_id = $post_id ORDER BY id;");

			?>

			<ul id="content_blocks">
				
				<?php

					$index = 0;
					foreach ($content_blocks as $block) {
						$this->outputContentBlock($block, $index);
						$index++;
					}

				?>

			</ul>

			<div class="cb_menublock">
				<h3>Text Format</h3>
				<ul class="contentblock_menu" id="cb_btns_format">
					<li><div data-blocktype="title" id="title_cb"></div><p>Title</p></li>
					<li><div data-blocktype="h2" id="h2_cb"></div><p>H2</p></li>
					<li><div data-blocktype="h3" id="h3_cb"></div><p>H3</p></li>
					<li><div data-blocktype="paragraph" id="paragraph_cb"></div><p>Paragraph</p></li>
					<li><div data-blocktype="quote" id="quote_cb"></div><p>Quote</p></li>
					<li><div data-blocktype="hr" id="hr_cb"></div><p>Horizontal Rule</p></li>
					<li><div data-blocktype="link" id="link_cb"></div><p>Link</p></li>
				</ul>
			</div>
			<div class="cb_menublock">
				<h3>Media</h3>
				<ul class="contentblock_menu" id="cb_btns_media">
					<li><div data-blocktype="preface" id="preface_cb"></div><p>Preface</p></li>
					<li><div data-blocktype="image" id="img_cb"></div><p>Image</p></li>
					<li><div data-blocktype="embed" id="embed_cb"></div><p>Code Embed</p></li>
					<li><div data-blocktype="gallery" id="gallery_cb"></div><p>Gallery</p></li>
					<li><div data-blocktype="video" id="video_cb"></div><p>Video</p></li>
					<li><div data-blocktype="advert" id="advert_cb"></div><p>Advert</p></li>
					<li><div data-blocktype="slideshow" id="slideshow_cb"></div><p>Slide Show</p></li>
					<li><div data-blocktype="rating" id="rating_cb"></div><p>Rating</p></li>
					<li><div data-blocktype="download" id="download_cb"></div><p>Download</p></li>
				</ul>
			</div>

		<?php }

		function save_content_blocks( $post_id, $post ){

			$post_type = get_post_type_object( $post->post_type );

			/* Check if the current user has permission to edit the post. */
		  	if ( !current_user_can( $post_type->cap->edit_post, get_the_ID() ) )
		    	return get_the_ID();

		    global $wpdb; 
			$wpdb->delete( $wpdb->prefix . 'px_content_blocks', array( 'post_id' => $post_id ) );

			$size = sizeof($_POST['px_blocks']);

			$cBlocks = $_POST['px_blocks'];

			foreach ($cBlocks as $block) {
				
				$wpdb->insert($wpdb->prefix . 'px_content_blocks', array(
				    'post_id' => $post_id,
				    'block_content' => json_encode($block['content']),
				    'list_position' => 1,
				    'block_type' => $block['blocktype'],
				    'block_color' => $block['blockcolor']
				));

			}

		}

		function outputContentBlock($block, $index){ ?>

			<li>
				<div class="content_block <?php echo $block->block_type; ?>" style="background: linear-gradient(#fff 97px, <?php echo $block->block_color; ?> 97px);">
					<div class="cb_header">
						<p class="cb_head_p"><?php echo $block->block_type; ?></p>
						<span class="minus-span"></span>
						<input type="color" value="<?php echo $block->block_color; ?>" name="px_blocks[<?php echo $index; ?>][blockcolor]">
					</div>

					<input type="hidden" name="px_blocks[<?php echo $index; ?>][blocktype]" value="<?php echo $block->block_type; ?>">

					<?php

						if(method_exists($this, 'output_' . $block->block_type))
							call_user_func(array($this, 'output_' . $block->block_type), $block, $index);

					?>

				</div>
			</li>

		<?php }

		function output_title($block, $index){ 

			$content = json_decode($block->block_content);

			?>

			<input type="text" name="px_blocks[<?php echo $index; ?>][content]" class="cb_content title" value="<?php echo stripslashes($content); ?>">

		<?php }

		function output_h2($block, $index){

			$content = json_decode($block->block_content);

			?>

			<input type="text" name="px_blocks[<?php echo $index; ?>][content]" class="cb_content h2" value="<?php echo stripslashes($content); ?>">

		<?php }

		function output_h3($block, $index){

			$content = json_decode($block->block_content);

			?>

			<input type="text" name="px_blocks[<?php echo $index; ?>][content]" class="cb_content h3" value="<?php echo stripslashes($content); ?>">

		<?php }

		function output_paragraph($block, $index){ ?>

			<div class="load_Quill"></div>
			<textarea name="px_blocks[<?php echo $index; ?>][content]" style="display: none"><?php echo stripslashes(json_decode($block->block_content)); ?></textarea>

		<?php }

		function output_quote($block, $index){ ?>
			
			<textarea name="px_blocks[<?php echo $index; ?>][content]" class="cb_content quote"><?php echo json_decode($block->block_content); ?></textarea>

		<?php }

		function output_hr($block, $index){ ?>
			
			<input name="px_blocks[<?php echo $index; ?>][content]" type="hidden" value="hr" class="cb_content hr">
			<hr>

		<?php }

		function output_link($block, $index){ 

			$blockArray = json_decode($block->block_content);
			$url = $blockArray->link_url;
			$txt = $blockArray->link_text;

			?>

			<input placeholder="Link URL..." name="px_blocks[<?php echo $index; ?>][content][link_url]" type="text" value="<?php echo $url; ?>" class="cb_content link">

			<input placeholder="Link Text..." name="px_blocks[<?php echo $index; ?>][content][link_text]" type="text" value="<?php echo $txt; ?>" class="cb_content link">

		<?php }

		function output_preface($block, $index){ 

			$blockArray = json_decode($block->block_content);
			$title = $blockArray->title;
			$content = $blockArray->preface_content;
			$img_src = $blockArray->img_src;
			$img_alt = $blockArray->img_alt;

			?>
			
			<input placeholder="Title..." name="px_blocks[<?php echo $index; ?>][content][title]" type="text" class="cb_content preface" value="<?php echo $title; ?>">
			<div class="load_Quill"></div>
			<textarea name="px_blocks[<?php echo $index; ?>][content][preface_content]" style="display: none"><?php echo stripslashes($content); ?></textarea>
			<input class="media_upload_input img_src" type="hidden" name="px_blocks[<?php echo $index; ?>][content][img_src]" value="<?php echo $img_src; ?>">
			<input class="media_upload_input img_alt" type="hidden" name="px_blocks[<?php echo $index; ?>][content][img_alt]" value="<?php echo $img_alt; ?>">
			<div class="btn upload_img_btn">Insert Preface Image</div>
			<img src="<?php echo $img_src; ?>" class="imgBlockImg">

		<?php }

		function output_image($block, $index){

			$blockArray = json_decode($block->block_content);
			$img_src = $blockArray->img_src;
			$img_alt = $blockArray->img_alt;

			?>
			
			<input type="hidden" name="px_blocks[<?php echo $index; ?>][content][img_src]" value="<?php echo $img_src; ?>" class="cb_content img_src">
			<input type="hidden" name="px_blocks[<?php echo $index; ?>][content][img_alt]" value="<?php echo $img_alt; ?>" class="cb_content img_alt">
			<div class="btn upload_img_btn">Insert Image</div>
			<img class="imgBlockImg" src="<?php echo $img_src; ?>">
			
		<?php }

		function output_embed($block, $index){ ?>
			
			<textarea class="embed" name="px_blocks[<?php echo $index; ?>][content]"><?php echo stripslashes(json_decode($block->block_content)); ?></textarea>

		<?php }

		function output_gallery($block, $index){ ?>
			
			<div class="btn upload_gallery_img">Add Image to Gallery</div>
			<ul class="gallery_list" data-blockindex="<?php echo $index; ?>">

				<?php foreach (json_decode($block->block_content) as $img) { 

					$imgArray = explode('###', $img);

					?>
						
					<li>
						<input type="hidden" value="<?php echo $img; ?>" name="px_blocks[<?php echo $index; ?>][content][]">
						<div class="removeImgOverlay">Remove</div>
						<img src="<?php echo $imgArray[0]; ?>">
					</li>

				<?php } ?>	

			</ul>

		<?php }

		function output_video($block, $index){ ?>
			
			<div class="btn upload_video_btn">Upload Video</div>
			<input name="px_blocks[<?php echo $index; ?>][content]" value="<?php echo json_decode($block->block_content); ?>" class="media_upload_input" type="text">

		<?php }

		function output_advert($block, $index){ 

			$o = get_post_meta( get_the_ID(), 'px_outcome', true );
			$o = explode('###', $o);

			$outcomeid = $o[0];
			$funnel_position = get_post_meta( get_the_ID(), 'funnel_position', true );

			global $wpdb;
	        $adverts = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_content_adverts WHERE outcome_id = '$outcomeid' AND funnel_position = '$funnel_position';");

	        $adId = json_decode($block->block_content);

			?>
			
			<select name="px_blocks[<?php echo $index; ?>][content]" class="cb_content advert" style="">
				<option value="">- select advert -</option>
			
				<?php 

					foreach ($adverts as $ad) {
						
						$selected = '';
						
						if($ad->id == $adId){
							$selected = 'selected';
						}

						echo '<option value="' . $ad->id . '" ' . $selected . '>' . $ad->title . '</option>';
					
					}

				?>
					
			</select>

		<?php }

		function output_slideshow($block, $index){ 

			$i = 0;

			?>
			
			<div class="left">
				<h2>Slides</h2>
				<div class="btnNewSlide">ADD NEW SLIDE</div>
				<ul class="slideList ui-sortable" data-blockindex="<?php echo $index; ?>">

					<?php foreach (json_decode($block->block_content) as $slide) {

						$slideObj = json_decode( stripslashes($slide) );

						echo "<li data-index='" . $i . "'>";
						echo "<input name='px_blocks[" . $index . "][content][]' type='hidden' value='" . stripslashes($slide) . "'>";
						echo "<p>" . $slideObj->title . "</p>";
						echo "<span>x</span>";
						echo "</li>";

						$i = $i + 1;

					} ?>

				</ul>
			</div>
			<div class="right">

			</div>

		<?php }

		function output_rating($block, $index){
			
			echo '<input name="px_blocks[' . $index . '][content]" style="display: none" value="' . json_decode($block->block_content) . '" type="number" min="0" max="5">';
			echo '<ul class="starList">';

		  		for($i=1; $i<6; $i++){
		  			if($i > json_decode($block->block_content)){
		  				echo '<li style="filter: grayscale(100%)"></li>';
		  			}else{
		  				echo '<li></li>';
		  			}
		  		}

  			echo '</ul>';

		}

		function output_download($block, $index){
			
			$o = get_post_meta( get_the_ID(), 'px_outcome', true );
			$o = explode('###', $o);

			$outcomeid = $o[0];
			$funnel_position = get_post_meta( get_the_ID(), 'funnel_position', true );

			global $wpdb;
	        $downloads = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_downloads WHERE outcome_id = '$outcomeid' AND funnel_position = '$funnel_position';");

	        $downloadObj = json_decode($block->block_content);
	        $downloadId = $downloadObj->download;

	        $emailRequired = '';
	        if(property_exists($downloadObj, 'email_required'))
	        	$emailRequired = 'checked';

			?>

			<label>Heading for the download box</label>
			<input type="text" value="<?php echo $downloadObj->heading; ?>" placeholder="Download our FREE guide to..." name="px_blocks[<?php echo $index; ?>][content][heading]">
			<select name="px_blocks[<?php echo $index; ?>][content][download]" class="cb_content download">
				<option value="">- select advert -</option>
			
				<?php 

					foreach ($downloads as $download) {
						
						$selected = '';
						
						if($download->id == $downloadId){
							$selected = 'selected';
						}

						echo '<option value="' . $download->id . '" ' . $selected . '>' . $download->filename . '</option>';
					
					}

				?>
					
			</select>

			<label>Email required for download?</label>
			<input type="checkbox" class="checkBoxInput" name="px_blocks[<?php echo $index; ?>][content][email_required]" <?php echo $emailRequired; ?>>

		<?php }

		function remove_contentblocks($postid){

			global $wpdb;
			$wpdb->delete(
				$wpdb->prefix . 'px_content_blocks',
				array(
					'post_id' => $postid
				)
			);

		}

		function get_content_adverts(){

			header('Content-Type: application/json');

			$outcomeid = $_POST['outcome_id'];
			$funnel_position = $_POST['funnel_position'];

			global $wpdb;
			$adverts = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_content_adverts WHERE outcome_id = '$outcomeid' AND funnel_position = '$funnel_position';");

			// error_log('Adverts???? ' . print_r($adverts));

			$response['funnel_position'] = $funnel_position;
			$response['outcome_id'] = $outcomeid;
			$response['ads'] = $adverts;

			$response['success'] = 1;
			echo json_encode($response);
		    die();

		}

		function get_advert_img(){

			header('Content-Type: application/json');

			$adId = $_POST['advertId'];

			global $wpdb;
			$advert = $wpdb->get_results("SELECT ad_img FROM " . $wpdb->prefix . "px_content_adverts WHERE id = '$adId';");

			$response['advert'] = $advert;
			$response['success'] = 1;
			echo json_encode($response);
		    die();

		}

		function get_downloads(){

			header('Content-Type: application/json');

			$outcomeid = $_POST['outcome_id'];
			$funnel_position = $_POST['funnel_position'];

			global $wpdb;
			$response['downloads'] = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_downloads WHERE outcome_id = '$outcomeid' AND funnel_position = '$funnel_position';");

			$response['success'] = 1;
			echo json_encode($response);
		    die();

		}

		function __construct(){

			add_action( 'edit_form_advanced', array($this, 'add_content_block_buttons'), 12 );
			add_action( 'save_post', array($this, 'save_content_blocks'), 10, 2 );

			add_action( 'wp_ajax_get_content_adverts', array($this, 'get_content_adverts') );
			add_action( 'wp_ajax_nopriv_get_content_adverts', array($this, 'get_content_adverts' ) );

			add_action( 'wp_ajax_get_downloads', array($this, 'get_downloads') );
			add_action( 'wp_ajax_nopriv_get_downloads', array($this, 'get_downloads' ) );

			add_action( 'wp_ajax_get_advert_img', array($this, 'get_advert_img') );

			add_action( 'before_delete_post', array($this, 'remove_contentblocks') );

		}

	}

?>