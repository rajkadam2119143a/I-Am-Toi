<?php
/**
 * Template Name: Submit Success Story
 * Description: Frontend form for users to submit success stories (uses ACF).
 */
defined('ABSPATH') || exit;

// Process ACF front-end form before any HTML
if ( function_exists('acf_form_head') ) {
	acf_form_head();
}

get_header();
?>

<style>
	:root {
		/* Text Colors */
		--color-text-main: #222;
		--color-text-muted: #555;
		--color-text-secondary: #777;
		--color-text-subtle: #999;
		--color-text-light: #fff;

		/* Brand Colors */
		--color-primary-red: #c8102e;
		--color-primary-red-dark: #9b2525;
		--color-primary-red-light: #d73b3c;

		/* Backgrounds & Dividers */
		--color-bg-main: #fff;
		--color-bg-muted: #fdf3f3;
		--color-divider: #f2dcdc;
	}

	/* Wrapper styling */
	.stories-wrapper {
		max-width: 800px;
		margin: 40px auto;
		padding: 30px 20px;
		background: var(--color-bg-main);
		border-radius: 14px;
		box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
		font-family: 'Segoe UI', sans-serif;
		border: 1px solid var(--color-divider);
	}

	/* Title */
	.stories-title {
		font-size: 2.5rem;
		text-align: center;
		margin-bottom: 15px;
		color: var(--color-text-main);
		font-weight: 700;
	}

	/* Paragraph text styling */
	.stories-wrapper p {
		font-size: 0.98rem;
		color: var(--color-text-muted);
		line-height: 1.65;
		margin-bottom: 18px;
	}

	/* Notices */
	.notice {
		background: #ffeeba;
		color: #856404;
		padding: 12px 15px;
		border-radius: 6px;
		margin-bottom: 20px;
		font-size: 0.95rem;
		border: 1px solid rgba(133, 100, 4, 0.2);
	}
	.notice.success {
		background: #d4edda;
		color: #155724;
		border: 1px solid rgba(21, 87, 36, 0.2);
	}

	/* ACF form fields wrapper */
	/* .acf-fields-wrapper {
	display: flex;
	flex-direction: column;
	gap: 20px;
	} */

	#mceu_108,#mceu_60,#mceu_61,#mceu_62,#mceu_63,#mceu_64,#mceu_65,#mceu_68,#mceu_67,#mceu_66,#mceu_56,#wp-acf-editor-39-editor-tools{
		display:none
	}
	/* Field styles */
	.acf-field input[type="text"],
	.acf-field input[type="email"],
	.acf-field textarea,
	.acf-field select {
		width: 100%;
		padding: 12px 14px;
		font-size: 1rem;
		border: 1px solid var(--color-divider);
		border-radius: 8px;
		background: #fafafa;
		color: var(--color-text-main);
		transition: all 0.2s ease;
		font-family: inherit;
	}

	.acf-field input:focus,
	.acf-field textarea:focus,
	.acf-field select:focus {
		border-color: var(--color-primary-red-light);
		background: #fff;
		outline: none;
		box-shadow: 0 0 5px rgba(200, 16, 46, 0.3);
	}
	
	.acf-taxonomy-field {
	  pointer-events: none;  /* disables clicking */
	  opacity: 0.6;          /* make it look disabled */
	}


	/* Universal light grey button style with smaller size */
	button,
	input[type="button"],
	input[type="reset"],
	.wp-core-ui .button,
	.wp-core-ui .button-primary,
	.wp-core-ui .button-secondary,
	.acf-button,
	.mce-toolbar button,
	.mce-toolbar .mce-btn {
		background: #333 !important; /* Light grey */
		color: #fff !important;
		font-weight: 500 !important;
		font-size: 0.85rem !important; /* Smaller text */
		padding: 4px 10px !important; /* Smaller padding */
		border: 1px solid #cccccc !important;
		border-radius: 6px !important;
		cursor: pointer !important;
		transition: all 0.3s ease !important;
		text-decoration: none !important;
		box-shadow: none !important;
		line-height: 1.2 !important;
	}

	/* Hover state for grey buttons */
	button:hover,
	input[type="button"]:hover,
	input[type="reset"]:hover,
	.wp-core-ui .button:hover,
	.wp-core-ui .button-primary:hover,
	.wp-core-ui .button-secondary:hover,
	.acf-button:hover,
	.mce-toolbar button:hover,
	.mce-toolbar .mce-btn:hover {
		background: #cccccc !important;
		border-color: #b3b3b3 !important;
	}

	/* TinyMCE toolbar buttons */
.mce-toolbar button,
.mce-toolbar .mce-btn {
    background: #333 !important;
    color: #fff !important; /* Make icon/text white */
    font-size: 0.75rem !important;
    padding: 5px 9px !important;
    border-radius: 6px !important;
    font-weight: 500 !important;
    border: none !important;
    cursor: pointer !important;
    transition: all 0.3s ease;
    display: inline-block;
}

/* Force inner icon/text to white */
.mce-toolbar .mce-ico,
.mce-toolbar .mce-txt {
    color: #fff !important;
    fill: #fff !important;
}

