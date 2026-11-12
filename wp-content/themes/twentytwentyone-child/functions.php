<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if (!function_exists('chld_thm_cfg_locale_css')):
	function chld_thm_cfg_locale_css($uri)
	{
		if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css'))
			$uri = get_template_directory_uri() . '/rtl.css';
		return $uri;
	}
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

if (!function_exists('child_theme_configurator_css')):
	function child_theme_configurator_css()
	{
		wp_enqueue_style('chld_thm_cfg_child', trailingslashit(get_stylesheet_directory_uri()) . 'style.css', array('twenty-twenty-one-custom-color-overrides', 'twenty-twenty-one-style', 'twenty-twenty-one-style', 'twenty-twenty-one-print-style'));
	}
endif;
add_action('wp_enqueue_scripts', 'child_theme_configurator_css', 10);

// END ENQUEUE PARENT ACTION

add_action('after_setup_theme', function () {
	if (is_user_logged_in() && current_user_can('subscriber')) {
		show_admin_bar(false);
	}
});

// FRONTEND ASSETS
add_action('wp_enqueue_scripts', 'iamtoi_load_dashboard_assets');
function iamtoi_load_dashboard_assets()
{
	wp_enqueue_style('dashboard-css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/dashboard.css', [], wp_rand());
	wp_enqueue_style('stories-css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/stories.css', [], wp_rand());
	wp_enqueue_script('jquery');
	wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
	wp_enqueue_script('uploader-js', trailingslashit(get_stylesheet_directory_uri()) . 'assets/js/uploader.js', array('jquery'), wp_rand(), true);
	wp_enqueue_script('dashboard-js', trailingslashit(get_stylesheet_directory_uri()) . 'assets/js/dashboard.js', ['chart-js'], wp_rand(), true);
}

add_action('after_setup_theme', function () {
	require_once get_stylesheet_directory() . '/custom/api/class-api-authentication.php';
	require_once get_stylesheet_directory() . '/custom/api/class-api-response.php';
});

add_action('init', 'disable_admin_bar_for_subscribers');
function disable_admin_bar_for_subscribers()
{
	if (is_user_logged_in()) {
		$user = wp_get_current_user();
		if (in_array('subscriber', (array) $user->roles)) {
			show_admin_bar(false);
			add_filter('show_admin_bar', '__return_false');
		}
	}
}


// Clear third-party session/cookie on WP logout
add_action('wp_logout', 'cqz_custom_logout_cleanup');
function cqz_custom_logout_cleanup()
{
	setcookie('i_s', '', time() - 3600, '/', '.timesgroup.com', true, true);
	// Add any other third-party session cleanup here if needed
}

