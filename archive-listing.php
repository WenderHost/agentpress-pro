<?php
/**
 * This file adds the Archive Listings Template to the AgentPress Pro Theme.
 *
 * @author StudioPress
 * @package AgentPress Pro
 * @subpackage Customizations
 */

//* Force full width layout
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

//* Add listings archive widget area
add_action( 'genesis_before_content_sidebar_wrap', 'agentpress_archive_widget' );
function agentpress_archive_widget() {

	if ( is_active_sidebar( 'listings-archive' ) ) {

		genesis_widget_area( 'listings-archive', array(
			'before' => '<div class="listing-archive full-width widget-area">',
			'after'  => '</div>',
		) );
	}
}

//* Relocate archive intro text
remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
add_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_taxonomy_title_description' );

//* Remove the standard loop
remove_action( 'genesis_loop', 'genesis_do_loop' );

add_action( 'genesis_loop', 'agentpress_listing_archive_loop' );
/**
 * Custom loop for listing archive page
 */
function agentpress_listing_archive_loop() {

	if ( have_posts() ) : while ( have_posts() ) : the_post();

		$listing_text = genesis_get_custom_field( '_listing_text' );
		$name 		 = get_the_title();
		$address = genesis_get_custom_field( '_listing_address' );
		$city = genesis_get_custom_field( '_listing_city' );
		$state = genesis_get_custom_field( '_listing_state' );
		$zip = genesis_get_custom_field( '_listing_zip' );
		$sq_ft = genesis_get_custom_field( '_listing_sqft' );

		$loop = ''; // init

		$loop .= sprintf( '<a href="%s">%s</a>', get_permalink(), genesis_get_image( array( 'size' => 'properties' ) ) );

		if( $sq_ft ) {
			$loop .= sprintf( '<span class="listing-price">%s</span>', $sq_ft . ' ft<sup>2</sup>' );
		}

		if( $listing_text ) {
			$loop .= sprintf( '<span class="listing-text">%s</span>', $listing_text );
		}

		if( $name )
			$loop .= sprintf( '<span class="listing-title"><a href="%s">%s</a></span>', get_permalink() , $name );

		if ( $address != $name ) {
			$loop .= sprintf( '<span class="listing-address">%s</span>', $address );
		}

		if ( $city || $state || $zip ) {

			//* count number of completed fields
			$pass = count( array_filter( array( $city, $state, $zip ) ) );

			//* If only 1 field filled out, no comma
			if ( 1 == $pass ) {
				$city_state_zip = $city . $state . $zip;
			}
			//* If city filled out, comma after city
			elseif ( $city ) {
				$city_state_zip = $city . ", " . $state . " " . $zip;
			}
			//* Otherwise, comma after state
			else {
				$city_state_zip = $city . " " . $state . ", " . $zip;
			}

			$loop .= sprintf( '<span class="listing-city-state-zip">%s</span>', trim( $city_state_zip ) );

		}

		if( $address == $name )
			$loop .= '<span class="listing-address">&nbsp;</span>';

		//$loop .= sprintf( '<a href="%s" class="more-link">%s</a>', get_permalink(), __( 'View Listing', 'agentpress' ) );

		/** wrap in post class div, and output **/
		printf( '<div class="%s"><div class="widget-wrap"><div class="listing-wrap">%s</div></div></div>', join( ' ', get_post_class() ), $loop );

	endwhile;

	genesis_posts_nav();

	else: printf( '<div class="entry"><p>%s</p></div>', __( 'Sorry, no properties matched your criteria.', 'agentpress' ) );

	endif;

}

genesis();
