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
	//*
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

	$html = array();

	$format_overview = '<div class="two-thirds first">%1$s%3$s%4$s</div><div class="one-third">%2$s</div>';
	$thumbnail = ( has_post_thumbnail() )? get_the_post_thumbnail( $post->ID, 'large', array( 'class' => 'property-image' ) ) : '<img src="http://placehold.it/1200x600&text=Image+coming+soon!" />';
	$post_content = apply_filters( 'the_content', get_the_content() );

	// Demographics
	if( function_exists( 'get_field' ) ){
		$rows_html = array();
		$rows = get_field( 'demographics_rows' );
		$format_demographics = '';
		if( $rows ){
			$format_demographics = '<h3>Demographics</h3><table><colgroup><col style="width: 32%%;" /><col style="width: 17%%;" /><col style="width: 17%%;" /><col style="width: 17%%;" /><col style="width: 17%%;" /></colgroup>
				<thead>
					<tr>
						<th>Distance</th>
						<th>1 mi.</th>
						<th>3 mi.</th>
						<th>5 mi.</th>
						<th>7 mi.</th>
					</tr>
				</thead>
				<tbody>
					%1$s
				</tbody>
			</table>';

			foreach( $rows as $row ){
				$headings = array( '1mi', '3mi', '5mi', '7mi' );
				$columns = '';
				foreach( $headings as $heading ){
					$value = ( ! empty( $row[$heading] ) )? $row[$heading] : '--' ;
					if( is_numeric( $value ) )
						$value = number_format( $value );
					$columns.= '<td>' . $value . '</td>';
				}
				$rows_html[] = '<tr><td>' . $row['label'] . '</td>' . $columns . '</tr>';
			}
		} else {
			$rows_html[] = '<tr><td>No demographics data found.</td></tr>';
		}
		$demographics_html = sprintf( $format_demographics, implode( '', $rows_html ) );
	} else {
		$demographics_html = '<p><strong>MISSING PLUGIN:</strong><br />Please install the <a href="http://www.advancedcustomfields.com/pro/" target="_blank">ACF Pro Plugin.</a></p>';
	}

	// Additional Details
	if( function_exists( 'get_field' ) ){
		$format_additional_details = '<h3>Additional Details</h3><table><colgroup><col style="width: 32%%;" /><col style="width: 68%%;" /></colgroup>
			<tbody>
				%1$s
			</tbody>
		</table>';

		$rows_html = array();
		$additional_details_rows = array( 'total_size', 'traffic_count', 'anchor_stores' );
		foreach( $additional_details_rows as $field_name ){
			$field = get_field_object( $field_name );
			if( $field ){
				if( empty( $field['value'] ) )
					continue;

				switch( $field['type'] ){
					case 'number':
						$value = number_format( $field['value'] );
					break;
					case 'textarea':
						$value = build_comma_list( $field['value'] );
					break;
					default:
						$value = $field['value'];
					break;
				}

				if( ! empty( $field['prepend'] ) )
					$value = $field['prepend']. ' ' . $value;

				if( ! empty( $field['append'] ) )
					$value.= ' ' . $field['append'];

				$rows_html[] = '<tr><td>' . $field['label'] . '</td><td>' . $value . '</td></tr>';
			}
		}

		$additional_details_rows = get_field( 'additional_details_rows' );
		if( $additional_details_rows ){
			foreach( $additional_details_rows as $row ){
				$rows_html[] = '<tr><td>' . $row['label'] . '</td><td>' . $row['value'] . '</td></tr>';
			}
		}

		$additional_details_html = sprintf( $format_additional_details, implode( '', $rows_html ) );
	}

	$html['overview'] = sprintf( $format_overview, $thumbnail, $post_content, $demographics_html, $additional_details_html );

	// Site Plan
	$site_plan = get_field( 'site_plan' );
	$html['site_plan_tab'] = '';
	$html['site_plan_content'] = '';
	if( isset( $site_plan['url'] ) && ! empty( $site_plan['url'] ) && ! stristr( $site_plan['url'], '.pdf' ) ){
		$site_plan_content = '<a href="' . $site_plan['url'] . '" target="_blank"><img src="' . $site_plan['url'] . '" width="' . $site_plan['width'] . '" height="' . $site_plan['height'] . '" class="site-plan" /></a>';
		$sublots = get_field( 'sublots' );
		if( $sublots ){
			$sublots_format = '<h3>Available Sublots</h3><table><colgroup><col style="width: 30%%" /><col style="width: 70%%" /></colgroup><tbody>%1$s</tbody></table>';
			$sublots_rows = array();
			foreach( $sublots as $sublot ){
				$sublots_rows[] = '<tr><td><img src="' . $sublot['sublot_image']['url'] . '" width="' . $sublot['sublot_image']['width'] . '" height="' . $sublot['sublot_image']['height'] . '" /></td><td>' . $sublot['sublot_size'] . '</td></tr>';
			}

			$site_plan_content.= sprintf( $sublots_format, implode( '', $sublots_rows ) );
		}
		$html['site_plan_content'] = "\n" . '<div id="siteplan">' . $site_plan_content . '</div>';
		$html['site_plan_tab'] = "\n" . '<li><a href="#siteplan">Site Plan</a></li>';
	}

	// Google Map
	$map = get_field( 'map' );
	if( ! empty( $map ) ){
		$format_map = '<div class="acf-map"><div class="marker" data-lat="%1$s" data-lng="%2$s"></div></div>';

		$html['map'] = sprintf( $format_map, $map['lat'], $map['lng'] );
	}

	$html = '<div id="property-tabs">
		<ul>
			<li><a href="#overview">Overview</a></li>' . $html['site_plan_tab'] . '
			<li><a href="#map">Map</a></li>
		</ul>
		<div id="overview">' . $html['overview'] . '</div>' . $html['site_plan_content'] . '
		<div id="map">' . $html['map'] . '</div>
	</div>';

	echo do_shortcode( $html );
}

/**
 * Returns a comma separated list from a multi-line string.
 *
 * @since 1.0.0
 *
 * @param string $string Multi-line string.
 * @return string HTML unordered list.
 */
function build_comma_list( $string ){
	if( ! strstr( $string, "\n") )
		return $string;

	$array = explode( "\n", $string );
	foreach( $array as $value ){
		if( ! empty( $value ) )
			$items[] = trim( $value );
	}
	$list = implode( ', ', $items );

	return $list;
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
