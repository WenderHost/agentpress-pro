<?php
/**
 * Builds the sidebar for `listing` CPT
 *
 * @since 1.0.0
 *
 * @return void
 */
function agentpress_listing_sidebar(){
	$format = '<section class="widget%4$s"%3$s><div class="widget-wrap">%1$s<div class="textwidget">%2$s</div></div></section>';

	$widgets = array(
		array(
			'title' => 'Property Resources',
			'content' => function(){
				global $post;
				$resources = array();
				$resource_fields = array( 'site_plan' => 'Site Plan', 'property_brochure' => 'Property Brochure' );
				foreach( $resource_fields as $field => $label ){
					$$field = get_field( $field );
					if( ! empty( $$field ) )
						$resources[$field] = array( 'label' => $label, 'value' => $$field );
				}

				if( ! empty( $resources ) ){
					foreach( $resources as $key => $field ){
						$html.= '<li><a href="' . $field['value'] . '" target="_blank">' . $field['label'] . '</a></li>';
					}

					return '<ul>' . $html . '</ul>';
				} else {
					return null;
				}
			},
			'classes' => 'primary-color',
		),
		array(
			'title' => 'Request Information',
			'content' => '<p>Contact us for further information about this property:</p><ul>
			<li><a href="mailto:info@hpiknox.com?subject=Information%20Request: ' . get_the_title() . '" target="_blank">info@hpiknox.com</a></li>
			</ul>',
			'classes' => 'primary-color',
		),
	);

	foreach( $widgets as $widget ){
		// Check `content` for an anonymous function
		$content = ( is_callable( $widget['content'] ) )? $widget['content']() : $widget['content'];
		if( empty( $content ) )
			continue;

		$title = ( $widget['title'] && ! empty( $widget['title'] ) )? '<h4 class="widget-title widgettitle">' . $widget['title'] . '</h4>' : '' ;
		$content = do_shortcode( $content );

		$attributes = '';
		if( $widget['attr'] && ! empty( $widget['attr'] ) && is_array( $widget['attr'] ) ){
			foreach( $widget['attr'] as $key => $value ){
				$attributes.= $key . '="' . esc_attr( $value ) . '"';
			}
		}
		$classes = ( $widget['classes'] && ! empty( $widget['classes'] ) )? ' ' . $widget['classes'] : '';

		echo sprintf( $format, $title, $content, $attributes, $classes );
	}
}
?>