/* Hover effect */
.mce-toolbar button:hover,
.mce-toolbar .mce-btn:hover {
    background: var(--color-primary-red-dark) !important;
    color: #fff !important;
}


	/* Hover border for form fields */
	.acf-field input:hover,
	.acf-field textarea:hover,
	.acf-field select:hover {
		border-color: var(--color-primary-red-light);
	}

	/* ✅ Updated ACF submit button */
	.acf-form-submit {
		text-align: center;
		margin-top: 25px;
	}

	.acf-form-submit input[type="submit"] {
		background: var(--color-primary-red) !important;
		color: #fff !important;
		font-size: 1.1rem !important;
		padding: 14px 36px !important;
		border-radius: 10px !important;
		font-weight: 600 !important;
		border: none !important;
		cursor: pointer !important;
		transition: all 0.3s ease;
		display: inline-block;
	}

	/* Hover effect for submit */
	.acf-form-submit input[type="submit"]:hover {
		background: var(--color-primary-red-dark) !important;
	}

	/* Hide the top red ACF error banner only */
	.acf-notice.acf-error,
	.acf-notice.-error {
		display: none !important;
	}

	/* Keep inline field errors visible */
	.acf-field .acf-error-message {
		display: block !important;
		color: #c8102e; /* brand red */
		font-size: 0.85rem;
		margin-top: 6px;
	}


	/* Mobile responsiveness */
	@media (max-width: 600px) {
		.stories-wrapper {
			padding: 20px 15px;
		}
		.stories-title {
			font-size: 1.8rem;
		}
		.acf-form-submit input[type="submit"] {
			width: 100% !important;  /* Full width on mobile */
		}
	}

</style>
<div class="stories-wrapper">
	<h1 class="stories-title">Submit Your Story</h1>

	<?php
	if ( isset($_GET['submitted']) && $_GET['submitted'] == '1' ) {
		echo '<div class="notice success">Thanks — your story has been submitted and is pending review.</div>';
	}

	if ( ! function_exists('acf_form') ) {
		echo '<div class="notice">Please install/activate Advanced Custom Fields plugin to use the submission form.</div>';
	} else {
		if ( ! is_user_logged_in() ) {
			echo '<p>Please <a href="' . esc_url( wp_login_url( get_permalink() ) ) . '">log in</a> to submit a story.</p>';
		} else {

			$current_user_id   = get_current_user_id();
			$department        = get_user_meta($current_user_id, 'department', true);
			$department_clean  = preg_replace('/^Bccl\s*-\s*/i', '', $department);

			// 🔹 Step 1: Get the term for this department
			$term = get_term_by('name', $department_clean, 'employee-category');

			// ✅ Get all terms from taxonomy
			$all_terms = get_terms([
				'taxonomy'   => 'employee-category',
				'hide_empty' => false,
			]);

			if (!is_wp_error($all_terms) && !empty($all_terms)) {

				// ✅ Sort terms by count (highest first)
				usort($all_terms, function($a, $b) {
					return $b->count <=> $a->count;
				});

				// ✅ Take top 50 departments
				$top50_terms = array_slice($all_terms, 0, 50);
				$top50_ids   = wp_list_pluck($top50_terms, 'term_id');

				// ✅ Check if current user’s department exists
				if ( ! $term || is_wp_error($term) ) {
					echo '<div class="notice error">Your department <strong>' . esc_html($department_clean) . '</strong> is not in the list. Please contact the administrator.</div>';
					return;
				}

				// ✅ Check if current user’s department is in top 50
				if (!in_array($term->term_id, $top50_ids)) {
					echo '<div class="notice error">Your function <strong>' 
						. esc_html($term->name) 
						. '</strong> is not in the Top 50 by employee count, so you are not allowed to submit a story.</div>';
					return; 
				}

				// 🔹 Step 2: Check if user is linked in Employee post type
				$employee_posts = get_posts([
					'post_type'  => 'employee',
					'meta_query' => [
						[
							'key'   => 'user_id',   // 🔥 adjust if your ACF field key is different
							'value' => $current_user_id,
						],
					],
					'posts_per_page' => 1,
				]);

				if (empty($employee_posts)) {
					echo '<div class="notice error">You are not registered in the Employee list. Please contact the administrator.</div>';
					return;
				}

				// ✅ Allowed → Show form

				// Pre-fill taxonomy
				add_filter('acf/load_value/key=taxonomy_68943a47f2637', function($value, $post_id, $field) use ($term) {
					if (empty($value)) {
						return array($term->term_id);
					}
					return $value;
				}, 10, 3);

				// Pre-fill Success Story Department field
				add_filter('acf/load_value/key=field_6899bece2ee65', function($value, $post_id, $field) use ($term) {
					if (empty($value)) {
						return array($term->term_id);
					}
					return $value;
				}, 10, 3);

				$field_group_id = 'group_6899be04c00a4';

				acf_form(array(
					'id'                 => 'submit-success-story',
					'post_id'            => 'new_post',
					'new_post'           => array(
						'post_type'   => 'success-story',
						'post_status' => 'pending',
						'post_author' => $current_user_id,
					),
					'field_groups'       => array($field_group_id),
					'submit_value'       => 'Submit Story',
					'return'             => add_query_arg('submitted','1', get_permalink()),
					'html_before_fields' => '<div class="acf-fields-wrapper">',
					'html_after_fields'  => '</div>',
					'post_title'         => true,
					'post_content'       => true,
				));
			}
		}
	}
	?>
</div>


<?php get_footer(); ?>