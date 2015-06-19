<?php

/**
 * Page template for `listing` CPT.
 *
 *
 *
 * @link URL
 * @since 1.x.x
 *
 * @package AgentPress Theme
 * @subpackage Component
 */

add_action( 'genesis_meta', 'agentpress_listing_genesis_meta' );
/**
 * Setup our template.
 *
 * @since 1.0.0
 *
 * @return void
 */
function agentpress_listing_genesis_meta() {
	remove_action( 'genesis_entry_content', 'genesis_do_post_content', 10 );
	remove_action( 'genesis_sidebar', 'genesis_do_sidebar', 10 );
	if( function_exists( 'get_field' ) )
		add_action( 'genesis_sidebar', 'agentpress_listing_sidebar');
}

add_filter( 'genesis_post_info', 'agentpress_listing_post_info' );
/**
 * Adding address to post info for `listing` CPT.
 *
 * @since 1.0.0
 *
 * @param string $info Post info.
 * @return string Property address with Google Map link.
 */
function agentpress_listing_post_info( $info ){
	global $post;

	$map = get_field( 'map', $post->ID );
	if( ! is_array( $map ) || 0 == count( $map ) )
		return false;

	$info = '<span class="address">' . $map['address'] . ' (<a href="https://www.google.com/maps/place/' . urlencode( $map['address'] ) . '" target="_blank">Map</a>)</span>';

	return $info;
}


add_action( 'genesis_entry_content', 'agentpress_listing_content' );
/**
 * Add content to the page body.
 *
 * @since 1.0.0
 *
 * @return void
 */
function agentpress_listing_content(){
	global $post;

	if( function_exists( 'get_field') ){
		$format = '%1$s%2$s';
	} else {
		$format = '<div style="border: 1px solid #900; background-color: #ecc; padding: 40px;"><p><strong>Missing Required Plugin:</strong><br />Please install the <a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">Advanced Custom Fields plugin</a>.</p></div>';
	}

	$thumbnail = ( has_post_thumbnail() )? get_the_post_thumbnail( $post->ID, 'large' ) : '<img src="http://placehold.it/1200x600&text=Image+coming+soon!" />';

	$map = get_field( 'map' );
	if( ! empty( $map ) ){
		$format_map = '<div class="acf-map"><div class="marker" data-lat="%1$s" data-lng="%2$s"></div></div>';

		$map_html = sprintf( $format_map, $map['lat'], $map['lng'] );
	}

	$html = sprintf( $format, $thumbnail, $map_html );

	echo do_shortcode( $html );
}

genesis();
