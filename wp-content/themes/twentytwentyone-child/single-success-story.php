<?php
/**
 * Template Name: Single Success Story
 */
defined('ABSPATH') || exit;
get_header();
?>

<style>
	/* --- Single Success Story Styles --- */

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
	}
</style>


<div class="success-story-wrapper">
	<?php
	$current_post_id = get_the_ID();

	$query = new WP_Query(array(
		'post_type'   => 'success-story',
		'p'           => $current_post_id,
		'post_status' => 'publish',
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
	// Large image style (main preview)
	$main_img_style = 'width:100%; border-radius:6px;';

	if (has_post_thumbnail()) {
		the_post_thumbnail('large', ['style' => $main_img_style]);
	} else {
		$fields = get_fields(get_the_ID());
		$image_found = false;

		if (!empty($fields)) {
			foreach ($fields as $field) {
				if (is_array($field) && isset($field['url'])) {
					echo '<img src="' . esc_url($field['url']) . '" alt="" style="' . $main_img_style . '" />';
					$image_found = true;
					break;
				}
				if (is_numeric($field)) {
					$img_url = wp_get_attachment_image_url($field, 'large');
					if ($img_url) {
						echo '<img src="' . esc_url($img_url) . '" alt="" style="' . $main_img_style . '" />';
						$image_found = true;
						break;
					}
				}
			}
		}

		// Fallback default image (if nothing found)
		if (!$image_found) {
			$default_img = site_url() . '/wp-content/uploads/2025/08/middleImage.jpg';
			echo '<img src="' . esc_url($default_img) . '" alt="Default Image" style="' . $main_img_style . '" />';
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
					<?php
					$designation   = ''; 
					
					// If designation belongs to user meta, fetch via user ID
					$user_id = get_post_field('post_author', get_the_ID()); 
					$designation = get_field('designation', 'user_' . $user_id); 
					?>
					<small>Posted by: <?php echo esc_html($author_name); ?><?php if (!empty($designation)) : ?>
			- <?php echo esc_html($designation); ?>
		<?php endif; ?></small>
					<div><small><?php echo esc_html($date); ?></small></div>
				</div>
			</div>
			<!-- Title -->
			<h1><?php the_title(); ?></h1>
			<!-- Business Value / Function -->		
			<?php
$function_name = 'Subscriber'; 


$department = get_field('department', get_the_ID());

// Department term name
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
			$function_name = $term->name;
		}
	}
}

?>

<div class="emp_category">
	<p>
		<strong>Function:</strong> 
		<?php echo esc_html($function_name); ?> 
		
	</p>
</div>



			<!-- Project Details -->
			<div>
<!-- 				<p><strong>Project Details:</strong></p> -->
				<?php the_content(); ?>
			</div>
			<!-- Like & Share -->

			<div style="display:flex; align-items:center;">
				<?php echo do_shortcode('[posts_like_dislike id='.get_the_ID().']');?>

<!-- 				<button class="share-button" data-url="<?php //echo esc_url(get_permalink()); ?>">
					🔗 Share
				</button> -->
			</div>
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

<script>
	document.addEventListener('DOMContentLoaded', function () {	

		// Share button
		document.querySelectorAll('.share-button').forEach(button => {
			button.addEventListener('click', function () {
				let url = this.getAttribute('data-url');

				if (navigator.share) {
					navigator.share({
						title: document.title,
						url: url
					}).catch(console.error);
				} else {
					navigator.clipboard.writeText(url).then(() => {
						alert('Link copied to clipboard!');
					});
				}
			});
		});
	});
</script>

<?php get_footer(); ?>
