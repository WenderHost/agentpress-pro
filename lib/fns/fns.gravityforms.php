<?php
/**
 * Functions for use with Gravity Forms.
 *
 * @since 1.0.0
 *
 * @package AgentPress Pro
 * @subpackage Component
 */

add_filter( 'gform_pre_render_1', 'populate_listings' );
add_filter( 'gform_pre_validation_1', 'populate_listings' );
add_filter( 'gform_pre_submission_filter_1', 'populate_listings' );
add_filter( 'gform_admin_pre_render_1', 'populate_listings' );
/**
 * Populates Gravity Form ID 1 <select> with all `listing` CPT titles
 *
 * @since 1.0.0
 *
 * @param array $form Gravity Forms form array.
 * @return array Filtered Gravity Forms array.
 */
function populate_listings( $form ){

	//echo '<pre>$form = '.print_r($form,true).'</pre>';


	foreach ( $form['fields'] as &$field ) {
		if ( $field->type != 'select' || strpos( $field->cssClass, 'populate-listings' ) === false )
		    continue;

		$posts = get_posts( 'numberposts=-1&post_type=listing&orderby=title&order=ASC' );

		$choices = array();
		foreach( $posts as $post ){
			$choices[] = array( 'text' => $post->post_title, 'value' => $post->post_title );
		}

		$field->placeholder = 'Select a Property';
		$field->choices = $choices;
	}

	return $form;
}
?>