add_action('init', 'check_login_user', 10);
function check_login_user()
{
	$all_policy_admin = is_string(ALL_ADMIN) ? explode(',', ALL_ADMIN) : (array) ALL_ADMIN;

	// Skip if already logged in or on wp-login page
	if (is_user_logged_in() || $GLOBALS['pagenow'] === 'wp-login.php' || is_admin()) {
		return;
	}

	// === COOKIE AUTH ===
	if (!empty($_COOKIE['i_s']) && empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW'])) {
		// Attempting cookie-based login...

		$apiResponse = new apiResponse();
		$api_response = $apiResponse->curlresponse();
		// Cookie API Response: ' . print_r($api_response, true)

		if ($api_response && $api_response['resp'] == 'true') {
			$email = $api_response['email'];
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$user_id = email_exists($email);

				if ($user_id) {
					$user = get_user_by('id', $user_id);
					// Logging in existing user via cookie: $email
				} else {
					$user_id = wp_create_user(strtolower($email), "", $email);
					$user = get_user_by('id', $user_id);
					// Created new user via cookie: $email
				}

				// Update role if policy admin
				if (in_array($user_id, $all_policy_admin)) {
					wp_update_user(['ID' => $user_id, 'role' => 'policy-admin']);
				}

				// Update basic profile
				wp_update_user([
					'ID'         => $user_id,
					'first_name' => $api_response['first_name'] ?? '',
					'last_name'  => $api_response['last_name'] ?? ''
				]);

				// Update meta fields
				foreach (['branch', 'location', 'designation', 'department', 'sap', 'profile_picture', 'manager_email'] as $meta_key) {
					if (isset($api_response[$meta_key])) {
						update_user_meta($user_id, $meta_key, $api_response[$meta_key]);
					}
				}

				// Log in user for dashboard access
				$user = get_user_by('id', $user_id); // example user
				if ($user) {
					wp_set_current_user($user_id);
					wp_set_auth_cookie($user_id, true);
					do_action('wp_login', $user->user_login, $user);
					// Redirect or reload
					wp_safe_redirect(home_url()); // or any URL
					exit;
				}
			} else {
				wp_die("Invalid email from cookie API: $email");
				header('WWW-Authenticate: Basic realm="Authentication Required"');
				exit;
			}
		} else {
			wp_die("Cookie-based API response invalid.");
			header('WWW-Authenticate: Basic realm="Authentication Required"');
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}

		// === BASIC AUTH ===
	} elseif (!empty($_SERVER['PHP_AUTH_USER']) || !empty($_SERVER['PHP_AUTH_PW'])) {
		// Attempting basic auth login...

		$apiAuthentication = new apiAuthentication();
		$user_info = $apiAuthentication->curlrequest($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
		// Basic Auth API Response: ' . print_r($user_info, true)

		if (isset($user_info['email']) && filter_var($user_info['email'], FILTER_VALIDATE_EMAIL)) {
			$email = $user_info['email'];
			$user_id = email_exists($email);

			if ($user_id) {
				$user = get_user_by('id', $user_id);
				// Logging in existing user via basic auth: $email
			} else {
				$user_id = wp_create_user(strtolower($email), "", $email);
				$user = get_user_by('id', $user_id);
				// Created new user via basic auth: $email
			}

			// Role update
			if (in_array($user_id, $all_policy_admin)) {
				wp_update_user(['ID' => $user_id, 'role' => 'policy-admin']);
			}

			// Profile update
			wp_update_user([
				'ID'         => $user_id,
				'first_name' => $user_info['first_name'] ?? '',
				'last_name'  => $user_info['last_name'] ?? ''
			]);

			// Meta fields
			foreach (['branch', 'location', 'designation', 'department', 'sap', 'profile_picture', 'manager_email'] as $meta_key) {
				if (isset($user_info[$meta_key])) {
					update_user_meta($user_id, $meta_key, $user_info[$meta_key]);
				}
			}

			// Set your custom cookie
			if (isset($user_info['i_s'], $user_info['i_s_expiry'])) {
				setcookie('i_s', $user_info['i_s'], $user_info['i_s_expiry'], "/", ".timesgroup.com", true, true);
			}

			// Log in user
			$user = get_user_by('id', $user_id); // example user
			if ($user) {
				wp_set_current_user($user_id);
				wp_set_auth_cookie($user_id, true);
				do_action('wp_login', $user->user_login, $user);
				// Redirect or reload
				wp_safe_redirect(home_url()); // or any URL
				exit;
			}
		} else {
			//wp_die("Invalid email or user info missing in Basic Auth response");
			header('WWW-Authenticate: Basic realm="Authentication Required"');
			exit;
		}

		// === NO AUTH ===
	} else {
		// No credentials found. Triggering browser auth prompt.
		header('WWW-Authenticate: Basic realm="Authentication Required"');
		echo '<script>window.close();</script>';
		exit;
	}
}

// Add custom columns to employee post type admin
add_filter('manage_employee_posts_columns', 'add_employee_custom_columns');
function add_employee_custom_columns($columns)
{
	$new_columns = array();
	foreach ($columns as $key => $value) {
		$new_columns[$key] = $value;
		if ($key === 'title') {
			$new_columns['email'] = 'Email';
			$new_columns['conversions'] = 'Conversions';
		}
	}
	return $new_columns;
}

// Populate custom columns with data
add_action('manage_employee_posts_custom_column', 'populate_employee_custom_columns', 10, 2);
function populate_employee_custom_columns($column, $post_id)
{
	switch ($column) {
		case 'email':
			$email = get_post_meta($post_id, 'email', true);
			echo esc_html($email);
			break;
		case 'conversions':
			$conversions = get_post_meta($post_id, 'conversions_number', true);
			echo esc_html($conversions);
			break;
	}
}

// Make custom columns sortable
add_filter('manage_edit-employee_sortable_columns', 'make_employee_columns_sortable');
function make_employee_columns_sortable($columns)
{
	$columns['email'] = 'email';
	$columns['conversions'] = 'conversions_number';
	return $columns;
}



// AJAX handlers for story management
add_action('wp_ajax_change_story_status', 'handle_change_story_status');
function handle_change_story_status()
{
	// Check permissions
	if (!current_user_can('manage_options')) {
		wp_die('Permission denied');
	}

	// Verify nonce
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'change_story_status')) {
		wp_die('Security check failed');
	}

	// Get parameters
	$story_id = intval($_POST['story_id']);
	$new_status = sanitize_text_field($_POST['new_status']);

	// Validate status
	$allowed_statuses = array('publish', 'pending', 'draft');
	if (!in_array($new_status, $allowed_statuses)) {
		wp_die('Invalid status');
	}

	// Update post status
	$result = wp_update_post(array(
		'ID' => $story_id,
		'post_status' => $new_status
	));

	if ($result) {
		echo 'success';
	} else {
		echo 'error';
	}
	wp_die();
}

add_action('wp_ajax_delete_story', 'handle_delete_story');
function handle_delete_story()
{
	// Check permissions
	if (!current_user_can('manage_options')) {
		wp_die('Permission denied');
	}

	// Verify nonce
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'delete_story')) {
		wp_die('Security check failed');
	}

	// Get story ID
	$story_id = intval($_POST['story_id']);

	// Delete the post
	$result = wp_delete_post($story_id, true); // true = force delete

	if ($result) {
		echo 'success';
	} else {
		echo 'error';
	}
	wp_die();
}

