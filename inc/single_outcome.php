<?php

	$outcomeid = $_GET['outcomeid'];

    $a_top_ad = '';
    $a_top_link = '';
    $a_side_ad = '';
    $a_side_link = '';
    $a_bottom_ad = '';
    $a_bottom_link = '';

    $r_top_ad = '';
    $r_top_link = '';
    $r_side_ad = '';
    $r_side_link = '';
    $r_bottom_ad = '';
    $r_bottom_link = '';

    $c_top_ad = '';
    $c_top_link = '';
    $c_side_ad = '';
    $c_side_link = '';
    $c_bottom_ad = '';
    $c_bottom_link = '';

    $p_top_ad = '';
    $p_top_link = '';
    $p_side_ad = '';
    $p_side_link = '';
    $p_bottom_ad = '';
    $p_bottom_link = '';

	global $wpdb;
	$outcomeInfo = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "outcomes WHERE id = $outcomeid;");

	global $wpdb;
	$awareness_ads = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_adverts WHERE outcome_id = $outcomeid AND funnel_position = 'awareness';");

    if(!empty($awareness_ads)){
        $a_top_ad = $awareness_ads[0]->top_ad;
        $a_top_link = $awareness_ads[0]->top_link;
        $a_side_ad = $awareness_ads[0]->side_ad;
        $a_side_link = $awareness_ads[0]->side_link;
        $a_bottom_ad = $awareness_ads[0]->bottom_ad;
        $a_bottom_link = $awareness_ads[0]->bottom_link;
    }

	$research_ads = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_adverts WHERE outcome_id = $outcomeid AND funnel_position = 'research';");

    if(!empty($research_ads)){
        $r_top_ad = $research_ads[0]->top_ad;
        $r_top_link = $research_ads[0]->top_link;
        $r_side_ad = $research_ads[0]->side_ad;
        $r_side_link = $research_ads[0]->side_link;
        $r_bottom_ad = $research_ads[0]->bottom_ad;
        $r_bottom_link = $research_ads[0]->bottom_link;
    }

	$comparison_ads = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_adverts WHERE outcome_id = $outcomeid AND funnel_position = 'comparison';");
    
    if(!empty($comparison_ads)){
        $c_top_ad = $comparison_ads[0]->top_ad;
        $c_top_link = $comparison_ads[0]->top_link;
        $c_side_ad = $comparison_ads[0]->side_ad;
        $c_side_link = $comparison_ads[0]->side_link;
        $c_bottom_ad = $comparison_ads[0]->bottom_ad;
        $c_bottom_link = $comparison_ads[0]->bottom_link;
    }

	$purchase_ads = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_adverts WHERE outcome_id = $outcomeid AND funnel_position = 'purchase';");

    if(!empty($purchase_ads)){
        $p_top_ad = $purchase_ads[0]->top_ad;
        $p_top_link = $purchase_ads[0]->top_link;
        $p_side_ad = $purchase_ads[0]->side_ad;
        $p_side_link = $purchase_ads[0]->side_link;
        $p_bottom_ad = $purchase_ads[0]->bottom_ad;
        $p_bottom_link = $purchase_ads[0]->bottom_link;
    }

?>

