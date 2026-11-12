<?php

/**
 * Template Name: Champion Board Fullscreen
 * Description: A blank page template for the Champion Board shortcode
 */
defined('ABSPATH') || exit;

get_header();

$blog_info    = get_bloginfo('name');
$description  = get_bloginfo('description', 'display');
$show_title   = (true === get_theme_mod('display_title_and_tagline', true));
$header_class = $show_title ? 'site-title' : 'screen-reader-text';
$last_upload_date = get_option('last_employee_csv_upload_date');
$last_upload_date = date('Y-m-d',strtotime($last_upload_date));
/*DEPARTMENTS PARTICIPATING*/
$totalConversions = [];
$employeeCount    = [];



// /**
//  * Fetch employee-category terms with ACF fields
//  */
// function get_employee_category_status( $date_filter = '' ) {
//     $args = [
//         'taxonomy'   => 'employee-category',
//         'hide_empty' => false,
//         'meta_key'   => 'active_users',
//         'orderby'    => 'meta_value_num',
//         'order'      => 'DESC',
//     ];

//     // Apply optional date filter (if needed)
//     if ( ! empty( $date_filter ) ) {
//         $args['meta_query'] = [
//             [
//                 'key'     => 'current_date',
//                 'value'   => $date_filter,
//                 'compare' => 'LIKE',
//             ]
//         ];
//     }

//     $terms = get_terms( $args );

//     if ( empty( $terms ) || is_wp_error( $terms ) ) {
//         return [];
//     }

//     $data = [];
//     foreach ( $terms as $term ) {
//         $short_function = get_term_meta( $term->term_id, 'short_function', true );
//         $total_count    = get_term_meta( $term->term_id, 'total_count', true );
//         $active_users   = get_term_meta( $term->term_id, 'active_users', true );

//         $data[] = [
//             'name'           => esc_html( $term->name ),
//             'short_function' => esc_html( $short_function ),
//             'total_count'    => intval( $total_count ),
//             'active_users'   => intval( $active_users ),
//         ];
//     }

//     return $data;
// }

// $last_upload_date = get_option('last_employee_csv_upload_date');
// $last_upload_date = date('Y-m-d', strtotime($last_upload_date));

// $stats = get_employee_category_status( $last_upload_date );

// // Print result
// if ( ! empty( $stats ) ) {
//     foreach ( $stats as $row ) {
//         echo '<p>';
//         echo 'Department: <strong>' . $row['name'] . '</strong><br>';
//         echo 'Short Function: ' . $row['short_function'] . '<br>';
//         echo 'Total Count: ' . $row['total_count'] . '<br>';
//         echo 'Active Users: ' . $row['active_users'] . '<br>';
//         echo '</p>';
//     }
// } else {
//     echo '<p>No employee category data found.</p>';
// }

// echo "<pre>";
// print_r($employeesCounts);
// echo "</pre>";
/*TOTAL SUBSCRIPTIONS*/
$total = 0;
$total_conversionss = 0;
$unique_term_ids = array();
$args = array(
	'post_type'      => 'employee', // your custom post type
	'post_status'    => 'publish',
	'posts_per_page' => -1, // get all
	'fields'         => 'ids', // only fetch post IDs for performance
	'date_query'     => array(
		array(
			'after'     => $last_upload_date,
			'inclusive' => true,
			'column'    => 'post_date', // use 'post_modified' if you want last update
		),
	),
);

$posts = get_posts($args);
foreach ($posts as $post_id) {
	$conversion = get_post_meta($post_id, 'conversions_number', true);
	if (is_numeric($conversion)) {
		$total_conversionss += (int) $conversion;
	}

	/*Attemp DEPARTMENTS*/
	$terms_post = wp_get_object_terms($post_id, 'employee-category', array('fields' => 'ids'));
	if (!is_wp_error($terms_post)) {
		$unique_term_ids = array_merge($unique_term_ids, $terms_post);
	}
	$total++;
}

// Get unique term IDs
$unique_term_ids = array_unique($unique_term_ids);
// Final count
$unique_category_count = count($unique_term_ids);
// echo "<pre>";
// print_r();
// echo "</pre>";

/*Top 10 Employee*/
// Get top 10 employees ordered by conversions_number (numeric)
$args = array(
	'post_type'      => 'employee',
	'post_status'    => 'publish',
	'posts_per_page' => 11,
	'meta_key'       => 'conversions_number',
	'orderby'        => array(
		'meta_value_num' => 'DESC', // primary: conversions_number
		'date'           => 'ASC',  // secondary: earlier upload first
		'ID'             => 'ASC',  // tertiary: lower ID first if still tied
	),
	'fields'         => 'ids',
	'date_query'     => array(
		array(
			'after'     => $last_upload_date,
			'inclusive' => true,
			'column'    => 'post_date',
		),
	),
);

