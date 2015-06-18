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

	$format = ( function_exists( 'get_field' ) )? '%1$s[property_details]' : '<div style="border: 1px solid #900; background-color: #ecc; padding: 40px;"><p><strong>Missing Required Plugin:</strong><br />Please install the <a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">Advanced Custom Fields plugin</a>.</p></div>';

	$thumbnail = ( has_post_thumbnail() )? get_the_post_thumbnail( $post->ID, 'large' ) : '<img src="http://placehold.it/1200x600&text=Image+coming+soon!" />';

	$html = sprintf( $format, $thumbnail );

	echo do_shortcode( $html );
}

genesis();
