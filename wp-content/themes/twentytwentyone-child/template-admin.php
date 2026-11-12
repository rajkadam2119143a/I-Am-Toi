<?php

/**
 * Template Name: Admin Dashboard
 */

get_header();

// ==== Security Check ====
if (! current_user_can('manage_options')) {
	wp_die('No permission');
}
?>

<style>
	.csv-upload-box {
		background: #fff;
		padding: 20px;
		border: 2px dashed #ccc;
		border-radius: 10px;
		width: 100%;
		margin: 20px auto;
		text-align: center;
		max-width: 800px;
	}

	.csv-upload-box input[type=file] {
		margin: 10px 0;
	}

	.csv-result {
		background: #fff;
		padding: 10px;
		margin-top: 15px;
		border: 1px solid #ddd;
		text-align: left;
	}

	.row {
		display: flex;
	}

	.leftside ul {
		list-style: none;
		padding: 10px 20px;
		background: #fff;
	}

	.leftside ul li a {
		font-size: 18px;
	}

	.upload-container {
		display: flex;
		flex-direction: column;
		gap: 2rem;
	}

	/* Upload Box */
	.upload-box {
		position: relative;
		padding: 3rem 2rem;
		text-align: center;
		border: 3px dashed rgba(255, 255, 255, 0.3);
		border-radius: 20px;
		transition: all 0.3s ease;
		background: rgba(255, 255, 255, 0.05);
		backdrop-filter: blur(20px);
		cursor: pointer;
	}

	.upload-box.dragover {
		border-color: #f093fb;
		background: rgba(240, 147, 251, 0.1);
		transform: scale(1.02);
	}

	.upload-box.uploading {
		border-color: #f093fb;
		background: rgba(240, 147, 251, 0.05);
	}

	.upload-content {
		transition: all 0.3s ease;
	}

	.upload-box.uploading .upload-content {
		opacity: 0;
		visibility: hidden;
	}

	.upload-box.uploading .upload-progress {
		opacity: 1;
		visibility: visible;
	}

	.upload-icon {
		margin-bottom: 1.5rem;
		color: #c8102e; /* Brand red */
	}

	.upload-title {
		font-size: 1.5rem;
		font-weight: 600;
		margin-bottom: 0.5rem;
		color: #000;
	}

	.upload-subtitle {
		color: #000;
		margin-bottom: 2rem;
		font-size: 12px;
	}

	.upload-button {
		background: linear-gradient(135deg, #a60d26 0%, #c8102e 100%);
		color: white;
		border: none;
		padding: 1rem 2rem;
		border-radius: 12px;
		font-size: 1rem;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.3s ease;
		box-shadow: 0 4px 15px rgba(200, 16, 46, 0.3);
	}

	.upload-button:hover {
		transform: translateY(-2px);
		box-shadow: 0 8px 25px rgba(240, 147, 251, 0.4);
	}

	/* Progress */
	.upload-progress {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		opacity: 0;
		visibility: hidden;
		transition: all 0.3s ease;
		text-align: center;
	}

	.progress-circle {
		position: relative;
		display: inline-block;
		margin-bottom: 1rem;
	}

	.progress-ring {
		transform: rotate(-90deg);
	}

	.progress-bar {
		transition: stroke-dashoffset 0.3s ease;
		stroke-linecap: round;
	}

	.progress-text {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		font-weight: 600;
		font-size: 1rem;
		color: #fff;
	}

	.progress-label {
		color: #e2e8f0;
		font-size: 0.9rem;
	}

	/* Files Preview */
	.files-preview {
		background: rgba(255, 255, 255, 0.1);
		backdrop-filter: blur(20px);
		border: 1px solid rgba(255, 255, 255, 0.2);
		border-radius: 20px;
		padding: 2rem;
		opacity: 0;
		visibility: hidden;
		transform: translateY(20px);
		transition: all 0.4s ease;
		box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
	}

	.files-preview.show {
		opacity: 1;
		visibility: visible;
		transform: translateY(0);
	}

	.preview-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 1.5rem;
	}

	.preview-title {
		font-size: 1.3rem;
		font-weight: 600;
		color: #fff;
	}

	.add-more-btn {
		background: rgba(255, 255, 255, 0.1);
		color: #fff;
		border: 1px solid rgba(255, 255, 255, 0.2);
		padding: 0.5rem 1rem;
		border-radius: 8px;
		font-size: 0.85rem;
		cursor: pointer;
		transition: all 0.3s ease;
	}

	.add-more-btn:hover {
		background: rgba(255, 255, 255, 0.2);
	}

	.files-list {
		display: grid;
		gap: 1rem;
	}

	.file-item {
		display: flex;
		align-items: center;
		gap: 1rem;
		padding: 1rem;
		background: rgba(255, 255, 255, 0.05);
		border: 1px solid rgba(255, 255, 255, 0.1);
		border-radius: 12px;
		transition: all 0.3s ease;
		backdrop-filter: blur(10px);
	}

	.file-item:hover {
		background: rgba(255, 255, 255, 0.1);
		transform: translateY(-2px);
	}

	.file-preview {
		width: 60px;
		height: 60px;
		object-fit: cover;
		border-radius: 8px;
		background: rgba(255, 255, 255, 0.1);
	}

	.file-info {
		flex: 1;
	}

	.file-name {
		font-weight: 500;
		color: #fff;
		margin-bottom: 0.25rem;
	}

	.file-size {
		font-size: 0.85rem;
		color: #e2e8f0;
	}

	.file-status {
		margin-right: 1rem;
	}

	.status-icon {
		font-size: 1.5rem;
	}

	.status-uploading {
		color: #f093fb;
	}

	.status-success {
		color: #48bb78;
	}

	.file-actions {
		display: flex;
		gap: 0.5rem;
	}

	.file-action {
		background: rgba(255, 255, 255, 0.1);
		border: 1px solid rgba(255, 255, 255, 0.2);
		color: #fff;
		width: 32px;
		height: 32px;
		border-radius: 6px;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		transition: all 0.3s ease;
	}

	.file-action:hover {
		background: rgba(255, 255, 255, 0.2);
	}

	.file-action.delete:hover {
		background: rgba(245, 87, 108, 0.2);
		border-color: #f5576c;
	}

	/* Upload Complete */
	.upload-complete {
		background: rgba(255, 255, 255, 0.1);
		backdrop-filter: blur(20px);
		border: 1px solid rgba(255, 255, 255, 0.2);
		border-radius: 20px;
		padding: 3rem 2rem;
		text-align: center;
		box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
	}

	.complete-header {
		margin-bottom: 2rem;
	}
	
	.stories-table td.story-author {
    white-space: normal;   /* allow wrapping */
    word-break: break-word; /* break long words/emails */
    max-width: 200px;      /* optional: limit column width */
	 font-size: 0.85rem;   
}


	.success-icon {
		width: 80px;
		height: 80px;
		background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 2.5rem;
		color: white;
		margin: 0 auto 1.5rem;
		animation: successBounce 0.6s ease;
	}

	@keyframes successBounce {
		0% {
			transform: scale(0);
		}

		50% {
			transform: scale(1.1);
		}

		100% {
			transform: scale(1);
		}
	}

	.complete-title {
		font-size: 1.8rem;
		font-weight: 600;
		color: #fff;
		margin-bottom: 0.5rem;
	}

	.complete-subtitle {
		color: #000;
		font-size: 1rem;
	}

	.complete-actions {
		display: flex;
		gap: 1rem;
		justify-content: center;
	}

	.new-upload-btn,
	.view-files-btn {
		padding: 1rem 1.5rem;
		border: none;
		border-radius: 12px;
		font-size: 1rem;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.3s ease;
	}

	.new-upload-btn {
		background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
		color: white;
		box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
	}

	.new-upload-btn:hover {
		transform: translateY(-2px);
		box-shadow: 0 8px 25px rgba(240, 147, 251, 0.4);
	}

	.view-files-btn {
		background: rgba(255, 255, 255, 0.1);
		color: #fff;
		border: 1px solid rgba(255, 255, 255, 0.2);
	}

	.view-files-btn:hover {
		background: rgba(255, 255, 255, 0.2);
	}

	/* Success Animation */
	.success-animation {
		text-align: center;
	}

	.success-animation .success-icon {
		font-size: 3rem;
		margin-bottom: 1rem;
		animation: successPulse 1.5s infinite;
	}

	@keyframes successPulse {

		0%,
		100% {
			transform: scale(1);
		}

		50% {
			transform: scale(1.05);
		}
	}

	.success-text {
		font-size: 1.3rem;
		font-weight: 600;
		color: #fff;
	}

	/* Error States */
	.file-item.error {
		border: 1px solid #fecaca;
		background: #fef2f2;
	}

	.error-message {
		color: #dc2626;
		font-size: 0.75rem;
		margin-top: 0.25rem;
	}

	/* Responsive Design */
	@media (max-width: 640px) {
		body {
			padding: 1rem;
		}

		.upload-box {
			padding: 2rem 1rem;
			margin: 1rem;
		}

		.upload-title {
			font-size: 1.25rem;
		}

		.files-preview {
			padding: 1rem;
		}

		.file-item {
			flex-direction: column;
			text-align: center;
			gap: 1rem;
		}

		.file-preview {
			margin-right: 0;
		}

		.file-actions {
			justify-content: center;
		}
	}

	/* Loading Animation */
	@keyframes spin {
		from {
			transform: rotate(0deg);
		}

		to {
			transform: rotate(360deg);
		}
	}

	.loading {
		animation: spin 1s linear infinite;
	}

	/* Drag and Drop Visual Feedback */
	.upload-box::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(99, 102, 241, 0.1));
		opacity: 0;
		transition: opacity 0.3s ease;
		border-radius: 13px;
	}

	.upload-box.dragover::before {
		opacity: 1;
	}

	/* Animation for slide out */
	@keyframes slideOut {
		from {
			opacity: 1;
			transform: translateY(0);
		}

		to {
			opacity: 0;
			transform: translateY(-20px);
		}
	}

	/* Tab Styles */
	.tab-content {
		flex: 1;
		padding: 20px;
	}

	.tab-pane {
		display: none;
	}

	.tab-pane.active {
		display: block;
	}

	.nav-link {
		cursor: pointer;
		transition: all 0.3s ease;
	}

	.nav-link:hover {
		background-color: rgba(255, 255, 255, 0.1);
	}

	.nav-link.active {
		background-color: rgba(255, 255, 255, 0.2);
		font-weight: bold;
	}

	/* Stories Management Styles */
	.stories-management {
		background: #fff;
		border-radius: 15px;
		padding: 30px;
		box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
	}

	.stories-management h2 {
		color: #333;
		margin-bottom: 25px;
		font-size: 1.8rem;
		font-weight: 600;
	}

	.stories-table-container {
		overflow-x: auto;
		border-radius: 10px;
		border: 1px solid #e1e5e9;
	}

	.stories-table {
		width: 100%;
		border-collapse: collapse;
		background: #fff;
	}

	.stories-table th {
		background: #f8f9fa;
		padding: 15px 12px;
		text-align: left;
		font-weight: 600;
		color: #495057;
		border-bottom: 1px solid #dee2e6;
		font-size: 0.9rem;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		border: 0px;
	}

	.stories-table td {
		padding: 15px 12px;
		border-bottom: 0px solid #e9ecef;
		vertical-align: top;
		border: 0px;
	}

	.stories-table tr:hover {
		background-color: #f8f9fa;
	}

	.story-title {
		font-weight: 500;
		color: #2c3e50;
		max-width: 200px;
	}

	.story-content {
		max-width: 300px;
		color: #6c757d;
		font-size: 0.9rem;
		line-height: 1.4;
	}

	.story-status {
		text-align: center;
	}

	.status-badge {
		padding: 6px 12px;
		border-radius: 20px;
		font-size: 0.8rem;
		font-weight: 500;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.status-publish {
		background: #d4edda;
		color: #155724;
		border: 1px solid #c3e6cb;
	}

	.status-pending {
		background: #fff3cd;
		color: #856404;
		border: 1px solid #ffeaa7;
	}

	.status-draft {
		background: #f8d7da;
		color: #721c24;
		border: 1px solid #f5c6cb;
	}

	.story-date {
		color: #6c757d;
		font-size: 0.85rem;
		text-align: center;
	}

	.story-actions {
		text-align: center;
		white-space: nowrap;
	}

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

	.edit-btn {
		background: #007bff;
		color: white;
	}

	.edit-btn:hover {
		background: #0056b3;
	}

	.approve-btn {
		background: #28a745;
		color: white;
	}

	.approve-btn:hover {
		background: #1e7e34;
	}

	.reject-btn {
		background: #ffc107;
		color: #212529;
	}

	.reject-btn:hover {
		background: #e0a800;
	}

	.delete-btn {
		background: #dc3545;
		color: white;
	}

	.delete-btn:hover {
		background: #c82333;
	}

	.no-permission {
		color: #6c757d;
		font-size: 0.8rem;
		font-style: italic;
	}

	.no-stories {
		text-align: center;
		padding: 40px;
		color: #6c757d;
	}

	.no-stories p {
		font-size: 1.1rem;
		margin: 0;
	}

	/* Pagination Styles */
	.pagination-container {
		margin-top: 30px;
		padding: 20px 0;
		border-top: 1px solid #e9ecef;
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex-wrap: wrap;
		gap: 15px;
	}

	.pagination-info {
		color: #6c757d;
		font-size: 0.9rem;
	}

	.pagination-links {
		display: flex;
		align-items: center;
		gap: 8px;
		flex-wrap: wrap;
	}

	.page-link {
		padding: 8px 12px;
		border: 1px solid #dee2e6;
		border-radius: 6px;
		text-decoration: none;
		color: #007bff;
		font-size: 0.9rem;
		font-weight: 500;
		transition: all 0.3s ease;
		background: #fff;
	}

	.page-link:hover {
		background: #007bff;
		color: #fff;
		border-color: #007bff;
		text-decoration: none;
	}

	.page-link.current {
		background: #007bff;
		color: #fff;
		border-color: #007bff;
	}

	.page-link.prev-link,
	.page-link.next-link {
		font-weight: 600;
	}

	.page-dots {
		color: #6c757d;
		padding: 8px 4px;
		font-weight: bold;
	}



	/* Error notifications */
	.error-notification {
		position: fixed;
		top: 20px;
		right: 20px;
		background: rgba(254, 226, 226, 0.95);
		color: #dc2626;
		padding: 1rem;
		border-radius: 12px;
		border: 1px solid rgba(252, 165, 165, 0.5);
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
		z-index: 1000;
		backdrop-filter: blur(10px);
		max-width: 300px;
		animation: slideInRight 0.3s ease;
	}

	/* Success notifications */
	.success-notification {
		position: fixed;
		top: 20px;
		right: 20px;
		background: rgba(209, 250, 229, 0.95);
		color: #059669;
		padding: 1rem;
		border-radius: 12px;
		border: 1px solid rgba(16, 185, 129, 0.5);
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
		z-index: 1000;
		backdrop-filter: blur(10px);
		max-width: 300px;
		animation: slideInRight 0.3s ease;
	}

	@keyframes slideInRight {
		from {
			transform: translateX(100%);
			opacity: 0;
		}

		to {
			transform: translateX(0);
			opacity: 1;
		}
	}

	@keyframes slideOutRight {
		from {
			transform: translateX(0);
			opacity: 1;
		}

		to {
			transform: translateX(100%);
			opacity: 0;
		}
	}

	/* Responsive pagination */
	@media (max-width: 768px) {
		.pagination-container {
			flex-direction: column;
			text-align: center;
		}

		.pagination-links {
			justify-content: center;
		}
		.row {
			display: flex;
			flex-direction: column;
		}
		.stories-table td {
			flex-direction: column;
		}
	}
</style>

<div class="container">
	<div class="row">
		<!-- Side Nav Bar Start-->
		<div class="side-navbar active-nav d-flex flex-column py-3 px-2 leftside" id="sidebar">
			<ul class="nav flex-column text-white w-100" role="tablist">
				<li class="active">
					<a class="nav-link active text-white" id="stories-tab" data-bs-toggle="tab" data-bs-target="#stories-setting" href="<?php echo get_permalink(119) . '?tab=stories-setting'; ?>" role="tab" aria-controls="stories-setting" aria-selected="true"> Success Stories</a>
				</li>
				<li class="">
					<a class="nav-link text-white" id="department-import-tab" data-bs-toggle="tab" data-bs-target="#department-import" href="<?php echo get_permalink(119) . '?tab=department-import'; ?>" role="tab" aria-controls="department-import" aria-selected="false">Upload Function Participations</a>
				</li>
				<li class="">
					<a class="nav-link text-white" id="layout-setting-tab" data-bs-toggle="tab" data-bs-target="#layout-setting" href="<?php echo get_permalink(119) . '?tab=layout-setting'; ?>" role="tab" aria-controls="layout-setting" aria-selected="false">Upload Subscription Count</a>
				</li>
			</ul>
		</div>
		<!-- Side Nav Bar End-->

		<!-- Tab Content Area -->
		<div class="tab-content rightside" id="tabContent">
			<!-- Success Stories Tab -->
			<div class="tab-pane fade show active" id="stories-setting" role="tabpanel" aria-labelledby="stories-tab">
				<div class="stories-management">
					<h2>Success Stories Management</h2>

					<?php
					// Pagination setup - check for page number in URL path and query parameters
					$paged = 1;
					
					// Check for page number in URL path (e.g., /admin/page/2/)
					$current_url = $_SERVER['REQUEST_URI'];
					if (preg_match('/\/page\/(\d+)\//', $current_url, $matches)) {
						$paged = max(1, intval($matches[1]));
					} elseif (isset($_GET['paged'])) {
						$paged = max(1, intval($_GET['paged']));
					} elseif (isset($_GET['page'])) {
						$paged = max(1, intval($_GET['page']));
					}
					$posts_per_page = 10;

					// Get success stories with pagination
					$args = array(
						'post_type' => 'success-story',
						'post_status' => array('publish', 'pending', 'draft'),
						'posts_per_page' => $posts_per_page,
						'paged' => $paged,
						'orderby' => 'date',
						'order' => 'DESC'
					);

					$success_stories_query = new WP_Query($args);
					$success_stories = $success_stories_query->posts;
					$total_posts = $success_stories_query->found_posts;
					$total_pages = ceil($total_posts / $posts_per_page);

					// Debug information (you can remove this later)
					// if (current_user_can('manage_options')) {
					// 	echo '<div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border: 1px solid #ccc; font-size: 12px;">';
					// 	echo '<strong>Debug Info:</strong><br>';
					// 	echo 'Current Page: ' . $paged . '<br>';
					// 	echo 'Posts per page: ' . $posts_per_page . '<br>';
					// 	echo 'Total posts found: ' . $total_posts . '<br>';
					// 	echo 'Total pages: ' . $total_pages . '<br>';
					// 	echo 'Posts on current page: ' . count($success_stories) . '<br>';
					// 	echo 'Current URL: ' . $_SERVER['REQUEST_URI'] . '<br>';
					// 	echo '</div>';
					// }

					if (!empty($success_stories)) : ?>
					<div class="stories-table-container">
						<table class="stories-table">
							<thead>
								<tr>
									<th>Title</th>
									<th>Posted by</th>
									<th>Content</th>
									<th>Status</th>
									<th>Date</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($success_stories as $story) : ?>
								<tr>
									<td class="story-title">
										<strong><?php echo esc_html($story->post_title); ?></strong>
									</td>
									<td class="story-author">
										
											<?php 
											$author_id   = $story->post_author;
											$first_name  = get_the_author_meta( 'first_name', $author_id );
											$last_name   = get_the_author_meta( 'last_name', $author_id );

											if ( !empty($first_name) || !empty($last_name) ) {
												echo esc_html( trim($first_name . ' ' . $last_name) );
											} else {
												// fallback to display name if first/last not set
												echo esc_html( get_the_author_meta( 'display_name', $author_id ) );
											}

											?>
										
									</td>
									<td class="story-content">
										<?php echo esc_html(wp_trim_words($story->post_content, 5)); ?>
									</td>
									<td class="story-status">
										<span class="status-badge status-<?php echo $story->post_status; ?>">
											<?php echo ucfirst($story->post_status); ?>
										</span>
									</td>
									<td class="story-date">
										<?php echo get_the_date('M j, Y', $story->ID); ?>
									</td>
									<td class="story-actions">
										<?php if (current_user_can('manage_options')) : ?>

										<a class="action-btn approve-btn" href="<?php echo get_permalink(194).'?id='.$story->ID; ?>">
											View
										</a>
										<?php if ($story->post_status === 'pending') : ?>
										<button class="action-btn approve-btn" onclick="changeStatus(<?php echo $story->ID; ?>, 'publish')">
											Approve
										</button>
										<?php endif; ?>
										<?php if ($story->post_status === 'publish') : ?>
										<button class="action-btn reject-btn" onclick="changeStatus(<?php echo $story->ID; ?>, 'pending')">
											Unpublish
										</button>
										<?php endif; ?>
										<button class="action-btn delete-btn" onclick="deleteStory(<?php echo $story->ID; ?>)">
											Delete
										</button>
										<?php else : ?>
										<span class="no-permission">No actions available</span>
										<?php endif; ?>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>

					<?php if ($total_pages > 1) : ?>
					<div class="pagination-container">
						<div class="pagination-info">
							Showing <?php echo (($paged - 1) * $posts_per_page) + 1; ?> to <?php echo min($paged * $posts_per_page, $total_posts); ?> of <?php echo $total_posts; ?> stories
						</div>
						<div class="pagination-links">
							<?php
							// Get current URL and preserve existing parameters
							$current_url = $_SERVER['REQUEST_URI'];
							$url_parts = parse_url($current_url);
							$query_params = [];
							
							if (isset($url_parts['query'])) {
								parse_str($url_parts['query'], $query_params);
							}
							
							// Ensure tab parameter is preserved if not set
							if (!isset($query_params['tab'])) {
								$query_params['tab'] = 'stories-setting';
							}
							
							// Function to generate pagination URL
							function generate_pagination_url($page_num, $base_path, $query_params) {
								if ($page_num == 1) {
									// For page 1, remove /page/X/ from URL
									$path = preg_replace('/\/page\/\d+\//', '/', $base_path);
									if (substr($path, -1) !== '/') {
										$path .= '/';
									}
								} else {
									// For other pages, replace or add /page/X/
									$path = preg_replace('/\/page\/\d+\//', "/page/{$page_num}/", $base_path);
									if (!preg_match('/\/page\/\d+\//', $path)) {
										$path = str_replace('/admin/', "/admin/page/{$page_num}/", $path);
									}
								}
								
								$query_string = !empty($query_params) ? '?' . http_build_query($query_params) : '';
								return $path . $query_string;
							}
							
							// Previous page link
							if ($paged > 1) : 
								$prev_url = generate_pagination_url($paged - 1, $url_parts['path'], $query_params);
							?>
							<a href="<?php echo esc_url($prev_url); ?>" class="page-link prev-link">
								← Previous
							</a>
							<?php endif; ?>

							<?php
							// Page numbers
							$start_page = max(1, $paged - 2);
							$end_page = min($total_pages, $paged + 2);

							// Show first page if not in range
							if ($start_page > 1) : 
								$first_url = generate_pagination_url(1, $url_parts['path'], $query_params);
							?>
							<a href="<?php echo esc_url($first_url); ?>" class="page-link">1</a>
							<?php if ($start_page > 2) : ?>
							<span class="page-dots">...</span>
							<?php endif; ?>
							<?php endif; ?>

							<?php for ($i = $start_page; $i <= $end_page; $i++) : 
								$page_url = generate_pagination_url($i, $url_parts['path'], $query_params);
							?>
							<a href="<?php echo esc_url($page_url); ?>" class="page-link <?php echo ($i == $paged) ? 'current' : ''; ?>">
								<?php echo $i; ?>
							</a>
							<?php endfor; ?>

							<?php
							// Show last page if not in range
							if ($end_page < $total_pages) : ?>
							<?php if ($end_page < $total_pages - 1) : ?>
							<span class="page-dots">...</span>
							<?php endif; ?>
							<?php 
								$last_url = generate_pagination_url($total_pages, $url_parts['path'], $query_params);
							?>
							<a href="<?php echo esc_url($last_url); ?>" class="page-link"><?php echo $total_pages; ?></a>
							<?php endif; ?>

							<?php
							// Next page link
							if ($paged < $total_pages) : 
								$next_url = generate_pagination_url($paged + 1, $url_parts['path'], $query_params);
							?>
							<a href="<?php echo esc_url($next_url); ?>" class="page-link next-link">
								Next →
							</a>
							<?php endif; ?>
						</div>
					</div>
					<?php endif; ?>
					<?php else : ?>
					<div class="no-stories">
						<p>No success stories found.</p>
					</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Import Department Tab -->
			<div class="tab-pane fade" id="department-import" role="tabpanel" aria-labelledby="department-import-tab">
				<div class="csv-upload-box">
					<div class="upload-container">
						<div class="upload-box" id="deptUploadBox">
							<div class="upload-content">
								<div class="upload-icon">
									<svg width="64" height="64" viewBox="0 0 24 24" fill="none">
										<path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</div>
								<h3 class="upload-title">Drop CSV file here or click to upload</h3>
								<p class="upload-subtitle">Upload department CSV file with columns: S. No., Function, Short Func, Total</p>
								<button class="upload-button" type="button">Choose CSV File</button>
								<input type="file" id="deptFileInput" accept=".csv" hidden>
							</div>

							<div class="upload-progress" id="deptUploadProgress">
								<div class="progress-circle">
									<svg class="progress-ring" width="60" height="60">
										<circle cx="30" cy="30" r="25" stroke="#4a5568" stroke-width="4" fill="none" />
										<circle class="progress-bar" cx="30" cy="30" r="25" stroke="#f093fb" stroke-width="4" fill="none" stroke-dasharray="157" stroke-dashoffset="157" />
									</svg>
									<span class="progress-text">0%</span>
								</div>
								<p class="progress-label">Uploading and importing departments...</p>
							</div>
						</div>

						<div class="files-preview" id="deptFilesPreview" style="display:none">
							<div class="preview-header">
								<h4 class="preview-title">Selected CSV File</h4>
								<button class="add-more-btn" id="deptAddMoreBtn" style="display: none;">Choose Different File</button>
							</div>
							<div class="files-list" id="deptFilesList">
								<!-- File will be dynamically added here -->
							</div>
						</div>

						<div class="upload-complete" id="deptUploadComplete" style="display: none;">
							<div class="complete-header">
								<div class="success-icon">✓</div>
								<h3 class="complete-title">Import Successful!</h3>
								<p class="complete-subtitle" id="deptImportResult">Departments have been imported successfully</p>
							</div>
							<div class="complete-actions">
								<button class="new-upload-btn" id="deptNewUploadBtn">Import Another File</button>
								<button class="view-files-btn" id="deptViewFilesBtn">View Departments</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Import Employee Tab -->
			<div class="tab-pane fade" id="layout-setting" role="tabpanel" aria-labelledby="layout-setting-tab">
				<div class="csv-upload-box">
					<div class="upload-container">
						<div class="upload-box" id="uploadBox">
							<div class="upload-content">
								<div class="upload-icon">
									<svg width="64" height="64" viewBox="0 0 24 24" fill="none">
										<path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</div>
								<h3 class="upload-title">Drop CSV file here or click to upload</h3>
								<p class="upload-subtitle">Upload Subscription Count CSV file with columns: ID, Employee Name, Email, Full Function, No. of Conversions</p>

								<button class="upload-button" type="button">Choose CSV File</button>
								<input type="file" id="fileInput" accept=".csv" hidden>
							</div>

							<div class="upload-progress" id="uploadProgress">
								<div class="progress-circle">
									<svg class="progress-ring" width="60" height="60">
										<circle cx="30" cy="30" r="25" stroke="#4a5568" stroke-width="4" fill="none" />
										<circle class="progress-bar" cx="30" cy="30" r="25" stroke="#f093fb" stroke-width="4" fill="none" stroke-dasharray="157" stroke-dashoffset="157" />
									</svg>
									<span class="progress-text">0%</span>
								</div>
								<p class="progress-label">Uploading and importing Subscription Count...</p>
							</div>
						</div>

						<div class="files-preview" id="filesPreview" style="display:none">
							<div class="preview-header">
								<h4 class="preview-title">Selected CSV File</h4>
								<button class="add-more-btn" id="addMoreBtn" style="display: none;">Choose Different File</button>
							</div>
							<div class="files-list" id="filesList">
								<!-- File will be dynamically added here -->
							</div>
						</div>

						<div class="upload-complete" id="uploadComplete" style="display: none;">
							<div class="complete-header">
								<div class="success-icon">✓</div>
								<h3 class="complete-title">Import Successful!</h3>
								<p class="complete-subtitle" id="importResult">Subscription Count have been imported successfully</p>
							</div>
							<div class="complete-actions">
								<button class="new-upload-btn" id="newUploadBtn">Import Another File</button>
								<button class="view-files-btn" id="viewFilesBtn">View Subscription Count</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Add meta tags for JavaScript -->
<meta name="employee_csv_nonce" content="<?php echo wp_create_nonce('employee_csv_import'); ?>">
<meta name="department_csv_nonce" content="<?php echo wp_create_nonce('department_csv_import'); ?>">
<script>
	window.ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	window.adminUrl = '<?php echo admin_url(); ?>';
	var labels = '';

	// Tab switching functionality
	document.addEventListener('DOMContentLoaded', function() {
		const tabLinks = document.querySelectorAll('.nav-link');
		const tabPanes = document.querySelectorAll('.tab-pane');

		// Function to activate a specific tab
		function activateTab(tabId) {
			// Remove active class from all tabs and panes
			tabLinks.forEach(l => l.classList.remove('active'));
			tabPanes.forEach(p => p.classList.remove('active', 'show'));

			// Find and activate the target tab
			const targetTab = document.querySelector(`[data-bs-target="#${tabId}"]`);
			const targetPane = document.getElementById(tabId);
			
			if (targetTab && targetPane) {
				targetTab.classList.add('active');
				targetPane.classList.add('active', 'show');
			}
		}

		// Check URL parameters for active tab
		const urlParams = new URLSearchParams(window.location.search);
		const activeTab = urlParams.get('tab');
		if (activeTab) {
			activateTab(activeTab);
		} else {
			// Default to stories tab if no tab parameter
			activateTab('stories-setting');
		}

		tabLinks.forEach(function(link) {
			link.addEventListener('click', function(e) {
				e.preventDefault();

				// Get the target tab ID
				const targetId = this.getAttribute('data-bs-target').substring(1);
				
				// Update URL with tab parameter while preserving other parameters
				const currentUrl = new URL(window.location);
				currentUrl.searchParams.set('tab', targetId);
				
				// Remove paged parameter when switching tabs
				currentUrl.searchParams.delete('paged');
				
				// Update browser URL without reloading
				window.history.pushState({}, '', currentUrl);
				
				// Activate the tab
				activateTab(targetId);
			});
		});

		// Department import functionality
		initDepartmentImport();
	});

	// Department import functions
	function initDepartmentImport() {
		const deptUploadBox = document.getElementById('deptUploadBox');
		const deptFileInput = document.getElementById('deptFileInput');
		const deptFilesPreview = document.getElementById('deptFilesPreview');
		const deptFilesList = document.getElementById('deptFilesList');
		const deptUploadComplete = document.getElementById('deptUploadComplete');
		const deptNewUploadBtn = document.getElementById('deptNewUploadBtn');
		const deptViewFilesBtn = document.getElementById('deptViewFilesBtn');
		const deptImportResult = document.getElementById('deptImportResult');

		if (!deptUploadBox) return;

		// Upload box events
		deptUploadBox.addEventListener('click', () => {
			deptFileInput.click();
		});

		deptFileInput.addEventListener('change', (e) => {
			if (e.target.files.length > 0) {
				handleDepartmentFile(e.target.files[0]);
			}
		});

		// Drag and drop events
		deptUploadBox.addEventListener('dragover', (e) => {
			e.preventDefault();
			deptUploadBox.classList.add('dragover');
		});

		deptUploadBox.addEventListener('dragleave', (e) => {
			e.preventDefault();
			deptUploadBox.classList.remove('dragover');
		});

		deptUploadBox.addEventListener('drop', (e) => {
			e.preventDefault();
			deptUploadBox.classList.remove('dragover');
			if (e.dataTransfer.files.length > 0) {
				handleDepartmentFile(e.dataTransfer.files[0]);
			}
		});

		// Action buttons
		deptNewUploadBtn.addEventListener('click', () => {
			deptUploadComplete.style.display = 'none';
			deptUploadBox.classList.remove('uploading');
			deptFileInput.value = '';
			deptFilesList.innerHTML = '';
		});

		deptViewFilesBtn.addEventListener('click', () => {
			window.location.href = window.adminUrl + 'edit-tags.php?taxonomy=employee-category';
		});

		function handleDepartmentFile(file) {
			// Validate file type
			if (!file.name.toLowerCase().endsWith('.csv')) {
				showError('Please select a valid CSV file.');
				return;
			}

			// Show file preview
			showDepartmentFilePreview(file);

			// Start upload process
			uploadDepartmentFile(file);
		}

		function showDepartmentFilePreview(file) {
			const fileSize = (file.size / 1024).toFixed(2);
			const fileItem = `
<div class="file-item">
<div class="file-preview">
<svg width="60" height="60" viewBox="0 0 24 24" fill="none">
<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2"/>
<polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2"/>
<line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2"/>
<line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2"/>
<polyline points="10,9 9,9 8,9" stroke="currentColor" stroke-width="2"/>
	</svg>
	</div>
<div class="file-info">
<div class="file-name">${file.name}</div>
<div class="file-size">${fileSize} KB</div>
	</div>
<div class="file-status">
<span class="status-icon status-uploading">⏳</span>
	</div>
	</div>
`;

			deptFilesList.innerHTML = fileItem;
			deptFilesPreview.style.display = 'block';
			deptUploadBox.classList.add('uploading');
		}

		function uploadDepartmentFile(file) {
			const formData = new FormData();
			formData.append('action', 'department_csv_import');
			formData.append('department_csv_nonce', getDepartmentNonce());
			formData.append('csv_file', file);

			// Simulate progress
			let progress = 0;
			const progressInterval = setInterval(() => {
				progress += Math.random() * 15;
				if (progress > 90) progress = 90;
				updateDepartmentProgress(progress);
			}, 200);

			// Send AJAX request
			fetch(window.ajaxurl, {
				method: 'POST',
				body: formData
			})
				.then(response => {
					if (!response.ok) {
						throw new Error(`HTTP error! status: ${response.status}`);
					}
					return response.text();
				})
				.then(data => {
				clearInterval(progressInterval);
				updateDepartmentProgress(100);

				setTimeout(() => {
					deptUploadBox.classList.remove('uploading');
					deptFilesPreview.style.display = 'none';
					deptUploadComplete.style.display = 'block';

					// Check if response contains errors
					if (data.includes('Error') || data.includes('Failed') || data.includes('Security check failed') || data.includes('Permission denied')) {
						deptImportResult.innerHTML = data;
						showError('Department import completed with errors. Please check the details above.');
					} else if (data.includes('Import complete')) {
						deptImportResult.innerHTML = data;
						showSuccess('Department import completed successfully!');
					} else {
						deptImportResult.innerHTML = data;
						showSuccess('Department import completed successfully!');
					}
				}, 500);
			})
				.catch(error => {
				clearInterval(progressInterval);
				deptUploadBox.classList.remove('uploading');
				console.error('Department upload error:', error);
				
				// Try to extract error message
				let errorMessage = 'Department upload failed: ' + error.message;
				showError(errorMessage);
				
				// Also display in result area
				deptImportResult.innerHTML = '<div class="error-message">' + errorMessage + '</div>';
			});
		}

		function updateDepartmentProgress(percentage) {
			const progressText = document.querySelector('#deptUploadProgress .progress-text');
			const progressBar = document.querySelector('#deptUploadProgress .progress-bar');

			if (progressText && progressBar) {
				progressText.textContent = Math.round(percentage) + '%';

				const circumference = 2 * Math.PI * 25;
				const offset = circumference - (percentage / 100) * circumference;
				progressBar.style.strokeDashoffset = offset;
			}
		}

		function getDepartmentNonce() {
			const nonceElement = document.querySelector('meta[name="department_csv_nonce"]');
			if (!nonceElement) {
				console.error('Department CSV import nonce not found.');
				return '';
			}
			return nonceElement.getAttribute('content') || '';
		}
	}

	// Story management functions
	function editStory(storyId) {
		// Redirect to WordPress admin edit page
		window.open('<?php echo admin_url('post.php?action=edit&post='); ?>' + storyId, '_blank');
	}

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

<?php get_footer(); ?>