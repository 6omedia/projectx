<?php

	require_once( dirname(__FILE__) . '/../../../wp-load.php' );

	function email_csv_download($array, $filename = "export.csv") {
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachment; filename="'.$filename.'";');

	    $f = fopen('php://output', 'w');
	    fputcsv($f, array_keys($array['0']));

	    foreach ($array as $line) {
	        fputcsv($f, $line);
	    }
	} 

	if(isset($_POST['csvdownload'])){

		$theArr = array(
		    array( 'item' => 'Server', 'cost' => 10000, 'approved by' => 'Joe'),
		    array( 'item' => 'Mt Dew', 'cost' => 1.25, 'approved by' => 'John'),
		    array( 'item' => 'IntelliJ IDEA', 'cost' => 500, 'approved by' => 'James'),
		);

		// get emails
	    global $wpdb;
	    $results = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "px_email_list WHERE download_id=" . $_POST['csvdownload'] . ";");

	    $emails = array();

	    foreach ($results as $email) {
	    	$emails[] = array('Email' => $email->email );
	    }

		email_csv_download(
			$emails,
		  	"captured_emails.csv"
		);
	}