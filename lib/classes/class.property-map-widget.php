<?php
/**
 * Class for the home page `Property Map Widget`
 *
 * @since 1.0.0
 *
 * @package AgentPress Pro
 * @subpackage Component
 */

class Property_Map_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'property_map_widget', // Base ID
			__( 'Property Map Widget', 'agentpress' ), // Name
			array( 'description' => __( 'Displays a Google Map with markers for each property listing on the site.', 'agentpress' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$posts = get_posts( 'numberposts=-1&post_type=listing&orderby=title&order=ASC' );

		if( ! $posts )
			return '<p><strong>ERROR:</strong> No properties found!</p>';

		wp_enqueue_script( 'google-maps' );
		wp_enqueue_script( 'listing-js' );

		$maps = array();
		foreach( $posts as $post ){
			$map = get_field( 'map', $post->ID );
			//echo '<pre>$map = '.print_r($map,true).'</pre>';
			$format_map = '<div class="marker" data-lat="%1$s" data-lng="%2$s"><h4><a href="%3$s" target="_blank" title="Click for Details">%4$s</a></h4><p class="entry-meta"><span class="address">%6$s</span></p><a href="%3$s" target="_blank" title="Click for Details">%5$s</a></div>';

			$thumbnail = ( has_post_thumbnail( $post->ID ) )? get_the_post_thumbnail( $post->ID, 'properties', array( 'class' => 'map-image' ) ) : '<img src="http://placehold.it/500x300&text=Image+coming+soon!" />';

			$html = sprintf( $format_map, $map['lat'], $map['lng'], get_permalink( $post->ID ), get_the_title( $post->ID ), $thumbnail, $map['address'] );
			$maps[] = $html;
		}

		echo $args['before_widget'];
		$format_maps = '<div class="acf-map home">%1$s</div>';
		echo sprintf( $format_maps, implode( '', $maps ) );
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		?><p>This widget has no settings.</p><?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}

add_action( 'widgets_init', function(){
     register_widget( 'Property_Map_Widget' );
});
?>