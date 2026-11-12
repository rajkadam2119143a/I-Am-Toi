<?php
/**
 * Template Name: Success Stories
 * Description: Displays dynamic success stories from the database.
 */
defined('ABSPATH') || exit;

get_header();

?>
<style>
	.btn-wrap{		
		margin-bottom: 20px;
	}
	

</style>
<div class="stories-wrapper">
	<h1 class="stories-title">Success Stories</h1>
	<p class="stories-subtitle">Learn from top performers and share your own success strategies</p>
	<div class="btn-wrap">
		<a href="<?php echo esc_url( site_url( '/add-stories/' ) ); ?>" class="btn-button">
			Add Success Stories
		</a>
	</div>

	<div class="stories-grid">
		<?php
		$query = new WP_Query(array(
			'post_type' => 'success-story',
			'posts_per_page' => -1,
			'post_status' => 'publish',
		));

		if ($query->have_posts()) :
		while ($query->have_posts()) : $query->the_post();

		$author_name = get_the_author();
		$first_name = get_the_author_meta('first_name', $query->post_author);
		$last_name  = get_the_author_meta('last_name', $query->post_author);

		if (empty($first_name) && empty($last_name)) {
			$author_name = get_the_author_meta('display_name', $query->post_author);
		} else {
			$author_name = trim($first_name . ' ' . $last_name);
		}

		$author_role = get_the_author_meta('description');
		$name_parts = explode(' ', $author_name);
		$initials = '';
		foreach ($name_parts as $part) {
			$initials .= strtoupper(substr($part, 0, 1));
		}
		$subscribers = get_post_meta(get_the_ID(), 'subscribers_gained', true);
		$likes = get_post_meta(get_the_ID(), 'likes', true);
		$comments = get_post_meta(get_the_ID(), 'comments', true);
		$post_link = get_permalink();
		?>

		<div class="stories-card">
    <a href="<?php echo esc_url($post_link); ?>" target="_blank" rel="noopener" class="stories-card-link">
        <div class="stories-header">
            <div class="stories-header-left">
                <?php
                // Featured image or fallback
                $img_style = 'width:60px; height:60px; border-radius:8px; object-fit:cover; box-shadow:0 2px 6px rgba(0,0,0,0.08); outline:none;';

                if (has_post_thumbnail()) {
                    echo get_the_post_thumbnail(get_the_ID(), 'thumbnail', [
                        'style' => $img_style
                    ]);
                } else {
                    $fields = get_fields(get_the_ID());
                    $image_found = false;

                    if (!empty($fields)) {
                        foreach ($fields as $field) {
                            if (is_array($field) && isset($field['url'])) {
                                echo '<img src="' . esc_url($field['url']) . '" alt="" style="' . $img_style . '" />';
                                $image_found = true;
                                break;
                            }
                            if (is_numeric($field)) {
                                $img_url = wp_get_attachment_image_url($field, 'thumbnail');
                                if ($img_url) {
                                    echo '<img src="' . esc_url($img_url) . '" alt="" style="' . $img_style . '" />';
                                    $image_found = true;
                                    break;
                                }
                            }
                        }
                    }

                    if (!$image_found) {
						$default_img = site_url() . '/wp-content/uploads/2025/08/middleImage.jpg';
						echo '<img src="' . esc_url($default_img) . '" alt="Default Image" style="' . $img_style . '" />';
					}

                }
                ?>

                <div class="stories-userinfo">
					<?php
					$designation   = ''; 
					
					// If designation belongs to user meta, fetch via user ID
					$user_id = get_post_field('post_author', get_the_ID()); 
					$designation = get_field('designation', 'user_' . $user_id); 
					?>
                    <strong><?php echo esc_html($author_name); ?></strong><small><?php if (!empty($designation)) : ?>
						- <?php echo esc_html($designation); ?>
					<?php endif; ?></small>
                    <?php
                    $department_name = '';
                    $department = get_field('department', get_the_ID());

                    if (!empty($department)) {
                        if (is_object($department) && isset($department->name)) {
                            $department_name = $department->name;
                        } elseif (is_array($department) && isset($department['name'])) {
                            $department_name = $department['name'];
                        } elseif (is_numeric($department)) {
                            $term = get_term($department);
                            if (!is_wp_error($term) && $term) {
                                $department_name = $term->name;
                            }
                        }
                    }
                    ?>
                    <span><?php echo esc_html($author_role ?: ($department_name ?: 'Subscriber')); ?></span>
                    <small><?php echo get_the_date(); ?></small>
                </div>
            </div>
        </div>

        <h3 class="stories-heading"><?php the_title(); ?></h3>
        <p class="stories-text"><?php echo wp_trim_words(get_the_content(), 40); ?></p>

        <div class="stories-divider"></div>
    </a>


			<div class="stories-footer">
				<div class="stories-footer-left">
					<!-- Like button -->
					<?php echo do_shortcode('[posts_like_dislike id='.get_the_ID().']');?>


					<!--                                 <button class="share-button" data-url="<?php //echo esc_url(get_permalink()); ?>">
🔗 Share
</button> -->
					<!--                                 <span>
<a href="https://twitter.com/intent/tweet?url=<?php //echo urlencode($post_link); ?>&text=<?php //echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener" style="color:inherit;">🔗 Tweet</a>
</span>
<span>
<a href="https://www.facebook.com/sharer/sharer.php?u=<?php// echo urlencode($post_link); ?>" target="_blank" rel="noopener" style="color:inherit;">🔗 Facebook</a>
</span> -->
				</div>
				<!--                             <div class="subscribers-gained">+<?php //echo esc_html($subscribers ?: 0); ?> subscribers gained</div> -->
			</div>
		</div>
		<?php
		endwhile;
		wp_reset_postdata();   
		?>

		<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>