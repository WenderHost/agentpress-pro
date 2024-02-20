<?php
/**
 * Custom loop for listing archive page
 */
function agentpress_listing_archive_loop() {

	if ( have_posts() ) : while ( have_posts() ) : the_post();

		$listing_text = genesis_get_custom_field( '_listing_text' );
		$name = get_the_title();
		if( function_exists( 'get_field' ) ){
			$map = get_field( 'map' );
			$address = ( is_array( $map ) && array_key_exists( 'address', $map ) && is_string( $map['address'] ) )? $map['address'] : '---' ;
		} else {
			$address = '**Missing ACF Plugin**';
		}



		$loop = ''; // init

		$loop .= sprintf( '<a href="%s">%s</a>', get_permalink(), genesis_get_image( array( 'size' => 'properties' ) ) );

		$sq_ft = get_field( 'total_size' );
		if( is_numeric( $sq_ft ) ){
			$sq_ft = number_format( $sq_ft );
			$sq_ft = ( empty( $sq_ft ) )? $sq_ft = '-- TBA --' : $sq_ft . ' ft<sup>2</sup>' ;
		}

		if( $sq_ft ) {
			$loop .= sprintf( '<span class="listing-price">%s</span>', $sq_ft );
		}

		if( empty( $listing_text ) ){
			$terms = get_the_terms( get_the_ID(), 'location' );
			if( $terms )
				//echo '<pre>$terms = '.print_r( $terms, true ).'</pre>';
				if( ! is_wp_error( $terms ) && is_array( $terms ) && 0 < count( $terms ) )
					$listing_text = $terms[0]->name;
		}

		if( $listing_text ) {
			$loop .= sprintf( '<span class="listing-text">%s</span>', $listing_text );
		}

		if( $name && ! stristr( $address, $name ) )
			$loop .= sprintf( '<span class="listing-title"><a href="%s">%s</a></span>', get_permalink() , $name );

		if ( $address != $name ) {
			$formatted_address = $address;

			/*
			 * If the property's name == the 1st line of its address,
			 * link the first line of the address to the property's page,
			 * and add a double br to make the content the same height as
			 * properties with names != the 1st line of their addresses.
			 */
			if( stristr( $address, $name ) )
				$formatted_address = preg_replace( '/(.*),/U', '<a href="' . get_permalink() . '">${1}</a>,', $formatted_address, 1  ) . '<br /><br />';

			$formatted_address = preg_replace( '/,/', '<br \/>', $formatted_address, 1 );
			$formatted_address = str_replace( array( ', United States', ', US'), '', $formatted_address );
			$loop .= sprintf( '<span class="listing-address">%s</span>', $formatted_address );
		}

		if ( isset( $city ) || isset( $state ) || isset( $zip ) ) {

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

/**
 * Filters the listings widget from the AgentPress Listing plugin.
 *
 * @param      string  $loop   The string generated by the Featured Listing widget.
 */
function agentpress_theme_features_listings_filter( $loop ){
	global $post;

	$listing_text = genesis_get_custom_field( '_listing_text' );
	$name = get_the_title();
	if( function_exists( 'get_field' ) ){
		$map = get_field( 'map' );
		$address = $map['address'];
	} else {
		$address = '**Missing ACF Plugin**';
	}

	$sq_ft = get_field( 'total_size' );
	if( is_numeric( $sq_ft ) )
		$sq_ft = number_format( $sq_ft );
	$sq_ft = ( empty( $sq_ft ) )? $sq_ft = '-- TBA --' : $sq_ft . ' ft<sup>2</sup>' ;

	if( $sq_ft ) {
		$loop .= sprintf( '<span class="listing-price">%s</span>', $sq_ft );
	}

	if( empty( $listing_text ) ){
		$terms = get_the_terms( get_the_ID(), 'location' );
		if( $terms )
			//echo '<pre>$terms = '.print_r( $terms, true ).'</pre>';
			if( ! is_wp_error( $terms ) && is_array( $terms ) && 0 < count( $terms ) )
				$listing_text = $terms[0]->name;
	}

	if( $listing_text ) {
		$loop .= sprintf( '<span class="listing-text">%s</span>', $listing_text );
	}

	if( $name && ! stristr( $address, $name ) )
		$loop .= sprintf( '<span class="listing-title"><a href="%s">%s</a></span>', get_permalink() , $name );

	if ( $address != $name ) {
		$formatted_address = $address;

		/*
		 * If the property's name == the 1st line of its address,
		 * link the first line of the address to the property's page,
		 * and add a double br to make the content the same height as
		 * properties with names != the 1st line of their addresses.
		 */
		if( stristr( $address, $name ) )
			$formatted_address = preg_replace( '/(.*),/U', '<a href="' . get_permalink() . '">${1}</a>,', $formatted_address, 1  ) . '<br /><br />';

		$formatted_address = preg_replace( '/,/', '<br \/>', $formatted_address, 1 );
		$formatted_address = str_replace( array( ', United States', ', US'), '', $formatted_address );
		$loop .= sprintf( '<span class="listing-address">%s</span>', $formatted_address );
	}

	return $loop;
}
add_filter( 'agentpress_featured_listings_widget_loop', 'agentpress_theme_features_listings_filter' );