<?php

// Setup an AJAX action to close the donation nag banner on the overview page.
function wp_statistics_close_donation_nag_action_callback() {
	GLOBAL $WP_Statistics, $wpdb; // this is how you get access to the database

	$manage_cap = wp_statistics_validate_capability( $WP_Statistics->get_option('manage_capability', 'manage_options') );
	
	if( current_user_can( $manage_cap ) ) {
		$WP_Statistics->update_option( 'disable_donation_nag', true );
	}
	
	wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_wp_statistics_close_donation_nag', 'wp_statistics_close_donation_nag_action_callback' );

// Setup an AJAX action to delete an agent in the optimization page.
function wp_statistics_delete_agents_action_callback() {
	GLOBAL $WP_Statistics, $wpdb; // this is how you get access to the database

	$manage_cap = wp_statistics_validate_capability( $WP_Statistics->get_option('manage_capability', 'manage_options') );

	if( current_user_can( $manage_cap ) ) {
		$agent = $_POST['agent-name'];
		
		if( $agent ) {
			
			$result = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}statistics_visitor WHERE `agent` = %s", $agent ));
			
			if($result) {
				echo sprintf(__('%s agent data deleted successfully.', 'wp_statistics'), '<code>' . $agent . '</code>');
			}
			else {
				_e('No agent data found to remove!', 'wp_statistics');
			}
			
		} else {
			_e('Please select the desired items.', 'wp_statistics');
		}
	} else {
		_e('Access denied!', 'wp_statistics');
	}
	
	wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_wp_statistics_delete_agents', 'wp_statistics_delete_agents_action_callback' );

// Setup an AJAX action to delete a platform in the optimization page.
function wp_statistics_delete_platforms_action_callback() {
	GLOBAL $WP_Statistics, $wpdb; // this is how you get access to the database

	$manage_cap = wp_statistics_validate_capability( $WP_Statistics->get_option('manage_capability', 'manage_options') );

	if( current_user_can( $manage_cap ) ) {
		$platform = $_POST['platform-name'];
		
		if( $platform ) {
			
			$result = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}statistics_visitor WHERE `platform` = %s", $platform));
			
			if($result) {
				echo sprintf(__('%s platform data deleted successfully.', 'wp_statistics'), '<code>' . htmlentities( $platform, ENT_QUOTES ) . '</code>');
			}
			else {
				_e('No platform data found to remove!', 'wp_statistics');
			}
		} else {
			_e('Please select the desired items.', 'wp_statistics');
		}
	} else {
		_e('Access denied!', 'wp_statistics');
	}
	
	wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_wp_statistics_delete_platforms', 'wp_statistics_delete_platforms_action_callback' );

// Setup an AJAX action to empty a table in the optimization page.
function wp_statistics_empty_table_action_callback() {
	GLOBAL $WP_Statistics, $wpdb; // this is how you get access to the database

	$manage_cap = wp_statistics_validate_capability( $WP_Statistics->get_option('manage_capability', 'manage_options') );

	if( current_user_can( $manage_cap ) ) {
		$table_name = $_POST['table-name'];

		if($table_name) {

			switch( $table_name ) {
				case 'useronline':
					echo wp_statitiscs_empty_table($wpdb->prefix . 'statistics_useronline');
					break;
				case 'visit':
					echo wp_statitiscs_empty_table($wpdb->prefix . 'statistics_visit');
					break;
				case 'visitors':
					echo wp_statitiscs_empty_table($wpdb->prefix . 'statistics_visitor');
					break;
				case 'exclusions':
					echo wp_statitiscs_empty_table($wpdb->prefix . 'statistics_exclusions');
					break;
				case 'pages':
					echo wp_statitiscs_empty_table($wpdb->prefix . 'statistics_pages');
					break;
				case 'search':
					echo wp_statitiscs_empty_table($wpdb->prefix . 'statistics_search');
					break;
				case 'all':
					$result_string = wp_statitiscs_empty_table($wpdb->prefix . 'statistics_useronline');
					$result_string .= '<br>' . wp_statitiscs_empty_table($wpdb->prefix . 'statistics_visit');
					$result_string .= '<br>' . wp_statitiscs_empty_table($wpdb->prefix . 'statistics_visitor');
					$result_string .= '<br>' . wp_statitiscs_empty_table($wpdb->prefix . 'statistics_exclusions');
					$result_string .= '<br>' . wp_statitiscs_empty_table($wpdb->prefix . 'statistics_pages');
					$result_string .= '<br>' . wp_statitiscs_empty_table($wpdb->prefix . 'statistics_search');

					echo $result_string;
					
					break;
				default:
					_e('Please select the desired items.', 'wp_statistics');
			}

			$WP_Statistics->Primary_Values();
		
		} else {
			_e('Please select the desired items.', 'wp_statistics');
		}
	} else {
		_e('Access denied!', 'wp_statistics');
	}
	
	wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_wp_statistics_empty_table', 'wp_statistics_empty_table_action_callback' );

// Setup an AJAX action to purge old data in the optimization page.
function wp_statistics_purge_data_action_callback() {
	GLOBAL $WP_Statistics, $wpdb; // this is how you get access to the database

	require($WP_Statistics->plugin_dir . '/includes/functions/purge.php');
	
	$manage_cap = wp_statistics_validate_capability( $WP_Statistics->get_option('manage_capability', 'manage_options') );

	if( current_user_can( $manage_cap ) ) {
		$purge_days = 0;

		if( array_key_exists( 'purge-days', $_POST ) ) { 
			// Get the number of days to purge data before.
			$purge_days = intval($_POST['purge-days']);
		}
		
		echo wp_statistics_purge_data( $purge_days );
	} else {
		_e('Access denied!', 'wp_statistics');
	}
	
	wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_wp_statistics_purge_data', 'wp_statistics_purge_data_action_callback' );

// Setup an AJAX action to purge visitors with more than a defined number of hits.
function wp_statistics_purge_visitor_hits_action_callback() {
	GLOBAL $WP_Statistics, $wpdb; // this is how you get access to the database

	require($WP_Statistics->plugin_dir . '/includes/functions/purge-hits.php');
	
	$manage_cap = wp_statistics_validate_capability( $WP_Statistics->get_option('manage_capability', 'manage_options') );

	if( current_user_can( $manage_cap ) ) {
		$purge_hits = 10;

		if( array_key_exists( 'purge-hits', $_POST ) ) { 
			// Get the number of days to purge data before.
			$purge_hits = intval($_POST['purge-hits']);
		}
		
		if( $purge_hits < 10 ) { 
			_e('Number of hits must be greater than or equal to 10!', 'wp_statistics');
		}
		else {
			echo wp_statistics_purge_visitor_hits( $purge_hits );
		}
	} else {
		_e('Access denied!', 'wp_statistics');
	}
	
	wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_wp_statistics_purge_visitor_hits', 'wp_statistics_purge_visitor_hits_action_callback' );
