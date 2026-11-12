<?php

/**
 * Template Name: View Admin Story Dashboard
 */
get_header();

// ==== Security Check ====
if (! current_user_can('manage_options')) {
	wp_die('No permission');
}

if(isset($_GET['id']) && !empty($_GET['id'])){
	$post_id = $_GET['id'];
}else{
	return;
}

?>
<style>
	/* --- Single Success Story Styles --- */
	.action-btn {
		padding: 6px 12px;
		margin: 2px;
		border: none;
		border-radius: 6px;
		font-size: 0.8rem;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.3s ease;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}
	/* Wrapper */
	.success-story-wrapper {
		max-width: 1366px;
		margin: auto;
		padding: 30px 20px;
		background:#fff
	}

	/* Post Title */
	.success-story-wrapper h1 {
		font-size: 48px;
		color: #333;
		margin: 20px 0px;
	}

	/* Avatar */
	.stories-avatar {
		background: #c8102e; /* Strong red */
		color: #fff;
		width: 45px;
		height: 45px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
		font-weight: bold;
		font-size: 1rem;
	}

	/* Author Info */
	.success-story-wrapper small {
		color: #777; /* Medium grey */
		font-size: 0.9rem;
	}

	/* Section Labels */
	.success-story-wrapper strong {
		color: #555; /* Dark grey instead of red */
	}

	/* Project Details Paragraphs */
	.success-story-wrapper p {
		margin: 0 0 12px 0;
		color: #666;
		font-size: 18px;
	}
	
	.stories-table td.story-author {
    white-space: normal;   /* allow wrapping */
    word-break: break-word; /* break long words/emails */
    max-width: 200px;      /* optional: limit column width */
}


	/* Horizontal Lines */
	.success-story-wrapper hr {
		border: none;
		border-top: 1px solid #eee;
	}

	/* Buttons */
	.like-button,
	.share-button {
		background-color: transparent !important; /* removes WP default */
		color: #555 !important;
		text-decoration: none !important;
		padding: 8px 14px;
		border-radius: 4px;
		cursor: pointer;
		font-size: 0.95rem;
		transition: all 0.25s ease;
		display: inline-flex;
		align-items: center;
		gap: 6px; /* space between icon and text */
		border: none !important;
	}

	.like-button:hover,
	.share-button:hover {
		background-color: transparent !important; /* also transparent on hover */
		color: #000 !important; /* black text on hover */
		text-decoration: none !important;
	}


	/* Responsive Layout Fix */
	@media (max-width: 768px) {
		.success-story-wrapper {
			padding: 20px;
		}
		.success-story-wrapper h1 {
			font-size: 1.5rem;
		}
		.row {
			display: flex;
			flex-direction: column;
		}
	}
</style>


