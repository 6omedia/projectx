<div class="px_container">

	<h1>Excluded IP's</h1>

	<ul class="px_add_ips_form">
		<li>
			<label>IP Address</label><br/>
			<input type="text" placeholder="Enter an IP to exclude..." id="input_ip">
		</li>
		<li>
			<label>Short Description</label><br/>
			<input type="text" placeholder="This IP is for..." id="input_desc">	
			<button id="add_btn"></button>
		</li>
	</ul>

	<h2>IP List</h2>

	<ul id="ex_ip_list">
		<?php

			global $wpdb;
			$ips = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_users WHERE blocked=1;");

			foreach ($ips as $ip) {
				echo '<li data-ipid="' . $ip->id . '"><span class="minus-span"></span>' . $ip->ip . ' - ' . $ip->description . '</li>';
			}

			// global $wpdb;
			// $ips = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_excluded_ips;");

			// foreach ($ips as $ip) {
			// 	echo '<li  data-ipid="' . $ip->id . '"><span class="minus-span"></span>' . $ip->ip . ' - ' . $ip->description . '</li>';
			// }

		?>
		<?php global $plugin_url; ?>
		<li><img class="spin" src="<?php echo $plugin_url; ?>/img/spin.gif"></li>
	</ul>

	<div id="notices">
		<div></div>
		<p class="errorNotice"></p>
	</div>

</div>