/**
 * Get all revisions for a specific employee
 * @param int $employee_id The original employee post ID
 * @return array Array of revision posts
 */
function get_employee_revisions($employee_id)
{
	$revisions = get_posts(array(
		'post_type' => 'employee',
		'post_parent' => $employee_id,
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'post_date',
		'order' => 'DESC'
	));

	return $revisions;
}

/**
 * Get employee revision history with detailed changes
 * @param int $employee_id The original employee post ID
 * @return array Array with revision history
 */
function get_employee_revision_history($employee_id)
{
	$original_employee = get_post($employee_id);
	if (!$original_employee || $original_employee->post_type !== 'employee') {
		return false;
	}

	$revisions = get_employee_revisions($employee_id);
	$history = array();

	// Add original employee as first entry
	$history[] = array(
		'post_id' => $employee_id,
		'post_title' => $original_employee->post_title,
		'email' => get_post_meta($employee_id, 'email', true),
		'conversions' => get_post_meta($employee_id, 'conversions_number', true),
		'function' => get_post_meta($employee_id, 'employee_category_name', true),
		'date' => $original_employee->post_date,
		'type' => 'original',
		'changes' => 'Original employee record'
	);

	// Add revisions
	foreach ($revisions as $revision) {
		$history[] = array(
			'post_id' => $revision->ID,
			'post_title' => $revision->post_title,
			'email' => get_post_meta($revision->ID, 'email', true),
			'conversions' => get_post_meta($revision->ID, 'conversions_number', true),
			'function' => get_post_meta($revision->ID, 'employee_category_name', true),
			'date' => $revision->post_date,
			'type' => 'revision',
			'changes' => get_post_meta($revision->ID, 'revision_changes', true),
			'revision_date' => get_post_meta($revision->ID, 'revision_date', true)
		);
	}

	return $history;
}

/**
 * Display employee revision history in a formatted table
 * @param int $employee_id The original employee post ID
 * @return string HTML formatted revision history
 */
function display_employee_revision_history($employee_id)
{
	$history = get_employee_revision_history($employee_id);
	if (!$history) {
		return '<p>No revision history found for this employee.</p>';
	}

	$output = '<div class="employee-revision-history">';
	$output .= '<h3>Employee Revision History</h3>';
	$output .= '<table class="wp-list-table widefat fixed striped">';
	$output .= '<thead><tr>';
	$output .= '<th>Date</th>';
	$output .= '<th>Type</th>';
	$output .= '<th>Name</th>';
	$output .= '<th>Email</th>';
	$output .= '<th>Conversions</th>';
	$output .= '<th>Function</th>';
	$output .= '<th>Changes</th>';
	$output .= '</tr></thead>';
	$output .= '<tbody>';

	foreach ($history as $entry) {
		$row_class = $entry['type'] === 'revision' ? 'revision-row' : 'original-row';
		$output .= '<tr class="' . $row_class . '">';
		$output .= '<td>' . date('Y-m-d H:i:s', strtotime($entry['date'])) . '</td>';
		$output .= '<td>' . ucfirst($entry['type']) . '</td>';
		$output .= '<td>' . esc_html($entry['post_title']) . '</td>';
		$output .= '<td>' . esc_html($entry['email']) . '</td>';
		$output .= '<td>' . esc_html($entry['conversions']) . '</td>';
		$output .= '<td>' . esc_html($entry['function']) . '</td>';
		$output .= '<td>' . esc_html($entry['changes']) . '</td>';
		$output .= '</tr>';
	}

	$output .= '</tbody></table></div>';

	return $output;
}

/**
 * Get all employees with their revision counts
 * @return array Array of employees with revision information
 */
function get_employees_with_revision_counts()
{
	$employees = get_posts(array(
		'post_type' => 'employee',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key' => 'email',
				'compare' => 'EXISTS'
			)
		)
	));

	$result = array();
	foreach ($employees as $employee) {
		// Only include original employees (not revisions)
		if ($employee->post_parent == 0) {
			$revision_count = count(get_employee_revisions($employee->ID));
			$result[] = array(
				'post_id' => $employee->ID,
				'name' => $employee->post_title,
				'email' => get_post_meta($employee->ID, 'email', true),
				'conversions' => get_post_meta($employee->ID, 'conversions_number', true),
				'function' => get_post_meta($employee->ID, 'employee_category_name', true),
				'revision_count' => $revision_count,
				'last_updated' => $employee->post_modified
			);
		}
	}

	return $result;
}