$employee_ids = get_posts($args);
$champions = [];
$badges = [
	0 => 'Champion',
	// 	1 => '1st Runner Up',
	// 	2 => '2nd Runner Up'
];
foreach ($employee_ids as $index => $post_id) {
	// Get employee data
	$name = get_the_title($post_id);
	// Get department (first term from taxonomy)
	$terms10 = wp_get_post_terms($post_id, 'employee-category', ['fields' => 'ids']);
	$term_name = get_term_by('id', $terms10[0], 'employee-category');
	$dept  = !empty($terms10) ? get_term_meta($terms10[0], 'short_function', true) : '';
	// Get conversions_number
	$subs = (int) get_post_meta($post_id, 'conversions_number', true);
	// (Optional) Calculate growth if you have old data stored
	$growth = ''; // You can compute here if needed
	// Get featured image or default
	$image_url = get_the_post_thumbnail_url($post_id, 'full');
	if (!$image_url) {
		$image_url = 'https://via.placeholder.com/150'; // Fallback image
	}
	// Assign badge if in top 3
	$badge = isset($badges[$index]) ? $badges[$index] : '';
	// Build array
	$champions[] = [
		'name'      => $name,
		'dept'      => $dept,
		'fulldept'      => $term_name->name,
		'subs'      => $subs,
		'growth'    => $growth,
		'badge'     => $badge,
		'image_url' => $image_url
	];
}
?>
<div class="container">
	<!-- Banner -->
	<div id="dashboard" class="banner">
		<div class="banner-content">
			<div class="left-text">
				<p class="small_font">5000 Participants</p>
<!-- 				<p class="small_font"><?php //echo (isset($terms) && !empty($terms) && is_array($terms)) ? count($terms) : 0; ?> Functions</p> -->
				<p><strong>#EachOneReachOne</strong></p>

			</div>
			<img src="<?php echo site_url() . '/wp-content/uploads/2025/08/middleImage.jpg'; ?>" alt="TOI Logo" class="logo-banner">
			<div class="right-text">
				<p>The Impact Begins With Me!</p>
			</div>
		</div>
	</div>

	<div class="tcb-cards">
		<div class="card">
			<!-- 			<div class="growth">+12.5%</div> -->
			<div class="icon">
				<img src="<?php echo site_url(); ?>/wp-content/uploads/2025/08/dashboard.png" alt="Dashboard Icon" />
			</div>
			<div class="number"><?php echo esc_html($total_conversionss); ?></div>
			<div class="label">TOTAL SUBSCRIPTIONS</div>
		</div>
		<div class="card">
			<!-- 			<div class="growth">+8.2%</div> -->
			<div class="icon"><img src="<?php echo site_url(); ?>/wp-content/uploads/2025/08/champions.png" alt="Participants Icon" /></div>
			<div class="number"><?php echo esc_html($total); ?></div>
			<div class="label">ACTIVE PARTICIPATIONS</div>
		</div>
		<!-- 		<div class="card">
			<!-- 			<div class="growth">+2 new</div> 
			<div class="icon">
				<img src="<?php //echo site_url(); ?>/wp-content/uploads/2025/08/post_your_story.png" alt="Department Icon" />
			</div>
			<div class="number"><?php //echo esc_html($unique_category_count); ?>/<?php //echo (isset($total_terms) && !empty($total_terms) && is_array($total_terms)) ? count($total_terms) : 0; ?></div>
			<div class="label">PARTICIPATING FUNCTIONS</div>
		</div> -->
<!-- 		<div class="card">
						<div class="growth">+3.1%</div> 
			<div class="icon">
				<img src="<?php //echo site_url(); ?>/wp-content/uploads/2025/08/leaderboard.png" alt="Growth Icon" />
			</div>
			<div class="number">18.7%</div>
			<div class="label">WEEKLY GROWTH</div>
		</div> -->
	</div>
				<?php
// // === Step 1: Get top 10 by conversions ===
// arsort($employeesCounts);
// $top10 = array_slice($employeesCounts, 0, 10, true);

// $barLabels = array_keys($top10);
// $barConversions = array_values($top10);

// // === Step 2: Match Active Employees values ===
// $barActive = [];
// foreach ($barLabels as $deptName) {
//     $barActive[] = isset($employeesActive[$deptName]) ? $employeesActive[$deptName] : 0;
// }

// // === Step 3: Match Total Employees values ===
// $barTotal = [];
// foreach ($barLabels as $deptName) {
//     $barTotal[] = isset($employeesTotal[$deptName]) ? $employeesTotal[$deptName] : 0;
// }
// ?>
<!-- // <script>
// var barLabels = <?php //echo json_encode($barLabels); ?>;
// var barConversions = <?php //echo json_encode($barConversions); ?>;
// var barActive = <?php //echo json_encode($barActive); ?>;
// var barTotal = <?php //echo json_encode($barTotal); ?>;
// </script> -->

<!-- // <style>
/* //   .bar-filters { margin-bottom: 15px; }
//   .bar-filter { cursor: pointer; margin-right: 10px; padding: 5px 10px; border: 1px solid #ccc; border-radius: 4px; user-select: none; display: inline-flex; align-items: center; }
//   .bar-filter.active { font-weight: bold; border-color: #333; }
//   .color-box { width: 15px; height: 15px; display: inline-block; margin-right: 5px; } */
// </style> -->

<!-- // <div class="section card">
//   <canvas id="barChart" height="100"></canvas>
// </div>
	 -->


	
