<?php
/**
 * This file adds the Home Page to the AgentPress Pro Theme.
 *
 * @author StudioPress
 * @package AgentPress Pro
 * @subpackage Customizations
 */

//* Enqueue scripts
add_action( 'wp_enqueue_scripts', 'agentpress_front_page_enqueue_scripts' );
function agentpress_front_page_enqueue_scripts() {
	
	//* Load scripts only if custom background is being used
	if ( ! get_option( 'agentpress-home-image' ) )
		return;

	//* Enqueue Backstretch scripts
	wp_enqueue_script( 'agentpress-backstretch', get_bloginfo( 'stylesheet_directory' ) . '/js/backstretch.js', array( 'jquery' ), '1.0.0' );
	wp_enqueue_script( 'agentpress-backstretch-set', get_bloginfo( 'stylesheet_directory' ).'/js/backstretch-set.js' , array( 'jquery', 'agentpress-backstretch' ), '1.0.0' );

	wp_localize_script( 'agentpress-backstretch-set', 'BackStretchImg', array( 'src' => str_replace( 'http:', '', get_option( 'agentpress-home-image' ) ) ) );
	
	//* Add agentpress-pro-home body class
	add_filter( 'body_class', 'agentpress_body_class' );

}

add_action( 'genesis_meta', 'agentpress_home_genesis_meta' );
/**
 * Add widget support for homepage. If no widgets active, display the default loop.
 *
 */
function agentpress_home_genesis_meta() {

	if ( is_active_sidebar( 'home-featured' ) || is_active_sidebar( 'home-top' ) || is_active_sidebar( 'home-middle-1' ) || is_active_sidebar( 'home-middle-2' ) || is_active_sidebar( 'home-middle-3' ) || is_active_sidebar( 'home-bottom' ) ) {

		//* Force full-width-content layout setting
		add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
		
		//* Remove breadcrumbs
		remove_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_breadcrumbs' );

		//* Remove the default Genesis loop
		remove_action( 'genesis_loop', 'genesis_do_loop' );
		
		//* Add home featured area
		add_action( 'genesis_after_header', 'agentpress_home_featured_widget' );
		
		//* Add home widget area
		add_action( 'genesis_before_footer', 'agentpress_home_widgets', 1 );

	}
}

function agentpress_body_class( $classes ) {

		$classes[] = 'agentpress-pro-home';
		return $classes;
		
}

function agentpress_home_featured_widget() {

	genesis_widget_area( 'home-featured', array(
		'before' => '<div class="home-featured full-width widget-area"><div class="wrap">',
		'after' => '</div></div>',
	) );

}

function agentpress_home_widgets() {

	genesis_widget_area( 'home-top', array(
		'before' => '<div class="home-top full-width widget-area"><div class="wrap">',
		'after'  => '</div></div>',
	) );

	if ( is_active_sidebar( 'home-middle-1' ) || is_active_sidebar( 'home-middle-2' ) || is_active_sidebar( 'home-middle-3' ) ) {
		
		echo '<div class="home-middle"><div class="wrap">';
		
			genesis_widget_area( 'home-middle-1', array(
				'before' => '<div class="home-middle-1 full-width widget-area">',
				'after'  => '</div>',
			) );
			
			genesis_widget_area( 'home-middle-2', array(
				'before' => '<div class="home-middle-2 widget-area">',
				'after'  => '</div>',
			) );
			
			genesis_widget_area( 'home-middle-3', array(
				'before' => '<div class="home-middle-3 widget-area">',
				'after'  => '</div>',
			) );
			
		echo '</div></div>';
		
	}
	
	genesis_widget_area( 'home-bottom', array(
		'before' => '<div class="home-bottom full-width widget-area"><div class="wrap">',
		'after'  => '</div></div>',
	) );

}

genesis();