add_action('wp_ajax_employee_csv_import', 'handle_employee_csv_import');
add_action('wp_ajax_nopriv_employee_csv_import', 'handle_employee_csv_import');
function handle_employee_csv_import()
{

	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);
	if (! current_user_can('manage_options')) {
		wp_die('<div class="error-message"><strong>Error:</strong> Permission denied. You do not have sufficient permissions to perform this action.</div>');
	}
	if (! isset($_POST['employee_csv_nonce']) || ! wp_verify_nonce($_POST['employee_csv_nonce'], 'employee_csv_import')) {
		wp_die('<div class="error-message"><strong>Error:</strong> Security check failed. Please refresh the page and try again.</div>');
	}
	if (! isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
		$upload_error = '';
		if (isset($_FILES['csv_file']['error'])) {
			switch ($_FILES['csv_file']['error']) {
				case UPLOAD_ERR_INI_SIZE:
					$upload_error = 'File size exceeds PHP upload limit.';
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$upload_error = 'File size exceeds form upload limit.';
					break;
				case UPLOAD_ERR_PARTIAL:
					$upload_error = 'File was only partially uploaded.';
					break;
				case UPLOAD_ERR_NO_FILE:
					$upload_error = 'No file was uploaded.';
					break;
				default:
					$upload_error = 'File upload failed with error code: ' . $_FILES['csv_file']['error'];
			}
		}
		wp_die('<div class="error-message"><strong>Error:</strong> CSV file upload failed. ' . $upload_error . '</div>');
	}

	// Validate file type
	$file_info = pathinfo($_FILES['csv_file']['name']);
	if (strtolower($file_info['extension']) !== 'csv') {
		wp_die('<div class="error-message"><strong>Error:</strong> Invalid file type. Please upload a CSV file.</div>');
	}

	// ✅ Validate CSV header BEFORE uploading to media library
	$temp_file = $_FILES['csv_file']['tmp_name'];

	// Detect file encoding and convert to UTF-8 if needed
	$file_content = file_get_contents($temp_file);
	if (function_exists('mb_detect_encoding')) {
		$encoding = mb_detect_encoding($file_content, 'UTF-8, ISO-8859-1, Windows-1252', true);
		if ($encoding && $encoding !== 'UTF-8') {
			$file_content = mb_convert_encoding($file_content, 'UTF-8', $encoding);
			file_put_contents($temp_file, $file_content);
		}
	} else {
		// Fallback: just assume UTF-8
		$file_content = utf8_encode($file_content);
		file_put_contents($temp_file, $file_content);
	}

	// Remove BOM if present
	$file_content = preg_replace('/^\xEF\xBB\xBF/', '', $file_content);
	file_put_contents($temp_file, $file_content);

	$handle = fopen($temp_file, 'r');
	if (!$handle) {
		wp_die('<div class="error-message"><strong>Error:</strong> Could not open CSV file.</div>');
	}

	// Expected header in exact order
	$expected_header = ['Employee Name', 'Email', 'Full Function', 'No. of Conversions'];

	// Get first row (header) for validation
	$header = fgetcsv($handle, 0, ',', '"', '\\');
	fclose($handle);

	if ($header === FALSE) {
		wp_die('<div class="error-message"><strong>Error:</strong> CSV file is empty or invalid.</div>');
	}

	// Clean header by removing BOM and trimming spaces
	$header = array_map(function ($col) {
		// Remove BOM from first column if present (multiple patterns for different BOM types)
		$col = preg_replace('/^[\xEF\xBB\xBF\xFE\xFF\x00\x00\xFE\xFF\xFF\xFE\x00\x00]/', '', $col);
		$col = preg_replace('/^ï»¿/', '', $col); // Remove UTF-8 BOM as visible characters
		return trim($col);
	}, $header);

	// Check exact match
	if ($header !== $expected_header) {
		wp_die('<div class="error-message"><strong>Error:</strong> CSV header does not match. Expected: ' . implode(', ', $expected_header) . '<br>Found: ' . implode(', ', $header) . '</div>');
	}

	/**
	 * ✅ Now upload file to Media Library (only if header validation passes)
	 */
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/media.php');
	require_once(ABSPATH . 'wp-admin/includes/image.php');

	$attachment_id = media_handle_upload('csv_file', 0);