<?php
// === Get last upload date ===
$last_upload_date = get_option('last_employee_csv_upload_date');
$last_upload_date = date('Y-m-d', strtotime($last_upload_date));

$terms = get_terms([
    'taxonomy'   => 'employee-category',
    'hide_empty' => false,
    'meta_query' => [[
        'key'     => 'current_date',
        'value'   => $last_upload_date,
        'compare' => 'LIKE'
    ]],
]);

$grouped = [];
$totalActive = $totalEmployees = 0;

if (!empty($terms) && !is_wp_error($terms)) {
    foreach ($terms as $term) {
        // === Conversions for this term since last upload date ===
        $posts = get_posts([
            'post_type'      => 'employee',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [[
                'taxonomy' => 'employee-category',
                'field'    => 'term_id',
                'terms'    => $term->term_id,
            ]],
            'date_query'     => [[
                'after'     => $last_upload_date,
                'inclusive' => true,
                'column'    => 'post_date',
            ]],
        ]);

        $term_conversions = 0;
        if (!empty($posts)) {
            foreach ($posts as $pid) {
                $term_conversions += (int) get_post_meta($pid, 'conversions_number', true);
            }
        }

        $short_func     = get_term_meta($term->term_id, 'short_function', true);
        $clubbed_func   = get_term_meta($term->term_id, 'clubbed_functions', true);
        $clubbed_short  = get_term_meta($term->term_id, 'clubbed_short_form', true);

        if (!$clubbed_func) {
            $clubbed_func  = $term->name;
            $clubbed_short = $short_func ?: $term->name;
        }

        $total_count  = (int) get_term_meta($term->term_id, 'total_count', true);
        $active_users = (int) get_term_meta($term->term_id, 'active_users', true);

        // === Ensure parent group exists ===
        if (!isset($grouped[$clubbed_func])) {
            $grouped[$clubbed_func] = [
                'short'       => $clubbed_short ?: $clubbed_func,
                'full'        => $clubbed_func,
                'total'       => 0,
                'active'      => 0,
                'conversions' => 0,
                'children'    => []
            ];
        }

        // === Aggregate parent totals ===
        $grouped[$clubbed_func]['total']       += $total_count;
        $grouped[$clubbed_func]['active']      += $active_users;
        $grouped[$clubbed_func]['conversions'] += $term_conversions;

        // === Push child breakdown ===
        $child_participation = $total_count > 0 ? round(($active_users / $total_count) * 100) : 0;
        $grouped[$clubbed_func]['children'][] = [
            'name'          => $term->name,
            'active'        => $active_users,
            'total'         => $total_count,
            'conversions'   => $term_conversions,
            'participation' => $child_participation
        ];

        $totalActive    += $active_users;
        $totalEmployees += $total_count;
    }
}

// === Split into buckets ===
$high = $medium = $low = [];
foreach ($grouped as $g) {
    // Replace parent participation calc with avg of children
    if (!empty($g['children'])) {
        $sum = array_sum(array_column($g['children'], 'participation'));
        $participation = round($sum / count($g['children']));
    } else {
        $participation = $g['total'] > 0 ? round(($g['active'] / $g['total']) * 100) : 0;
    }

    $row = [
        'short'         => $g['short'],
        'full'          => $g['full'],
        'conversions'   => $g['conversions'],
        'total'         => $g['total'],
        'active'        => $g['active'],
        'participation' => $participation,
        'children'      => $g['children']
    ];

    if ($participation >= 20) {
        $high[] = $row;
    } elseif ($participation >= 5 && $participation < 20) {
        $medium[] = $row;
    } else {
        $low[] = $row;
    }
}


// Sort each bucket by participation desc
$sortByPart = fn($a, $b) => $b['participation'] <=> $a['participation'];
usort($high, $sortByPart);
usort($medium, $sortByPart);
usort($low, $sortByPart);
?>

<style>
/* ===== Layout ===== */
.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr) 1fr; /* 3 charts + side panel */
  gap: 20px;
/*   background: #999; /* dark red background */  */
  padding: 20px;
  border-radius: 16px;
  border: 2px solid #fff; /* white border around dashboard */
}

/* ===== Cards ===== */
.card-multi {
/*   background: #a42121; */
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,.3);
  padding: 10px;
  color: #fff;
  border: 1px solid #fff; /* white border */
}

.chart-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 600px;
  border: 1px solid #fff; /* white border */
}

.chart-title {
  font-weight: 600;
  text-align: center;
  margin-bottom: 5px;
  font-size: 14px;
  color: #000;
}

/* ===== Chart bars style ===== */
.chart-card canvas {
  flex-grow: 1;
  width: 95% !important;
  margin: 0 auto;
/*   height: auto !important; */
	height: 400px !important;
}

