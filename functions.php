<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Setup Theme
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

//* Set Localization (do not remove)
load_child_theme_textdomain( 'agentpress', apply_filters( 'child_theme_textdomain', get_stylesheet_directory() . '/languages', 'agentpress' ) );

//* Add Image upload to WordPress Theme Customizer
require_once( get_stylesheet_directory() . '/lib/customize.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', __( 'AgentPress Pro Theme', 'agentpress' ) );
define( 'CHILD_THEME_URL', 'http://my.studiopress.com/themes/agentpress/' );
define( 'CHILD_THEME_VERSION', '3.1.1' );

//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Enqueue Google fonts
add_action( 'wp_enqueue_scripts', 'agentpress_google_fonts' );
function agentpress_google_fonts() {

	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Lato:300,700|Roboto:700,300,400', array(), CHILD_THEME_VERSION );
		
}

//* Enqueue Scripts
add_action( 'wp_enqueue_scripts', 'agentpress_scripts' );
function agentpress_scripts() {

	wp_enqueue_style( 'dashicons' );
	wp_enqueue_script( 'agentpress-responsive-menu', get_bloginfo( 'stylesheet_directory' ) . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0' );
		
}

//* Add new image sizes
add_image_size( 'properties', 500, 300, TRUE );

//* Add support for custom header
add_theme_support( 'custom-header', array(
	'header-selector' => '.site-title a',
	'header-text'     => false,
	'height'          => 80,
	'width'           => 320,
) );

//* Add support for structural wraps
add_theme_support( 'genesis-structural-wraps', array(
	'header',
	'nav',
	'subnav',
	'site-inner',
	'footer-widgets',
	'footer',
) );

//* Add support for additional color style options
add_theme_support( 'genesis-style-selector', array(
	'agentpress-pro-blue'  => __( 'AgentPress Pro Blue', 'agentpress' ),
	'agentpress-pro-gold'  => __( 'AgentPress Pro Gold', 'agentpress' ),
	'agentpress-pro-green' => __( 'AgentPress Pro Green', 'agentpress' ),
) );

//* Filter the property details array
add_filter( 'agentpress_property_details', 'agentpress_property_details_filter' );
function agentpress_property_details_filter( $details ) {

    $details['col1'] = array( 
        __( 'Price:', 'agentpress' )   => '_listing_price', 
        __( 'Address:', 'agentpress' ) => '_listing_address', 
        __( 'City:', 'agentpress' )    => '_listing_city', 
        __( 'State:', 'agentpress' )   => '_listing_state', 
        __( 'ZIP:', 'agentpress' )     => '_listing_zip',
    );
    $details['col2'] = array( 
        __( 'MLS #:', 'agentpress' )       => '_listing_mls', 
        __( 'Square Feet:', 'agentpress' ) => '_listing_sqft', 
        __( 'Bedrooms:', 'agentpress' )    => '_listing_bedrooms', 
        __( 'Bathrooms:', 'agentpress' )   => '_listing_bathrooms', 
        __( 'Basement:', 'agentpress' )    => '_listing_basement',
    );

    return $details;

}

//* Reposition the primary navigation
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_before_header', 'genesis_do_nav' );

//* Reposition the secondary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 7 );

//* Reduce the secondary navigation menu to one level depth
add_filter( 'wp_nav_menu_args', 'agentpress_secondary_menu_args' );
function agentpress_secondary_menu_args( $args ){

	if( 'secondary' != $args['theme_location'] )
	return $args;

	$args['depth'] = 1;
	return $args;

}

//* Reposition the breadcrumbs
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
add_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_breadcrumbs' );

//* Remove comment form allowed tags
add_filter( 'comment_form_defaults', 'agentpress_remove_comment_form_allowed_tags' );
function agentpress_remove_comment_form_allowed_tags( $defaults ) {
	
	$defaults['comment_notes_after'] = '';
	return $defaults;

}

//* Add Discliamer to Footer
add_action( 'genesis_footer', 'agentpress_disclaimer' );
	function agentpress_disclaimer() {
		genesis_widget_area( 'disclaimer', array(
			'before' => '<div class="disclaimer widget-area">',
			'after'  => '</div>',
		) );
}

//* Customize Listings
add_action( 'genesis_before', 'agentpress_listing_styles' );
function agentpress_listing_styles() {
	if ( is_singular( 'listing' ) || is_post_type_archive( 'listing' ) ) {
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
	}
}

//* Add Default Image for Listings
add_filter( 'genesis_get_image', 'agentpress_default_image', 10, 2 );
function agentpress_default_image( $output, $args ) {
    global $post;

    if( 'listing' == get_post_type() && ! $output && $args['size'] == 'properties' && $args['format'] == 'html' ) {

        $output = sprintf( '<img class="attachment-properties" src="%s" alt="%s" />', get_stylesheet_directory_uri() .'/images/default-listing.png', get_the_title( $post->ID ) );

    }
    return $output;
}

//* Add support for 2-column footer widgets
add_theme_support( 'genesis-footer-widgets', 2 );

//* Add support for after entry widget
add_theme_support( 'genesis-after-entry-widget-area' );

//* Relocate after entry widget
remove_action( 'genesis_after_entry', 'genesis_after_entry_widget_area' );
add_action( 'genesis_after_entry', 'genesis_after_entry_widget_area', 5 );

//* Register widget areas
genesis_register_sidebar( array(
	'id'          => 'home-featured',
	'name'        => __( 'Home - Featured', 'agentpress' ),
	'description' => __( 'This is the featured section at the top of the homepage.', 'agentpress' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-top',
	'name'        => __( 'Home - Top', 'agentpress' ),
	'description' => __( 'This is the top section of the content area on the homepage.', 'agentpress' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-middle-1',
	'name'        => __( 'Home - Middle 1', 'agentpress' ),
	'description' => __( 'This is first widget-area in the middle section of the content area on the homepage.', 'agentpress' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-middle-2',
	'name'        => __( 'Home - Middle 2', 'agentpress' ),
	'description' => __( 'This is second widget-area in the middle section of the content area on the homepage.', 'agentpress' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-middle-3',
	'name'        => __( 'Home - Middle 3', 'agentpress' ),
	'description' => __( 'This is third widget-area in the middle section of the content area on the homepage.', 'agentpress' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-bottom',
	'name'        => __( 'Home - Bottom', 'agentpress' ),
	'description' => __( 'This is the bottom section of the content area on the homepage.', 'agentpress' ),
) );
genesis_register_sidebar( array(
	'id'          => 'listings-archive',
	'name'        => __( 'Listings Archive', 'agentpress' ),
	'description' => __( 'This is the widget-area at the top of the listings archive.', 'agentpress' ),
) );
genesis_register_sidebar( array(
	'id'          => 'disclaimer',
	'name'        => __( 'Disclaimer', 'agentpress' ),
	'description' => __( 'This is the disclaimer section of the footer.', 'agentpress' ),
) );