// 	if (is_wp_error($attachment_id)) {
// 		wp_die('<div class="error-message"><strong>Error:</strong> Error uploading CSV to media library: ' . $attachment_id->get_error_message() . '</div>');
// 	}

	// Get the path of the uploaded file from Media Library
	$file = get_attached_file($attachment_id);

	$handle = fopen($file, 'r');
	if (!$handle) {
		wp_die('<div class="error-message"><strong>Error:</strong> Could not open uploaded CSV file.</div>');
	}

	$row = 0;
	$imported = 0;
	$errors = array();
	$debug_info = array();
	$date = current_time('mysql'); // WordPress-safe date
	while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== FALSE) {
		$row++;
		if ($row == 1) continue; // Skip header row

		// Debug: Log raw data
		$debug_info[] = "Row {$row} raw data: " . print_r($data, true);

		// Validate required fields
		if (count($data) < 4) {
			$errors[] = "Row {$row}: Insufficient data columns (found " . count($data) . ", need 4)";
			continue;
		}

		// Clean and validate each field
		$employee_name = trim($data[0]);
		$email = trim($data[1]);
		$full_function = trim($data[2]);
		$conversions_raw = trim($data[3]);

		// Debug: Log cleaned data
		$debug_info[] = "Row {$row} cleaned: Name='{$employee_name}', Email='{$email}', Conversions='{$conversions_raw}', Function='{$full_function}'";

		// Validate data
		if (empty($employee_name)) {
			$errors[] = "Row {$row}: Employee name is required";
			continue;
		}

		if (empty($email) || !is_email($email)) {
			$errors[] = "Row {$row}: Valid email is required (got: '{$email}')";
			continue;
		}

		// Handle conversions - allow both numeric and text
		$conversions = 0;
		if (is_numeric($conversions_raw)) {
			$conversions = intval($conversions_raw);
		} else {
			// Try to extract number from text
			preg_match('/\d+/', $conversions_raw, $matches);
			if (!empty($matches)) {
				$conversions = intval($matches[0]);
			}
		}

		if ($conversions < 0) {
			$errors[] = "Row {$row}: Conversions must be a positive number (got: {$conversions})";
			continue;
		}

		if (empty($full_function)) {
			$errors[] = "Row {$row}: Full function is required";
			continue;
		}

		// Sanitize data for database
		$employee_name = sanitize_text_field($employee_name);
		$email = sanitize_email($email);
		$full_function = sanitize_text_field($full_function);


		// Check if employee already exists by email
		$existing_posts = get_posts(array(
			'post_type' => 'employee',
			'meta_query' => array(
				array(
					'key' => 'email',
					'value' => $email,
					'compare' => '='
				)
			),
			'post_status' => 'publish',
			'posts_per_page' => 1
		));


		if (!empty($existing_posts)) {

			// echo '<pre>';
			// print_r($existing_posts);
			// echo '</pre>';

			// Get existing employee data for comparison
			$existing_post = $existing_posts[0];
			$existing_post_id = $existing_post->ID;			

			$existing_title = $existing_post->post_title;
			$existing_email = get_post_meta($existing_post_id, 'email', true);
			$existing_conversions = get_post_meta($existing_post_id, 'conversions_number', true);
			$existing_terms = wp_get_post_terms($existing_post_id, 'employee-category', array('fields' => 'names'));
			$existing_function = !empty($existing_terms) ? $existing_terms[0] : '';

			// Check if there are any changes
			$has_changes = false;
			$changes_log = array();

			if ($existing_title !== $employee_name) {
				$has_changes = true;
				$changes_log[] = "Name: '{$existing_title}' → '{$employee_name}'";
			}
			if ($existing_email !== $email) {
				$has_changes = true;
				$changes_log[] = "Email: '{$existing_email}' → '{$email}'";
			}
			if ($existing_conversions != $conversions) {
				$has_changes = true;
				$changes_log[] = "Conversions: '{$existing_conversions}' → '{$conversions}'";
			}
			if ($existing_function !== $full_function) {
				$has_changes = true;
				$changes_log[] = "Function: '{$existing_function}' → '{$full_function}'";
			}



			if ($has_changes) {


				// Get the original creation date of the existing post
				$original_post_date = $existing_post->post_date;
				$original_post_date_gmt = $existing_post->post_date_gmt;

				// First, create a revision post with the OLD data (previous state)
				$revision_post_id = wp_insert_post(array(
					'post_type'   => 'revision',
					'post_title'  => $existing_title, // Use old name
					'post_status' => 'inherit',
					'post_parent' => $existing_post_id, // Set parent ID for revision tracking
					'post_date' => $original_post_date, // Use original creation date
					'post_date_gmt' => $original_post_date_gmt, // Use original GMT date
					'post_content' => 'Previous state before CSV import. Changes: ' . implode(', ', $changes_log)
				));

				if ($revision_post_id && !is_wp_error($revision_post_id)) {
					// Store OLD data in revision post
					update_post_meta($revision_post_id, 'email', $existing_email);
					update_post_meta($revision_post_id, 'conversions_number', $existing_conversions);
					update_post_meta($revision_post_id, 'revision_date', current_time('mysql'));
					update_post_meta($revision_post_id, 'revision_changes', implode('; ', $changes_log));
					update_post_meta($revision_post_id, 'revision_type', 'previous_state');

					// Store old taxonomy term in revision
					if (!empty($existing_function)) {
						$old_term = term_exists($existing_function, 'employee-category');
						if (!$old_term) {
							$old_term = wp_insert_term($existing_function, 'employee-category');
						}
						if (!is_wp_error($old_term)) {
							wp_set_post_terms($revision_post_id, array(intval($old_term['term_id'])), 'employee-category');
							update_post_meta($revision_post_id, 'employee_category_term_id', intval($old_term['term_id']));
							update_post_meta($revision_post_id, 'employee_category_name', $existing_function);
						}
					}

					$debug_info[] = "Row {$row}: Created revision with previous data for employee '{$employee_name}'";
				}



				// Now update the ORIGINAL post with NEW data
				$datanew = wp_update_post(array(
					'ID' => $existing_post_id,
					'post_title' => $employee_name, // New name
					'post_date' => current_time('mysql'), // Update date to current
					'post_date_gmt' => current_time('mysql', 1) // Update GMT date
				));

				// Update original post with NEW data
				update_post_meta($existing_post_id, 'email', $email);
				update_post_meta($existing_post_id, 'conversions_number', $conversions);

				// Create or get taxonomy term for NEW data
				$term = term_exists($full_function, 'employee-category');
				if (!$term) {
					$term = wp_insert_term($full_function, 'employee-category');
					if (!is_wp_error($term)) {
						$debug_info[] = "Row {$row}: Created new taxonomy term '{$full_function}' with ID {$term['term_id']}";
					}
				}
				if (!is_wp_error($term)) {
					wp_set_post_terms($existing_post_id, array(intval($term['term_id'])), 'employee-category');
					update_post_meta($existing_post_id, 'employee_category_term_id', intval($term['term_id']));
					update_post_meta($existing_post_id, 'employee_category_name', $full_function);
					update_term_meta(intval($term['term_id']), 'current_date', $date);
					$debug_info[] = "Row {$row}: Applied new taxonomy term '{$full_function}' (ID: {$term['term_id']}) to original post";
				} else {
					$errors[] = "Row {$row}: Failed to create/get taxonomy term '{$full_function}'";
				}

				$post_id = $existing_post_id;
				$action = 'updated_with_revision';
				$debug_info[] = "Row {$row}: '{$post_id}'Updated original post with new data and created revision with previous data for employee '{$employee_name}'";


			} else {
				// No changes, just update the existing post (but keep original date)
				wp_update_post(array(
					'ID' => $existing_post_id,
					'post_title' => $employee_name,
					'post_date' => current_time('mysql'), // Update date to current
					'post_date_gmt' => current_time('mysql', 1) // Update GMT date
				));
				$post_id = $existing_post_id;
				$action = 'no_changes';
				$debug_info[] = "Row {$row}: No changes detected for employee '{$employee_name}' - keeping original date";
			}




		} else {
			// Create new employee post with current import date
			$post_id = wp_insert_post(array(
				'post_type'   => 'employee',
				'post_title'  => $employee_name,
				'post_status' => 'publish',
				'post_date' => $date, // Use current import date
				'post_date_gmt' => current_time('mysql', 1) // Use current GMT date
			));
			$action = 'created';
		}

		if ($post_id && !is_wp_error($post_id)) {
			// Update post meta fields
			update_post_meta($post_id, 'email', $email);
			update_post_meta($post_id, 'conversions_number', $conversions);

			// Create or get taxonomy term with enhanced tracking
			$term = term_exists($full_function, 'employee-category');
			if (!$term) {
				$term = wp_insert_term($full_function, 'employee-category');

				if (!is_wp_error($term)) {
					$debug_info[] = "Row {$row}: Created new taxonomy term '{$full_function}' with ID {$term['term_id']}";
				} else {
					$errors[] = "Row {$row}: Failed to create taxonomy term '{$full_function}'";
				}
			}

			if (!is_wp_error($term)) {
				wp_set_post_terms($post_id, array(intval($term['term_id'])), 'employee-category');
				$debug_info[] = "Row {$row}: Applied taxonomy term '{$full_function}' (ID: {$term['term_id']}) to employee post";

				// Store taxonomy term ID in post meta for easier tracking
				update_post_meta($post_id, 'employee_category_term_id', intval($term['term_id']));
				update_post_meta($post_id, 'employee_category_name', $full_function);
				update_term_meta(intval($term['term_id']), 'current_date', $date);
			} else {
				$errors[] = "Row {$row}: Failed to get/create taxonomy term '{$full_function}'";
			}

			$imported++;
			$debug_info[] = "Row {$row}: Successfully {$action} employee '{$employee_name}' with email '{$email}' and {$conversions} conversions";
		} else {
			$errors[] = "Row {$row}: Failed to create/update employee post";
		}
	}
	fclose($handle);
	$response = "<strong>Import complete!</strong> Imported {$imported} employees.<br>CSV File ID in Media Library: {$attachment_id}<br><br>";

	// Store the last upload date
	update_option('last_employee_csv_upload_date', current_time('mysql'));

	// Add revision information if any revisions were created
	$revision_count = 0;
	$updated_count = 0;
	foreach ($debug_info as $info) {
		if (strpos($info, 'Updated original post with new data') !== false) {
			$updated_count++;
		}
		if (strpos($info, 'Created revision with previous data') !== false) {
			$revision_count++;
		}
	}

	if ($updated_count > 0) {
		$response .= "<strong>Updated Employees:</strong> {$updated_count} employee records were updated with new data and previous data was saved as revisions.<br>";
	}

	if (!empty($errors)) {
		$response .= "<br><strong>Errors:</strong><br>";
		foreach ($errors as $error) {
			$response .= htmlspecialchars($error) . "<br>";
		}
		$response .= "<br>";
	}
	echo $response;
}

