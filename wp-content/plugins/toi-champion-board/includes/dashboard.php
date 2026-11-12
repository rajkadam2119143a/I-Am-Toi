<?php
defined('ABSPATH') || exit;

function tcb_render_dashboard() {
	ob_start();
?>
<div class="container">


	<div class="tcb-header">
		<div class="logo">
			<img src="icon.png" alt="TOI Logo" class="logo-icon">
			<div class="logo-text">
				<strong>I AM TOI</strong>
				<span class="sub">Times of India Campaign</span>
			</div>
		</div>
		<nav class="nav">
			<a href="#dashboard" class="active"><i class="icon">&#128202;</i> Dashboard</a>
			<a href="#leaderboard"><i class="icon">&#127942;</i> Individual Leaderboard</a>
			<a href="#departments"><i class="icon">&#128101;</i> Department Rankings</a>
			<a href="#stories"><i class="icon">&#128221;</i> Success Stories</a>
			<!-- <a href="#"><i class="icon">&#9881;</i> My Stats</a> -->
		</nav>
	</div>

	<!-- Banner -->
	<div id="dashboard" class="banner">
		<h1>I AM TOI</h1>
		<p>Times of India Subscriber Campaign</p>
		<div class="stats">
			<span>6,000 Participants</span>
			<!-- <span>156,789 Subscribers</span> -->
			<span>28 Departments</span>
		</div>
	</div>

	<div id="leaderboard" class="tcb-cards">
		<div class="card">
			<div class="growth">+12.5%</div>
			<div class="icon">🎯</div>
			<div class="number">2,847</div>
			<div class="label">TOTAL SUBSCRIPTIONS</div>
		</div>
		<div class="card">
			<div class="growth">+8.2%</div>
			<div class="icon">👥</div>
			<div class="number">1,245</div>
			<div class="label">ACTIVE PARTICIPANTS</div>
		</div>
		<div class="card">
			<div class="growth">+2 new</div>
			<div class="icon">🏛️</div>
			<div class="number">28/30</div>
			<div class="label">DEPARTMENTS PARTICIPATING</div>
		</div>
		<div class="card">
			<div class="growth">+3.1%</div>
			<div class="icon">📈</div>
			<div class="number">18.7%</div>
			<div class="label">MONTHLY GROWTH</div>
		</div>
	</div>


	<div  id="departments" class="tcb-main">
		<div class="left">
			<?php
	// Example data - replace with real backend data or query
	$champions = [
		['name'=>'Rajesh Kumar','dept'=>'Editorial','subs'=>145,'growth'=>'+12%','badge'=>'Champion','image_url'=>'/wp-content/uploads/rajesh.jpg'],
		['name'=>'Priya Sharma','dept'=>'Marketing','subs'=>128,'growth'=>'+8%','badge'=>'1-Runner-up','image_url'=>'/wp-content/uploads/priya.jpg'],
		['name'=>'Amit Singh','dept'=>'Sales','subs'=>115,'growth'=>'+15%','badge'=>'2-Runner-up','image_url'=>'/wp-content/uploads/amit.jpg'],
		['name'=>'Sneha Patel','dept'=>'Digital','subs'=>98,'growth'=>'+6%','badge'=>'','image_url'=>''],
		['name'=>'Vikram Reddy','dept'=>'Operations','subs'=>87,'growth'=>'+9%','badge'=>'','image_url'=>''],
		['name'=>'Kavya Nair','dept'=>'HR','subs'=>76,'growth'=>'+4%','badge'=>'','image_url'=>''],
		// ... up to 10
	];

	// If you fetch from backend, map accordingly, e.g.:
	// $image_url = get_user_meta($user_id, 'champion_image', true) ?: '';

			?>
			<div class="section champions-list">
				<h3>🏅 Top 10 Employee Champions</h3>

				<?php foreach ($champions as $index => $c): 
	$rank = $index + 1;
	$is_top3 = $rank <= 3;
	// Derive initials
	$initials = '';
	foreach (explode(' ', $c['name']) as $part) {
		if (!empty($part)) $initials .= strtoupper($part[0]);
		if (strlen($initials) >= 2) break;
	}
	// image_url may be empty if no image uploaded
	$image_url = isset($c['image_url']) ? $c['image_url'] : '';
				?>
				<div class="champion-card <?php echo $is_top3 ? 'top-highlight' : ''; ?>">
					<div class="left">
						<?php if ($is_top3): ?>
						<?php if (!empty($image_url)): ?>
						<div class="avatar avatar-img">
							<img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($c['name']); ?>">
						</div>
						<?php else: ?>
						<div class="avatar avatar-initials"><?php echo esc_html($initials); ?></div>
						<?php endif; ?>
						<?php else: ?>
						<div class="rank-circle">#<?php echo $rank; ?></div>
						<?php endif; ?>

						<div class="meta">
							<div class="name"><?php echo esc_html($c['name']); ?></div>
							<div class="dept"><?php echo esc_html($c['dept']); ?></div>
						</div>
					</div>

					<div class="right">
						<div class="subs"><span class="subs-number"><?php echo intval($c['subs']); ?></span> <span class="subs-label">subs</span></div>
						<?php if (!empty($c['growth'])): ?>
						<div class="growth"><?php echo esc_html($c['growth']); ?> <span class="growth-text">this month</span></div>
						<?php endif; ?>

						<?php if (!empty($c['badge']) && $is_top3): ?>
						<span class="badge <?php echo sanitize_title($c['badge']); ?>"><?php echo esc_html($c['badge']); ?></span>
						<?php endif; ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>


			<!-- LEFT: Success Stories -->

			<div id="stories" class="section">
				<h3>📰 Success Stories</h3>

				<div class="story-card">
					<div class="story-left">
						<div class="avatar">RK</div>
						<div class="story-info">
							<div class="story-meta">
								<span class="author">Rajesh Kumar</span>
								<span class="dept">Editorial</span>
								<span class="time">• 2 hours ago</span>
							</div>
							<h4 class="story-title">How I convinced my apartment complex...</h4>
							<p class="story-excerpt">By sharing compelling local news stories and explaining the value of quality journalism, I managed to get 23 subscriptions from my neighbors...</p>
							<div class="story-actions">
								<div class="stats"><span class="heart">♡</span> 45</div>
								<!-- <button class="btn-text">Share</button> -->
								<button class="btn-outline">Read More</button>
							</div>
						</div>
					</div>
					<div class="subs-pill">+23 subs</div>
				</div>

				<!-- Repeat story-card for other stories... -->
			</div>
		</div>

		<!-- RIGHT: Chart + Events -->
		<div class="right">
			<div class="section">
				<h3>📊 Department Participation Leaders</h3>
				<div class="chart-container">
					<canvas id="chart"></canvas>
				</div>
			</div>

			<div class="section">
				<h3>📅 Latest Campaign Events</h3>

				<div class="event-card">
					<div class="event-left">
						<h4 class="event-title">Subscription Strategy Workshop <span class="chip workshop">workshop</span><span class="chip upcoming">upcoming</span></h4>
						<p class="event-desc">Learn effective techniques for engaging your network and converting conversations into subscriptions</p>
						<div class="event-meta">
							<span class="meta-item">📅 Jan 15, 2024</span>
							<span class="meta-item">⏰ 2:00 PM - 4:00 PM</span>
							<span class="meta-item">📍 Conference Hall A</span>
						</div>
					</div>
					<div class="event-actions">
						<div class="attendees">85 attendees</div>
						<button class="btn-outline">Register</button>
						<button class="btn-text">Details ↗</button>
					</div>
				</div>

				<!-- Repeat event-card for other events... -->
			</div>
		</div>

	</div>

	<?php
	return ob_get_clean();
}
	?>