/* Chart.js bar custom styling */
.chart-bar {
  background: linear-gradient(90deg, #f5f5f5, #d9d9d9);
  border: 1px solid #000;
  border-radius: 4px;
  box-shadow: inset 0 2px 4px rgba(0,0,0,.2);
}

/* ===== Sidebar ===== */
.side-panel {
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 320px;
  height: 600px;
  overflow: auto;
/*   border: 1px solid #000; /* black border */ */
  background: #fff;       /* white background */
  color: #000;            /* black text */
}

.gauge-box {
  background: #fff;
/*   border: 1px solid #000; */
  border-radius: 8px;
  padding: 12px;
  text-align: center;
  color: #000;
}

.gauge-title {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #000;
}

.gauge-wrap {
  position: relative;
  width: 50%;
  height: 120px;
  margin: 0 auto;
}

.gauge-labels {
  position: absolute;
  bottom: 25px;
  left: 0;
  width: 100%;
  display: flex;
  justify-content: space-between;
  font-weight: 600;
  font-size: 16px;
  padding: 0 15px;
  box-sizing: border-box;
  color: #000;
}

.gauge-center {
  position: absolute;
  bottom: -5px;
  left: 35%;
  transform: translate(-5%, 0);
  font-size: 22px;
  font-weight: bold;
  color: #000;   /* changed from white to black */
}

.gauge-left {
  position: absolute;
  bottom: 0;
  left: -15px;
  color: #000;
}
.gauge-right {
  position: absolute;
  right: -45px;
  bottom: 0;
  color: #000;
}

/* ===== Subscription table ===== */
.table-wrap {
  max-height: 250px;
  overflow-y: auto;
  border: 1px solid #ccc;
  border-radius: 6px;
  display: flex;
  flex-direction: column;
  background: #fff;
  color: #000;
}

.subscription-table {
  border-collapse: collapse;
  width: 100%;
}

.subscription-table th,
.subscription-table td {
  padding: 6px 10px;
  border-bottom: 1px solid #eee;
  text-align: center;
  color: #000;
}

.subscription-table thead th {
  top: 0;
  background: #f2f2f2; /* light gray instead of red */
  color: #000;
  z-index: 2;
}

.subscription-table tbody {
  font-size: 12px;
}

.subscription-table tfoot td {
  position: sticky;
  bottom: -8px;
  background: #f2f2f2; /* light gray instead of red */
  color: #000;
  z-index: 2;
}

/* ===== Responsive ===== */
@media (max-width: 1100px) {
  .dashboard-grid {
    grid-template-columns: 1fr 1fr; 
  }
  .chart-card {
    height: auto;           /* let chart adjust */
    min-height: 350px;      /* keep decent size */
    padding-bottom: 25px;   /* space for axis labels */
  }
  .side-panel {
    width: 100%;
    height: auto;
  }
}

@media (max-width: 720px) {
  .dashboard-grid {
    grid-template-columns: 1fr; 
  }
  .chart-card {
    height: auto;           /* no hard 300px */
    min-height: 280px;      /* smaller but still visible */
    padding-bottom: 25px;   /* prevent labels from being cut */
  }
  .side-panel {
    order: -1; /* show side panel on top for mobile */
    width: 100%;
  }
}


</style>

<div class="section ">
	<div class="card" style="padding-bottom: 10px;font-size:12px;">
	<div class="last_uploaded" style="display: flex; justify-content: flex-end; align-items: center; text-align: right; ">
<!-- 		<p style="margin:0;">
			No. of Unattributed Subscriptions: 4
		</p> -->
		<span>
			<?php
			if ($last_upload_date) {
				echo "Last updated: " . date('d M Y', strtotime($last_upload_date)) . " | 09:35 AM";
			}
			?>
		</span>
	</div>
</div>
<h3><img src="<?php echo site_url(); ?>/wp-content/uploads/2025/08/champions.png" width="28" height="28" /> Participation</h3>	
	<div class="dashboard-grid">
  <!-- High -->
  <div class="card-multi chart-card">
    <div class="chart-title">High Participation (Functions)</div>
    <canvas id="highChart" ></canvas>
  </div>

  <!-- Medium -->
  <div class="card-multi chart-card">
    <div class="chart-title">Medium Participation (Functions)</div>
    <canvas id="mediumChart"></canvas>
  </div>

  <!-- Low -->
  <div class="card-multi chart-card">
    <div class="chart-title">Low Participation (Functions)</div>
    <canvas id="lowChart"></canvas>
  </div>

  <!-- Side panel -->
 <!-- <div class="card-multi side-panel">
    <div class="gauge-box">
  <h4 class="gauge-title">Active Participants</h4>
  <div class="gauge-wrap">
    <canvas id="gaugeChart"></canvas>
    <div class="gauge-labels">
      <span class="gauge-left">0</span>
      <span class="gauge-center"><?php //echo $totalActive; ?></span>
      <span class="gauge-right"><?php //echo $totalEmployees; ?></span>
    </div>
  </div>
</div>
<!-- <?php
// 🔽 Sort departments/functions by conversions (highest → lowest)
//usort($allTermsData, function($a, $b) {
    //return $b['conversions'] <=> $a['conversions'];
//});

// 🔽 Calculate total conversions
//$totalConversions = array_sum(array_column($allTermsData, 'conversions'));
?>

<div>
    <table class="subscription-table">
      <thead>
        <tr>
          <th>Department</th>
          <th>Conversions</th>
        </tr>
      </thead>
      <tbody>
        <?php //foreach ($allTermsData as $r): ?>
          <tr>
            <td><?php //echo esc_html($r['short']); ?></td>
            <td><?php //echo esc_html($r['conversions']); ?></td>
          </tr>
        <?php //endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td>Total</td>
          <td><?php //echo (int) $totalConversions; ?></td>
        </tr>
      </tfoot>
    </table>
</div> 


   </div> -->
</div>
	<p style="font-size: 14px;
    /* text-align: -webkit-left; */
    color: #999999;
    padding: 18px 0px 1px 18px;"><i>
		Note : Participation is calculated as active campaign participants divided by the total employees in each function.</i>
	</p>
	

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
/* ===== Data from PHP ===== */
const highData   = <?php echo json_encode($high); ?>;
const mediumData = <?php echo json_encode($medium); ?>;
const lowData    = <?php echo json_encode($low); ?>;
const totalActive = <?php echo (int) $totalActive; ?>;
const totalEmployees = <?php echo (int) $totalEmployees; ?>;

/* ===== Helper: decode HTML entities AND strip tags ===== */
function decodeAndStrip(html) {
  if (!html) return "";
  const tmp = document.createElement('div');
  tmp.innerHTML = html;                 // decode entities AND parse tags
  return (tmp.textContent || tmp.innerText || "").trim(); // returns plain text
}

/* ===== Aggregate participation for a row (use numeric values) ===== */
function aggregateParticipation(row) {
  if (row.children && row.children.length > 0) {
    const sum = row.children.reduce((acc, c) => acc + (Number(c.participation) || 0), 0);
    return row.children.length ? sum / row.children.length : 0;
  }
  return Number(row.participation) || 0;
}

/* ===== Normalize rows and children (make names safe) ===== */
function normalizeRow(row) {
  const newRow = {
    ...row,
    participation: aggregateParticipation(row),
    full: decodeAndStrip(row.full || row.short || row.name || row.label || ""),
    short: decodeAndStrip(row.short || row.full || row.name || row.label || "")
  };

  if (row.children && row.children.length > 0) {
    newRow.children = row.children.map(c => ({
      ...c,
      participation: Number(c.participation) || 0,
      full: decodeAndStrip(c.full || c.short || c.name || c.label || ""),
      short: decodeAndStrip(c.short || c.full || c.name || c.label || "")
    }));
  }

  return newRow;
}

function prepareRows(rows) {
  return rows.map(normalizeRow);
}

/* ===== Common Chart.js options for horizontal bars ===== */
const baseBarOptions = {
  type: 'bar',
  data: { labels: [], datasets: [] },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    resizeDelay: 100,
    indexAxis: 'y',
    animation: { duration: 300 },
    plugins: {
      legend: { display: false },
      tooltip: { enabled: true }
    },
    scales: {
      x: {
        beginAtZero: true,
        max: undefined,
        grid: { color: 'rgba(255,255,255,.2)' },
        ticks: { color: '#000' }
      },
      y: {
        grid: { display: false },
        ticks: { color: '#000' }
      }
    }
  }
};