add_action('wp_ajax_department_csv_import', 'handle_department_csv_import');
add_action('wp_ajax_nopriv_department_csv_import', 'handle_department_csv_import');
function handle_department_csv_import()
{
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);

	if (! current_user_can('manage_options')) {
		wp_die('<div class="error-message"><strong>Error:</strong> Permission denied.</div>');
	}
	if (! isset($_POST['department_csv_nonce']) || ! wp_verify_nonce($_POST['department_csv_nonce'], 'department_csv_import')) {
		wp_die('<div class="error-message"><strong>Error:</strong> Security check failed.</div>');
	}
	if (! isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
		$upload_error = '';
		if (isset($_FILES['csv_file']['error'])) {
			switch ($_FILES['csv_file']['error']) {
				case UPLOAD_ERR_INI_SIZE:
					$upload_error = 'File size exceeds PHP upload limit.';
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$upload_error = 'File size exceeds form upload limit.';
					break;
				case UPLOAD_ERR_PARTIAL:
					$upload_error = 'File was only partially uploaded.';
					break;
				case UPLOAD_ERR_NO_FILE:
					$upload_error = 'No file was uploaded.';
					break;
				default:
					$upload_error = 'File upload failed with error code: ' . $_FILES['csv_file']['error'];
			}
		}
		wp_die('<div class="error-message"><strong>Error:</strong> CSV file upload failed. ' . $upload_error . '</div>');
	}

	// Validate file type
	$file_info = pathinfo($_FILES['csv_file']['name']);
	if (strtolower($file_info['extension']) !== 'csv') {
		wp_die('<div class="error-message"><strong>Error:</strong> Invalid file type. Please upload a CSV file. File uploaded: ' . htmlspecialchars($_FILES['csv_file']['name']) . '</div>');
	}

	// ✅ Validate CSV header BEFORE uploading to media library
	$temp_file = $_FILES['csv_file']['tmp_name'];

	// Detect file encoding and convert to UTF-8 if needed
	$file_content = file_get_contents($temp_file);
	if (function_exists('mb_detect_encoding')) {
		$encoding = mb_detect_encoding($file_content, 'UTF-8, ISO-8859-1, Windows-1252', true);
		if ($encoding && $encoding !== 'UTF-8') {
			$file_content = mb_convert_encoding($file_content, 'UTF-8', $encoding);
			file_put_contents($temp_file, $file_content);
		}
	} else {
		$file_content = utf8_encode($file_content);
		file_put_contents($temp_file, $file_content);
	}

	// Remove BOM if present
	$file_content = preg_replace('/^\xEF\xBB\xBF/', '', $file_content);
	file_put_contents($temp_file, $file_content);

	$handle = fopen($temp_file, 'r');
	if (!$handle) {
		wp_die('<div class="error-message"><strong>Error:</strong> Could not open CSV file.</div>');
	}

	// Updated expected header
	$expected_header = [
		'S. No.', 
		'Function', 
		'Short Func', 
		'Total', 
		'Active User', 
		'Clubbed Department', 
		'Clubbed Short Form'
	];

	// Get first row (header) for validation
	$header = fgetcsv($handle, 0, ',', '"', '\\');
	fclose($handle);

	if ($header === FALSE) {
		wp_die('<div class="error-message"><strong>Error:</strong> CSV file is empty or invalid.</div>');
	}

	// Clean header
	$header = array_map(function ($col) {
		$col = preg_replace('/^[\xEF\xBB\xBF\xFE\xFF\x00\x00\xFE\xFF\xFF\xFE\x00\x00]/', '', $col);
		$col = preg_replace('/^ï»¿/', '', $col);
		return trim($col);
	}, $header);

	if ($header !== $expected_header) {
		wp_die('<div class="error-message"><strong>Error:</strong> CSV header does not match. Expected: ' . implode(', ', $expected_header) . '<br>Found: ' . implode(', ', $header) . '</div>');
	}

	// ✅ Upload file after header validation
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/media.php');
	require_once(ABSPATH . 'wp-admin/includes/image.php');

	$attachment_id = media_handle_upload('csv_file', 0);
	if (is_wp_error($attachment_id)) {
		wp_die('<div class="error-message"><strong>Error:</strong> Error uploading CSV to media library: ' . $attachment_id->get_error_message() . '</div>');
	}

	$file = get_attached_file($attachment_id);
	$handle = fopen($file, 'r');
	if (!$handle) {
		wp_die('<div class="error-message"><strong>Error:</strong> Could not open uploaded CSV file.</div>');
	}

	$row = 0;
	$imported = 0;
	$updated = 0;
	$errors = array();
	$debug_info = array();

	while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== FALSE) {
		$row++;
		if ($row == 1) continue; // Skip header

		$debug_info[] = "Row {$row} raw data: " . print_r($data, true);

		if (count($data) < 7) {
			$errors[] = "Row {$row}: Insufficient data columns (need 7: Function, Short Func, Total, Active User, Clubbed Department, Clubbed Short Form)";
			continue;
		}

		$serial_number   = trim($data[0]);
		$department_name = trim($data[1]);
		$short_function  = trim($data[2]);
		$total_count     = isset($data[3]) ? trim($data[3]) : '';
		$active_user     = isset($data[4]) ? trim($data[4]) : '';
		$clubbed_func    = isset($data[5]) ? trim($data[5]) : '';
		$clubbed_short   = isset($data[6]) ? trim($data[6]) : '';

		$debug_info[] = "Row {$row} cleaned: Dept='{$department_name}', Short='{$short_function}', Total='{$total_count}', Active='{$active_user}', Clubbed='{$clubbed_func}', Clubbed Short='{$clubbed_short}'";

		if (empty($department_name)) {
			$errors[] = "Row {$row}: Function (department name) is required";
			continue;
		}
		if (empty($short_function)) {
			$errors[] = "Row {$row}: Short Func is required";
			continue;
		}
		if (empty($clubbed_func)) {
			$errors[] = "Row {$row}: Clubbed Func is required";
			continue;
		}
		if (empty($clubbed_short)) {
			$errors[] = "Row {$row}: Clubbed Short Func is required";
			continue;
		}
		if (!is_numeric($total_count)) {
			$errors[] = "Row {$row}: Total must be a number (got: '{$total_count}')";
			continue;
		}

		$department_name = sanitize_text_field($department_name);
		$short_function  = sanitize_text_field($short_function);
		$total_count     = intval($total_count);
		$clubbed_func    = sanitize_text_field($clubbed_func);
		$clubbed_short   = sanitize_text_field($clubbed_short);

		$department_slug = sanitize_title($short_function);
		$existing_term   = term_exists($department_name, 'employee-category');
		$date            = current_time('mysql');

		if ($existing_term) {
			$term_id = $existing_term['term_id'];
			$result = wp_update_term($term_id, 'employee-category', ['name' => $department_name]);

			if (!is_wp_error($result)) {
				update_term_meta($term_id, 'short_function', $short_function);
				update_term_meta($term_id, 'total_count', $total_count);
				update_term_meta($term_id, 'serial_number', $serial_number);
				update_term_meta($term_id, 'active_users', $active_user);

				// ✅ New fields inserted here
				update_term_meta($term_id, 'clubbed_functions', $clubbed_func);
				update_term_meta($term_id, 'clubbed_short_form', $clubbed_short);

				update_term_meta($term_id, 'current_date', $date);
				$updated++;
			} else {
				$errors[] = "Row {$row}: Failed to update department '{$department_name}': " . $result->get_error_message();
			}
		} else {
			$result = wp_insert_term($department_name, 'employee-category', []);
			if (!is_wp_error($result)) {
				$term_id = $result['term_id'];
				update_term_meta($term_id, 'short_function', $short_function);
				update_term_meta($term_id, 'total_count', $total_count);
				update_term_meta($term_id, 'serial_number', $serial_number);
				update_term_meta($term_id, 'active_users', $active_user);

				// ✅ New fields inserted here
				update_term_meta($term_id, 'clubbed_functions', $clubbed_func);
				update_term_meta($term_id, 'clubbed_short_form', $clubbed_short);

				update_term_meta($term_id, 'current_date', $date);
				$imported++;
			} else {
				$errors[] = "Row {$row}: Failed to create department '{$department_name}': " . $result->get_error_message();
			}
		}
	}
	fclose($handle);

	$response = "<strong>Department Import complete!</strong><br>";
	$response .= "New departments created: {$imported}<br>";
	$response .= "Existing departments updated: {$updated}<br>";
	$response .= "CSV File ID in Media Library: {$attachment_id}<br><br>";

	echo $response;
}