<div class="px_container">

    <ul class="px_breadcrumb">
        <li><a href="admin.php?page=campaigns">Campaigns</a></li>
        <li>&#10095;&#10095;</li>
        <li><a href="admin.php?page=single-campaign&campaignid=<?php echo $_GET['campaignid'] ?>"><?php echo $_GET['campaign_name']; ?></a></li>
        <li>&#10095;&#10095;</li>
        <li><a href="#"><?php echo $outcomeInfo[0]->title; ?></a></li>
    </ul>
	
	<!-- <h1><?php // echo $outcomeInfo[0]->title; ?></h1> -->

    <label>Outcome Title:</label><br/>
    <input type="text" id="outcome_title_input" value="<?php echo $outcomeInfo[0]->title; ?>">

	<div class="tabs">
    
		<ul id="tabs_nav" data-outcomeid="<?php echo $outcomeid; ?>">
			<li data-tab-id="fp_awareness" class="currentTab">Awareness</li>
			<li data-tab-id="fp_research">Research</li>
			<li data-tab-id="fp_comparison">Comparison</li>
			<li data-tab-id="fp_purchase">Purchase</li>
		</ul>

    	<div class="tab" id="fp_awareness">
    		<h2>Awareness</h2>
    		<ul class="advert_list" id="ad_list_awareness">
    			<li>
    				<button id="awareness_t_ad_btn">Upload Top Advert</button>
    				<input type="text" id="awareness_t_upload" value="<?php echo $a_top_ad; ?>">
    				<br/>
    				<label>Advert Link</label>
    				<input type="text" id="awareness_t_link" value="<?php echo $a_top_link; ?>">
    			</li>
    			<li>
    				<button id="awareness_s_ad_btn">Upload Side Advert</button>
    				<input type="text" id="awareness_s_upload" value="<?php echo $a_side_ad; ?>">
    				<br/>
    				<label>Advert Link</label>
    				<input type="text" id="awareness_s_link" value="<?php echo $a_side_link; ?>">
    			</li>
    			<li>
    				<button id="awareness_b_ad_btn">Upload Bottom Advert</button>
    				<input type="text" id="awareness_b_upload" value="<?php echo $a_bottom_ad; ?>">
    				<br/>
    				<label>Advert Link</label>
    				<input type="text" id="awareness_b_link" value="<?php echo $a_bottom_link; ?>">
    			</li>
    			<li>
    				<h2>Content Adverts</h2>
    			</li>
    			<li>
					<button id="awareness_upload_btn">Upload Advert Image</button>
					<input id="content_ad_img_input" type="text" name="" placeholder="Image Upload...">
					<br/>
					<label>Advert Title</label>
					<input id="content_ad_title_input" type="text" name="" placeholder="Advert Title...">
					<br/>
    				<label>Advert Link</label>
					<input id="content_ad_link_input" type="text" name="" placeholder="Advert Link...">
					<button id="add_ad_btn" class="add_icon"></button>
				</li>
				<li>
	    			<ul id="content_ad_list" class="content_ad_lists">
	   					<?php

							global $wpdb;
							$adverts = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_content_adverts WHERE outcome_id = $outcomeid AND funnel_position = 'awareness';");

							foreach ($adverts as $advert) {
								echo '<li data-advertid="' . $advert->id . '"  data-advertlink="' . $advert->ad_link . '" data-adtitle="'. $advert->title .'">';
                                echo '<p>' . $advert->title . '</p>';
                                echo '<span></span><img src="' . $advert->ad_img . '"></a></li>';
							}

						?>
	    			</ul>
    			</li>
    			<li><button class="set_ads_btn" id="awareness_set_ad">Update Outcome</button></li>
    		</ul>
    	</div>

    	<div class="tab" id="fp_research">
    		<h2>Research</h2>
    		<ul class="advert_list" id="ad_list_research">
    			<li>
    				<button id="research_t_ad_btn">Upload Top Advert</button>
    				<input type="text" id="research_t_upload" value="<?php echo $r_top_ad; ?>">
    				<br/>
    				<label>Advert Link</label>
    				<input type="text" id="research_t_link" value="<?php echo $r_top_link; ?>">
    			</li>
    			<li>
    				<button id="research_s_ad_btn">Upload Side Advert</button>
    				<input type="text" id="research_s_upload" value="<?php echo $r_side_ad; ?>">
    				<br/>
    				<label>Advert Link</label>
    				<input type="text" id="research_s_link" value="<?php echo $r_side_link; ?>">
    			</li>
    			<li>
    				<button id="research_b_ad_btn">Upload Bottom Advert</button>
    				<input type="text" id="research_b_upload" value="<?php echo $r_bottom_ad; ?>">
    				<br/>
    				<label>Advert Link</label>
    				<input type="text" id="research_b_link" value="<?php echo $r_bottom_link; ?>">
    			</li>
    			<li>
    				<h2>Content Adverts</h2>
    			</li>
    			<li>
					<button id="research_upload_btn">Upload Advert Image</button>
					<input id="content_ad_img_research" type="text" name="" placeholder="Image Upload...">
					<br/>
                    <label>Advert Title</label>
                    <input id="content_ad_title_research" type="text" name="" placeholder="Advert Title...">
                    <br/>
    				<label>Advert Link</label>
					<input id="content_ad_link_research" type="text" name="" placeholder="Advert Link...">
					<button id="research_ad_btn" class="add_icon"></button>
				</li>
				<li>
	    			<ul id="research_c_ad_list" class="content_ad_lists">
	   					<?php

							global $wpdb;
							$adverts = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_content_adverts WHERE outcome_id = $outcomeid AND funnel_position = 'research';");

							foreach ($adverts as $advert) {
								echo '<li data-advertid="' . $advert->id . '"  data-advertlink="' . $advert->ad_link . '" data-adtitle="'. $advert->title .'">';
                                echo '<p>' . $advert->title . '</p>';
                                echo '<span></span><img src="' . $advert->ad_img . '"></a></li>';
							}

						?>
	    			</ul>
    			</li>
    			<li><button class="set_ads_btn" id="research_set_ad">Update Outcome</button></li>
    		</ul>
    	</div>

    	<div class="tab" id="fp_comparison">
    		<h2>Comparison</h2>
    		<ul class="advert_list" id="ad_list_comparison">
    			<li>
    				<button id="comparison_t_ad_btn">Upload Top Advert</button>
    				<input type="text" id="comparison_t_upload" value="<?php echo $c_top_ad; ?>">
    				<br/>
    				<label>Advert Link</label>
    				<input type="text" id="comparison_t_link" value="<?php echo $c_top_link; ?>">
    			</li>
    			<li>
    				<button id="comparison_s_ad_btn">Upload Side Advert</button>
    				<input type="text" id="comparison_s_upload" value="<?php echo $c_side_ad; ?>">
    				<br/>
    				<label>Advert Link</label>
    				<input type="text" id="comparison_s_link" value="<?php echo $c_side_link; ?>">
    			</li>
    			<li>
    				<button id="comparison_b_ad_btn">Upload Bottom Advert</button>
    				<input type="text" id="comparison_b_upload" value="<?php echo $c_bottom_ad; ?>">
    				<br/>
    				<label>Advert Link</label>
    				<input type="text" id="comparison_b_link" value="<?php echo $c_bottom_link; ?>">
    			</li>
    			<li>
    				<h2>Content Adverts</h2>
    			</li>
    			<li>
					<button id="comparison_upload_btn">Upload Advert Image</button>
					<input id="content_ad_img_comparison" type="text" name="" placeholder="Image Upload...">
					<br/>
                    <label>Advert Title</label>
                    <input id="content_ad_title_comparison" type="text" name="" placeholder="Advert Title...">
                    <br/>
    				<label>Advert Link</label>
					<input id="content_ad_link_comparison" type="text" name="" placeholder="Advert Link...">
					<button id="comparison_ad_btn" class="add_icon"></button>
				</li>
				<li>
	    			<ul id="comparison_c_ad_list" class="content_ad_lists">
	   					<?php

							global $wpdb;
							$adverts = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_content_adverts WHERE outcome_id = $outcomeid AND funnel_position = 'comparison';");

							foreach ($adverts as $advert) {
								echo '<li data-advertid="' . $advert->id . '"  data-advertlink="' . $advert->ad_link . '" data-adtitle="'. $advert->title .'">';
                                echo '<p>' . $advert->title . '</p>';
                                echo '<span></span><img src="' . $advert->ad_img . '"></a></li>';
							}

						?>
	    			</ul>
    			</li>
    			<li><button class="set_ads_btn" id="comparison_set_ad">Update Outcome</button></li>
    		</ul>
    	</div>

    	<div class="tab" id="fp_purchase">
    		<h2>Purchase</h2>
    		<ul class="advert_list" id="ad_list_purchase">
    			<li>
    				<button id="purchase_t_ad_btn">Upload Top Advert</button>
    				<input type="text" id="purchase_t_upload" value="<?php echo $p_top_ad; ?>">
    				<br/>
    				<label>Advert Link</label>
    				<input type="text" id="purchase_t_link" value="<?php echo $p_top_link; ?>">
    			</li>
    			<li>
    				<button id="purchase_s_ad_btn">Upload Side Advert</button>
    				<input type="text" id="purchase_s_upload" value="<?php echo $p_side_ad; ?>">
    				<br/>
    				<label>Advert Link</label>
    				<input type="text" id="purchase_s_link" value="<?php echo $p_side_link; ?>">
    			</li>
    			<li>
    				<button id="purchase_b_ad_btn">Upload Bottom Advert</button>
    				<input type="text" id="purchase_b_upload" value="<?php echo $p_bottom_ad; ?>">
    				<br/>
    				<label>Advert Link</label>
    				<input type="text" id="purchase_b_link" value="<?php echo $p_bottom_link; ?>">
    			</li>
    			<li>
    				<h2>Content Adverts</h2>
    			</li>
    			<li>
					<button id="purchase_upload_btn">Upload Advert Image</button>
					<input id="content_ad_img_purchase" type="text" name="" placeholder="Image Upload...">
					<br/>
                    <label>Advert Title</label>
                    <input id="content_ad_title_purchase" type="text" name="" placeholder="Advert Title...">
                    <br/>
    				<label>Advert Link</label>
					<input id="content_ad_link_purchase" type="text" name="" placeholder="Advert Link...">
					<button id="purchase_ad_btn" class="add_icon"></button>
				</li>
				<li>
	    			<ul id="purchase_c_ad_list" class="content_ad_lists">
	   					<?php

							global $wpdb;
							$adverts = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_content_adverts WHERE outcome_id = $outcomeid AND funnel_position = 'purchase';");

							foreach ($adverts as $advert) {
								echo '<li data-advertid="' . $advert->id . '"  data-advertlink="' . $advert->ad_link . '" data-adtitle="'. $advert->title .'">';
                                echo '<p>' . $advert->title . '</p>';
                                echo '<span></span><img src="' . $advert->ad_img . '"></a></li>';
							}

						?>
	    			</ul>
    			</li>
    			<li><button class="set_ads_btn" id="purchase_set_ad">Update Outcome</button></li>
    		</ul>
    	</div>
	    
	</div>

	<div id="notices">
		<div></div>
		<p class="errorNotice"></p>
	</div>

</div>