/* ===== Value labels plugin (shows % at end of each bar) ===== */
const valueLabelPlugin = {
  id: 'valueLabel',
  afterDatasetsDraw(chart) {
    const { ctx, data, scales } = chart;
    const x = scales.x;
    const y = scales.y;
    ctx.save();
    ctx.font = '600 12px system-ui, Arial';
    ctx.textBaseline = 'middle';

    const realParts = chart.data.datasets[0].originalData || chart.data.datasets[0].data;
    data.datasets[0].data.forEach((val, i) => {
      const xPos = x.getPixelForValue(val);
      const yPos = y.getPixelForTick(i);
      const realVal = Math.round(Number(realParts[i]) || 0);
      const text = realVal + '%';
      const textWidth = ctx.measureText(text).width + 6;

      // if bar is wide enough, draw inside (right aligned)
      if (xPos > textWidth + 10) {
        ctx.fillStyle = '#fff';       // white text inside
        ctx.textAlign = 'right';
        ctx.fillText(text, xPos - 6, yPos);
      } else {
        ctx.fillStyle = '#000';       // black text outside
        ctx.textAlign = 'left';
        ctx.fillText(text, xPos + 6, yPos);
      }
    });
    ctx.restore();
  }
};

/* ===== Chart renderer (with robust tooltip showing parent + children) ===== */
function renderGroupChart(canvasId, rows, color) {
  const preparedRows = prepareRows(rows);

  // sort by participation descending
  const sorted = [...preparedRows].sort((a, b) => b.participation - a.participation);

  const labels = sorted.map(r => r.short || r.full);
  const parts  = sorted.map(r => Number(r.participation) || 0);

  // compute nice max
  const maxValue = Math.max(...parts, 1);
  function roundUpToNice(num) {
    if (num <= 50) return Math.ceil(num / 10) * 10;
    if (num <= 200) return Math.ceil(num / 20) * 20;
    if (num <= 500) return Math.ceil(num / 50) * 50;
    return Math.ceil(num / 100) * 100;
  }
  const niceMax = roundUpToNice(maxValue);

  const cfg = JSON.parse(JSON.stringify(baseBarOptions));
  cfg.options.scales.x.max = niceMax;

  cfg.data = {
    labels: labels,
    datasets: [{
      label: 'Participation %',
      data: parts,
      originalData: parts,
      borderRadius: 8,
      backgroundColor: color
    }]
  };

  // tooltip: hide title (avoid duplicate "short" header) and show parent+children lines (safe decoding & fallbacks)
  cfg.options.plugins.tooltip.callbacks = {
    title: function() {
      // return null to remove the default title/header
      return null;
    },
    label: function(context) {
      const idx = context.dataIndex;
      const parent = sorted[idx];
      const parentName = decodeAndStrip(parent.full || parent.short || parent.name || 'Unnamed');
      const parentLine = parentName + ': ' + Math.round(Number(parent.participation) || 0) + '%';

      if (parent.children && parent.children.length) {
        const childLines = parent.children.map(c => {
          const childName = decodeAndStrip(c.full || c.short || c.name || 'Unnamed');
          return '   • ' + childName + ': ' + Math.round(Number(c.participation) || 0) + '%';
        });
        return [parentLine, ...childLines];
      }

      return parentLine;
    }
  };

  cfg.plugins = [valueLabelPlugin];
  const ctx = document.getElementById(canvasId).getContext('2d');
  return new Chart(ctx, cfg);
}