// Hide Add Media button and Visual/Text tabs for admins
function acf_hide_wysiwyg_buttons_for_admin()
{
	if (current_user_can('administrator')) {
		echo '<style>
            /* Hide Add Media button */
            .acf-field-wysiwyg .wp-media-buttons {
                display: none !important;
            }

            /* Hide Visual/Text tabs */
            .acf-field-wysiwyg .wp-editor-tabs {
                display: none !important;
            }
        </style>';
	}
}
add_action('acf/input/admin_head', 'acf_hide_wysiwyg_buttons_for_admin');

function restrict_post_title_chars( $valid, $value, $field, $input ){
	if( $field['name'] === '_post_title' ) {
		if( preg_match('/[^a-zA-Z @#\-\_?.]/', $value) ) {
			$valid = 'Only letters and @ # - _ are allowed in the title.';
		}
	}
	return $valid;
}
add_filter('acf/validate_value', 'restrict_post_title_chars', 10, 4);

// function restrict_wysiwyg_chars($valid, $value, $field, $input)
// {
// 	if ($field['type'] === 'wysiwyg') {
// 		// Allow letters, space, enter (\n), @, #, -, _, ?, .
// 		if (preg_match('/[^a-zA-Z @#\-\_?.\n]/', strip_tags($value))) {
// 			$valid = 'Only letters, spaces, enters, and @ # - _ ? . are allowed.';
// 		}
// 	}
// 	return $valid;
// }
// add_filter('acf/validate_value', 'restrict_wysiwyg_chars', 10, 4);


