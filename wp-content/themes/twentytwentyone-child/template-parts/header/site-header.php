<?php
/**
 * Displays the site header.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

$wrapper_classes  = 'container';
$wrapper_classes .= has_custom_logo() ? ' has-logo' : '';
$wrapper_classes .= ( true === get_theme_mod( 'display_title_and_tagline', true ) ) ? ' has-title-and-tagline' : '';
$wrapper_classes .= has_nav_menu( 'primary' ) ? ' has-menu' : '';

$blog_info    = get_bloginfo( 'name' );
$description  = get_bloginfo( 'description', 'display' );
$show_title   = ( true === get_theme_mod( 'display_title_and_tagline', true ) );
$header_class = $show_title ? 'site-title' : 'screen-reader-text';
?>

<header id="masthead" class="<?php echo esc_attr( $wrapper_classes ); ?>">
	<div class="tcb-header">
		<div class="logo">
			<a href="<?php echo site_url();?>"><img src="<?php echo site_url().'/wp-content/uploads/2025/08/TOI_logo-2.png';?>" alt="TOI Logo" class="logo-icon"></a>
			<!-- 			<div class="logo-text">
<strong><?php //echo esc_html( $blog_info ); ?></strong>
<span class="sub"><?php //echo $description;?></span>
</div> -->
		</div>

		<div class="nav-toggle" id="navToggle">
			<span></span>
			<span></span>
			<span></span>
		</div>
		<nav class="nav">
			<ul id="navMenu">
				<?php
				wp_nav_menu(array(
					'menu'            => 3, // Menu ID
					'container'       => false,
					'menu_class'      => '',
					'items_wrap'      => '%3$s',
					'fallback_cb'     => false
				));
				if ( is_user_logged_in() && current_user_can('administrator') ) {

				?>
				<li id="menu-item-50" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-50">
					<a href="<?php echo get_permalink(119);?>">
						<i class="icon">
							<img src="https://zing.timesofindia.com/bccl/i_am_toi/wp-content/uploads/2025/08/leaderboard.png" alt="Dashboard Icon" width="35" height="35"> 
						</i> Admin</a>
				</li>
				<?php }?>
			</ul>
		</nav>
	</div>
</header>