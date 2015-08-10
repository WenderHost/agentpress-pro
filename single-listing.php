<?php
/**
 * Page template for `listing` CPT.
 *
 *
 *
 * @link URL
 * @since 1.0.0
 *
 * @package AgentPress Theme
 * @subpackage Component
 */
//* Force full width content layout
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

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
	/*
	remove_action( 'genesis_sidebar', 'genesis_do_sidebar', 10 );
	if( function_exists( 'get_field' ) )
		add_action( 'genesis_sidebar', 'agentpress_listing_sidebar');
	/**/
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

	$info = '<span class="address">' . $map['address'] . ' (<a href="https://www.google.com/maps/place/' . urlencode( $map['address'] ) . '" target="_blank">View Map</a>)</span>';

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
		$format = '%1$s%3$s%4$s%%2$s';
	} else {
		$format = '<div style="border: 1px solid #900; background-color: #ecc; padding: 40px;"><p><strong>Missing Required Plugin:</strong><br />Please install the <a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">Advanced Custom Fields plugin</a>.</p></div>';
	}

	$html[] = ( has_post_thumbnail() )? get_the_post_thumbnail( $post->ID, 'large', array( 'class' => 'property-image' ) ) : '<img src="http://placehold.it/1200x600&text=Image+coming+soon!" />';

	$post_content = get_the_content();
	if( ! empty( $post_content ) )
		$html[] = '<div class="description">' . apply_filters( 'the_content', $post_content ) . '</div>';

	// Property Details
	$property_details_array = array( 'Total Square Footage' => 'total_square_footage', 'Current Anchor Stores' => 'current_anchor_stores', 'Population' => 'population', 'Average Household Income' => 'average_household_income', 'Traffic Count' => 'traffic_count' );
	foreach( $property_details_array as $label => $name ){
		$$name = get_field( $name );
	}
	$html[] = '<div class="one-half first"><strong>Total Square Footage:</strong><br />' . number_format( $total_square_footage ) . ' ft<sup>2</sup></div><div class="one-half"><strong>Traffic Count:</strong><br />' . number_format( $traffic_count ) . ' vehicles per day</div>';

	if( ! empty( $population ) || ! empty( $average_household_income ) || ! empty( $current_anchor_stores ) ){
		$list_vars = array( 'population', 'average_household_income', 'current_anchor_stores' );
		foreach( $list_vars as $var ){
			if( ! empty( $$var ) && strstr( $$var, "\n" ) )
				$$var = build_list( $$var );
		}

		$html[] = '<div class="one-third first"><strong>Population:</strong><br />' . $population . '</div><div class="one-third"><strong>Avg. Household Income:</strong><br />'. $average_household_income .'</div><div class="one-third"><strong>Current Anchor Stores:</strong><br />' . $current_anchor_stores . '</div>';
	}



	$map = get_field( 'map' );
	if( ! empty( $map ) ){
		$format_map = '<div class="acf-map"><div class="marker" data-lat="%1$s" data-lng="%2$s"></div></div>';

		$html[] = sprintf( $format_map, $map['lat'], $map['lng'] );
	}

	$html = implode( '', $html );

	echo do_shortcode( $html );
}

/**
 * Returns an HTML list from a multi-line string.
 *
 * @since 1.0.0
 *
 * @param string $string Multi-line string.
 * @return string HTML unordered list.
 */
function build_list( $string ){
	if( ! strstr( $string, "\n") )
		return $string;

	$array = explode( "\n", $string );
	foreach( $array as $value ){
		$li[] = '<li>' . $value . '</li>';
	}
	$ul = '<ul>' . implode( '', $li ) . '</ul>';

	return $ul;
}

genesis();