/* ===== Render the three group charts ===== */
renderGroupChart('highChart',   highData   || [], '#669933'); // green
renderGroupChart('mediumChart', mediumData || [], '#ffcc66'); // amber
renderGroupChart('lowChart',    lowData    || [], '#cc3333'); // red

/* ===== Gauge chart (unchanged) ===== */
const gaugeCtxEl = document.getElementById('gaugeChart');
if (gaugeCtxEl) {
  const gaugeCtx = gaugeCtxEl.getContext('2d');
  new Chart(gaugeCtx, {
    type: 'doughnut',
    data: {
      datasets: [{
        data: [
          <?php echo (int)$totalActive; ?>,
          <?php echo max((int)$totalEmployees - (int)$totalActive, 0); ?>
        ],
        backgroundColor: ['#f36c6c', '#fff'],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      circumference: 180,
      rotation: -90,
      plugins: { legend: { display: false }, tooltip: { enabled: false } }
    }
  });
}
</script>

</div>
	
	<div id="leaderboard" class="tcb-main">
		<div class="left">
			<div class="section champions-list">
				<h3><img src="<?php echo site_url(); ?>/wp-content/uploads/2025/08/leaderboard.png" width="28" height="28" />Top Individual Champions</h3>
				<?php foreach ($champions as $index => $c):
				$rank = $index + 1;
				$is_top3 = $rank <= 1;
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
						<?php
						$avatar_class = 'avatar';
						if ($rank === 1) {
							$avatar_class .= ' avatar-red';
						} elseif ($rank === 2) {
							$avatar_class .= ' avatar-blue';
						} elseif ($rank === 3) {
							$avatar_class .= ' avatar-grey';
						} else {
							// Define an array of possible default avatar colors
							$default_colors = ['avatar-yellow', 'avatar-green', 'avatar-purple', 'avatar-orange', 'avatar-teal', 'avatar-pink', 'avatar-cyan', 'avatar-lime', 'avatar-indigo', 'avatar-brown'];
							// Pick a random color from the array
							$random_color = $default_colors[array_rand($default_colors)];
							$avatar_class .= ' ' . $random_color;
						}
						?>
						<div class="<?php echo esc_attr($avatar_class); ?>">
							<?php echo esc_html($initials); ?>
						</div>
						<div class="meta">
							<div class="name"><?php echo esc_html($c['name']); ?></div>
							<div class="dept"><?php echo esc_html($c['fulldept']); ?></div>
						</div>
					</div>

					<div class="right">
						<div class="subs"><span class="subs-number"><?php echo intval($c['subs']); ?></span> <span class="subs-label">subs</span></div>
						<?php if (!empty($c['growth'])): ?>
						<div class="growth"><?php echo esc_html($c['growth']); ?> <span class="growth-text">this month</span></div>
						<?php endif; ?>

						<?php if (!empty($c['badge']) && $is_top3): ?>
						<span class="badge patch-black <?php echo sanitize_title($c['badge']); ?>"><?php echo esc_html($c['badge']); ?></span>
						<?php endif; ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<!-- LEFT: Success Stories -->
		</div>

		<!-- RIGHT: Chart + Events -->
		<div class="right">
			<div class="section" id="departments">
				<h3><img src="<?php echo site_url(); ?>/wp-content/uploads/2025/08/champions.png" width="28" height="28" /> Top 5 Functions</h3>
				<?php
				$employeesCounts   = []; // final dataset for chart
				$clubbedCounts     = []; // totals for parent terms
				$clubbedBreakdown  = []; // child breakdown for tooltips

				$terms = get_terms([
					'taxonomy'   => 'employee-category',
					'hide_empty' => false,
					'meta_query' => [
						[
							'key'     => 'current_date',
							'value'   => $last_upload_date,
							'compare' => 'LIKE'
						]
					],
					'meta_key'   => 'active_users',
					'orderby'    => 'meta_value_num',
					'order'      => 'DESC',
				]);

				if (!empty($terms) && !is_wp_error($terms)) {
					foreach ($terms as $term) {
						$args = [
							'post_type'      => 'employee',
							'post_status'    => 'publish',
							'posts_per_page' => -1,
							'fields'         => 'ids',
							'tax_query'      => [
								[
									'taxonomy' => 'employee-category',
									'field'    => 'term_id',
									'terms'    => $term->term_id,
								]
							],
							'date_query'     => [
								[
									'after'     => $last_upload_date,
									'inclusive' => true,
									'column'    => 'post_date',
								],
							],
						];

						$posts = get_posts($args);
						$total_conversions = 0;
						if (!empty($posts)) {
							foreach ($posts as $post_id) {
								$conv = get_post_meta($post_id, 'conversions_number', true);
								$total_conversions += intval($conv);
							}
						}

						$short_func   = get_term_meta($term->term_id, 'short_function', true);
						$active_users = get_term_meta($term->term_id, 'active_users', true);

						// === If this is a child, add to parent ===
						if ($term->parent) {
							$parent_term = get_term($term->parent, 'employee-category');
							if ($parent_term && !is_wp_error($parent_term)) {
								$clubbedCounts[$parent_term->name] =
									($clubbedCounts[$parent_term->name] ?? 0) + (int) $total_conversions;

								$clubbedBreakdown[$parent_term->name][] = $term->name . " " . (int) $total_conversions;
								continue;
							}
						}

						// Standalone
						$employeesCounts[esc_html($term->name)] = (int) $total_conversions;
					}
				}

				// === Merge child totals into their parent totals ===
				foreach ($clubbedCounts as $parentName => $childTotal) {
					$employeesCounts[$parentName] = ($employeesCounts[$parentName] ?? 0) + (int)$childTotal;
				}arsort($employeesCounts);
				$top5   = array_slice($employeesCounts, 0, 5, true);
				$others = array_slice($employeesCounts, 5, null, true);

				$labels = array_keys($top5);
				$values = array_values($top5);

				// Add "Others"
				$others_total = array_sum($others);
				if ($others_total > 0) {
					$labels[] = 'Other Functions';
					$values[] = $others_total;
				}

				$colors = ['#8c1b11', '#bb281a', '#e53323', '#d86b5d', '#d3a080'];
				if ($others_total > 0) {
					$colors[] = '#efefef';
				}

				// === Build breakdown data ===
				$breakdownData = [];

				// Parent breakdowns (for top-level tooltips if needed)
				foreach ($clubbedBreakdown as $parent => $children) {
					$cleanParent = html_entity_decode(trim($parent), ENT_QUOTES, 'UTF-8');
					$breakdownData[$cleanParent] = $children;
				}

				// Others breakdown (parents only, no children lines)
				if (!empty($others)) {
					$lines = [];
					foreach ($others as $label => $val) {
						$cleanLabel = html_entity_decode(trim($label), ENT_QUOTES, 'UTF-8');
						if (!empty($clubbedBreakdown[$label])) {
							// parent has children → show combined total
							$combined = isset($employeesCounts[$label]) ? (int)$employeesCounts[$label] : (int)$val;
							$lines[] = $cleanLabel . " " . $combined;
						} else {
							$lines[] = $cleanLabel . " " . (int)$val;
						}
					}
					$breakdownData['Other Functions'] = $lines;
				}
				?>
				<script>
				var labels = <?php echo json_encode(array_map(function($l){ 
					return html_entity_decode($l, ENT_QUOTES, 'UTF-8'); 
				}, $labels)); ?>;
				var values = <?php echo json_encode($values); ?>;
				var backgroundColors = <?php echo json_encode($colors); ?>;
				var breakdownData = <?php echo json_encode($breakdownData); ?>;
				</script>

				<div class="chart-container">
					<div class="chart_count">
						<?php echo esc_html($total_conversionss); ?>
					</div>
					<canvas id="chart"></canvas>
				</div>
			</div>
			






			<!-- Repeat event-card for other events... -->
			<div id="stories" class="section">
				<h3><img src="<?php echo site_url(); ?>/wp-content/uploads/2025/08/success_stories.png" width="28" height="28" />Success Stories</h3>
				<?php
				$args = [
					'post_type'      => 'success-story',
					'post_status'    => 'publish',
					'posts_per_page' => 2,
					'orderby'        => 'modified',  // order by last updated
					'order'          => 'DESC'       // latest updated first
				];

				$story_posts = get_posts($args);
				if (isset($story_posts) && !empty($story_posts)) {
					foreach ($story_posts as $indexx => $story_post) {

						$ranks = $indexx + 1;
						$is_top33 = $ranks <= 3;
						// Derive initials
						$initialss = '';
						foreach (explode(' ', $story_post->post_title) as $part) {
							if (!empty($part)) $initialss .= strtoupper($part[0]);
							if (strlen($initialss) >= 2) break;
						}

						$avatar_class = 'avatar';

						if ($ranks === 1) {
							$avatar_class .= ' avatar-red';
						} elseif ($ranks === 2) {
							$avatar_class .= ' avatar-blue';
						} elseif ($ranks === 3) {
							$avatar_class .= ' avatar-grey';
						} else {
							$avatar_class .= ' avatar-default'; // ← Add this line for non-top-3 users
						}

				?>
				<div class="story-card">
					<div class="story-left">
						<?php
						// Author info
						$first_name = get_the_author_meta('first_name', $story_post->post_author);
						$last_name  = get_the_author_meta('last_name', $story_post->post_author);

						if (empty($first_name) && empty($last_name)) {
							$author_name = get_the_author_meta('display_name', $story_post->post_author);
						} else {
							$author_name = trim($first_name . ' ' . $last_name);
						}
						$author_email = get_the_author_meta('user_email', $story_post->post_author);

						// Avatar initials
						$initialss = '';
						if ($author_name) {
							$words = explode(' ', trim($author_name));
							foreach ($words as $w) {
								$initialss .= strtoupper($w[0]);
							}
						} else {
							$initialss = strtoupper(substr($author_email, 0, 2));
						}

						// Subscribers from ACF
						$subscribers = get_field('subscribers', $story_post->ID) ?: 0;

						// Department name
						$department_name = '';
						$department = get_field('department', $story_post->ID);

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

						// Likes — use plugin output instead of meta
						$likes_html = do_shortcode('[posts_like_dislike id=' . $story_post->ID . ']');


						// Time ago
						$time_ago = human_time_diff(get_the_time('U', $story_post->ID), current_time('timestamp')) . ' ago';

						// Bigger thumbnail with nicer rounding
						$img_style = 'width:60px; height:60px; border-radius:8px; object-fit:cover; box-shadow:0 2px 6px rgba(0,0,0,0.08); outline:none;';

						if (has_post_thumbnail($story_post->ID)) {
							echo get_the_post_thumbnail($story_post->ID, 'thumbnail', [
								'style' => $img_style
							]);
						} else {
							$fields = get_fields($story_post->ID);
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
					
						<div class="story-info">
							<div class="story-meta">
								 <?php
								$user_id = $story_post->post_author;
								$designation = get_field('designation', 'user_' . $user_id);
								?>

								<span class="author">
									<?php echo esc_html($author_name); ?>
									<?php if (!empty($designation)) : ?>
										<span class="designation"> - <?php echo esc_html($designation); ?></span>
									<?php endif; ?>
								</span>
								
								<span class="time">• <?php echo esc_html($time_ago); ?></span>
							</div>
							<div class="story-meta">
								<span class="dept"><?php echo esc_html($department_name ?: 'Subscriber'); ?></span>
							</div>
							<h4 class="story-title"><?php echo esc_html($story_post->post_title); ?></h4>
							<p class="story-excerpt"><?php echo esc_html(wp_trim_words($story_post->post_content, 20)); ?></p>
							<div class="d-flex">
								<div class="stats"><?php echo $likes_html; ?></div>
								<div class="">
									<!-- <button class="btn-text">Share</button> -->
									<a class="btn-outline" href="<?php echo get_permalink($story_post->ID); ?>">Read More</a>
								</div>
							</div>
						</div>
					</div>
					<?php if ($subscribers > 0): ?>
					<div class="subs-pill">+<?php echo esc_html($subscribers); ?> subs</div>
					<?php endif; ?>
				</div>
				<?php
					}
				}
				?>
				<!-- Repeat story-card for other stories... -->
				<div class="center-button">
					<div class="story-actions">
						<a class="btn-outline" href="<?php echo get_permalink(20); ?>">View Stories</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="top-performer-section">
		<div class="top-performer-card">
			<div class="icon">
				<!-- 			<i class="fas fa-trophy"></i> -->
				<img src="<?php echo site_url(); ?>/wp-content/uploads/2025/08/image-17.jpeg" alt="TOI Logo" width="100" height="100" class="logo-banner">
			</div>
			<h2>Share your I AM TOI Stories</h2>
			<p>Share your success story and inspire others! Top performers get featured and can help the entire TOI community grow together.</p>
			<a href="<?php echo site_url(); ?>/add-stories/" class="btn-share">Share your Success Story</a>

			<!-- Guidelines embedded here -->
			<div class="guidelines-card">
				<h3><i class="fas fa-file-alt"></i> Story Guidelines</h3>
				<div class="guidelines-columns">
					<div class="guidelines-left">
						<h4>What to Include:</h4>
						<ul>
							<li>Specific strategies you used</li>
							<li>Numbers and results achieved</li>
							<li>Challenges you overcame</li>
							<li>Tips for others to follow</li>
							<li>Timeline of your success</li>
						</ul>
					</div>
					<div class="guidelines-right">
						<h4>Story Requirements:</h4>
						<ul>
							<li>Must be a top performer (top 50)</li>
							<li>Authentic personal experience</li>
							<li>Actionable insights for others</li>
							<li>Professional and appropriate content</li>
							<li>Subject to approval process</li>
						</ul>
					</div>
				</div>
			</div>
			<!-- End of guidelines -->
		</div>
	</div>
</div>

<?php get_footer(); ?>