<div class="success-story-wrapper">
	<?php
	$current_post_id = $post_id;
	$status  = get_post_status($current_post_id);

	$query = new WP_Query(array(
		'post_type'   => 'success-story',
		'p'           => $current_post_id,
		'post_status' => array('publish', 'pending', 'draft'),
	));

	if ($query->have_posts()) :
	while ($query->have_posts()) : $query->the_post();

	$author_email = get_the_author_meta('user_email');
	$author_name  = get_the_author();
	$first_name = get_the_author_meta('first_name', $query->post_author);
	$last_name  = get_the_author_meta('last_name', $query->post_author);

	if (empty($first_name) && empty($last_name)) {
		$author_name = get_the_author_meta('display_name', $query->post_author);
	} else {
		$author_name = trim($first_name . ' ' . $last_name);
	}
	$date         = get_the_date();
	$likes        = get_post_meta(get_the_ID(), 'likes', true);
	$subscribers  = get_post_meta(get_the_ID(), 'subscribers', true);
	$fields       = get_fields(get_the_ID());

	// Avatar initials
	$initials = '';
	if ($author_name) {
		$words = explode(' ', trim($author_name));
		foreach ($words as $w) {
			$initials .= strtoupper($w[0]);
		}
	} else {
		$initials = strtoupper(substr($author_email, 0, 2));
	}
	?>

	<div style="display:flex; flex-wrap:wrap; gap:30px; align-items:flex-start;">

		<!-- Left: Image -->
		<div style="flex:1; min-width:300px;">
			<?php
			if (has_post_thumbnail()) {
				the_post_thumbnail('large', ['style' => 'width:100%; border-radius:6px;']);
			} elseif (!empty($fields)) {
				foreach ($fields as $field) {
					if (is_array($field) && isset($field['url'])) {
						echo '<img src="' . esc_url($field['url']) . '" alt="" style="width:100%; border-radius:6px;" />';
						break;
					}
					if (is_numeric($field)) {
						$img_url = wp_get_attachment_image_url($field, 'large');
						if ($img_url) {
							echo '<img src="' . esc_url($img_url) . '" alt="" style="width:100%; border-radius:6px;" />';
							break;
						}
					}
				}
			}
			?>
		</div>

		<!-- Right: Details -->
		<div style="flex:2; min-width:300px;">

			<!-- Avatar & Info -->
			<div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
				<div class="stories-avatar">
					<?php echo esc_html($initials); ?>
				</div>
				<div>
					<small>Posted by: <?php echo esc_html($author_name); ?></small> / 
					<small><?php echo esc_html($date); ?></small>
				</div>
			</div>
			<!-- Title -->
			<h1><?php the_title(); ?></h1>
			<!-- Business Value / Function -->		
			<?php
			$function_name = '—'; 
			$department    = get_field('department', get_the_ID());

			if (!empty($department)) {
				if (is_object($department) && isset($department->term_id)) {
					$term_id = intval($department->term_id);
				} elseif (is_array($department) && isset($department['term_id'])) {
					$term_id = intval($department['term_id']);
				} elseif (is_numeric($department)) {
					$term_id = intval($department);
				} else {
					$term_id = 0;
				}

				if ($term_id) {
					$term = get_term($term_id);
					if (!is_wp_error($term) && $term) {
						$short_function = get_term_meta($term_id, 'short_function', true);
						$function_name = $short_function ?: $term->name;
					}
				}
			}
			?>
			<div class="emp_category">
				<p>
					<strong>Function:</strong> <?php echo esc_html($function_name); ?>
				</p>
			</div>

			<!-- Project Details -->
			<div>
				<p><strong>Project Details:</strong></p>
				<?php the_content(); ?>
			</div>
			<!-- Like & Share -->
			<?php if (current_user_can('manage_options')) : ?>
			<?php if ($status === 'pending') : ?>
			<button class="action-btn approve-btn" onclick="changeStatus(<?php echo $current_post_id; ?>, 'publish')">
				Approve
			</button>
			<?php endif; ?>
			<?php if ($status === 'publish') : ?>
			<button class="action-btn reject-btn" onclick="changeStatus(<?php echo $current_post_id; ?>, 'pending')">
				Unpublish
			</button>
			<?php endif; ?>
			<button class="action-btn delete-btn" onclick="deleteStory(<?php echo $current_post_id; ?>)">
				Delete
			</button>
			<?php else : ?>
			<span class="no-permission">No actions available</span>
			<?php endif; ?>
		</div>
	</div>

	<?php
	endwhile;
	wp_reset_postdata();
	else :
	echo '<p>Sorry, this success story could not be found.</p>';
	endif;
	?>
</div>

<?php get_footer(); ?>
<script>
	function changeStatus(storyId, newStatus) {
		if (!confirm('Are you sure you want to change the status of this story?')) {
			return;
		}

		// Create form data
		const formData = new FormData();
		formData.append('action', 'change_story_status');
		formData.append('story_id', storyId);
		formData.append('new_status', newStatus);
		formData.append('nonce', '<?php echo wp_create_nonce('change_story_status'); ?>');

		// Send AJAX request
		fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
			method: 'POST',
			body: formData
		})
			.then(response => response.text())
			.then(data => {
			if (data.includes('success')) {
				alert('Status updated successfully!');
				location.reload(); // Refresh the page to show updated status
			} else {
				alert('Error updating status: ' + data);
			}
		})
			.catch(error => {
			console.error('Error:', error);
			alert('Error updating status. Please try again.');
		});
	}

	function deleteStory(storyId) {
		if (!confirm('Are you sure you want to delete this story? This action cannot be undone.')) {
			return;
		}

		// Create form data
		const formData = new FormData();
		formData.append('action', 'delete_story');
		formData.append('story_id', storyId);
		formData.append('nonce', '<?php echo wp_create_nonce('delete_story'); ?>');

		// Send AJAX request
		fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
			method: 'POST',
			body: formData
		})
			.then(response => response.text())
			.then(data => {
			if (data.includes('success')) {
				alert('Story deleted successfully!');
				location.reload(); // Refresh the page to remove the deleted story
			} else {
				alert('Error deleting story: ' + data);
			}
		})
			.catch(error => {
			console.error('Error:', error);
			alert('Error deleting story. Please try again.');
		});
	}
</script>