<?php

/**
 * Displays a Google Map of Past Developments.
 *
 * @param      array  $atts   {
 *   @type  string  $id           The spreadsheet's ID.
 *   @type  bool    $render_table Whether or not to render the table. Default `true`.
 *   @type  string  $tempalte     The template file to render.
 * }
 */
function agentpress_past_developments( $atts ){
	$atts = shortcode_atts( array(
		'id' => null,
		'render_table' => true,
		'template' => 'past-developments-table'
	), $atts );

	//$json_url = sprintf( 'https://spreadsheets.google.com/feeds/list/%s/od6/public/basic?alt=json', $atts['id'] );
	$json_url = sprintf( 'https://opensheet.elk.sh/%s/Sheet1', $atts['id'] );
	$sheet_url = sprintf( 'https://docs.google.com/spreadsheet/pub?hl=en_US&hl=en_US&key=%s&output=html', $atts['id'] );

	wp_enqueue_script( 'datatables' );
	wp_enqueue_style( 'datatables' );
	wp_enqueue_script( 'agentpress-dt', get_stylesheet_directory_uri() . '/js/agentpress-dt.js', array( 'datatables', 'google-maps' ), filemtime( get_stylesheet_directory() . '/js/agentpress-dt.js' ) );

	if( 'past-developments-map' == $atts['template'] )
		$atts['render_table'] = false;
	wp_localize_script( 'agentpress-dt', 'wpvars', [
		'json' => $json_url,
		'sheet' => $sheet_url,
		'key' => $atts['id'],
		'render_table' => $atts['render_table'],
		'json_url'	=> get_stylesheet_directory_uri() . '/js/past-developments.json',
	]);

	$template_file = ( file_exists( get_stylesheet_directory() . '/lib/includes/' . $atts['template'] . '.html' ) )? get_stylesheet_directory() . '/lib/includes/' . $atts['template'] . '.html' : get_stylesheet_directory() . '/lib/includes/past-developments-table.html' ;

	$html = file_get_contents( $template_file );

	echo $html;
}
add_shortcode( 'past-developments', 'agentpress_past_developments' );

/**
 * Returns HTML for displaying a `Team Member`.
 *
 * 1/3 + 2/3 column layout. Team member photo in
 * the left column, bio in the right.
 *
 * @since 1.0.0
 *
 * @param array $atts {
 * 		@type str/int $photo URL of team member's photo, or ID of attachment.
 * 		@type str $name Name of the team member. If this value isn't empty, this triggers a formatted content layout.
 * 		@type str $title Team member's title.
 * 		@type str $bio Team member's bio.
 * }
 * @param str $content Text of team member's bio.
 * @return str HTML for team member's bio.
 */
add_shortcode( 'teammember', 'agentpress_team_member' );
function agentpress_team_member( $atts, $content = '' ){
	extract( shortcode_atts( array(
		'photo' => 'http://placehold.it/600x600&text=Placeholder',
		'name'	=> '',
		'title'	=> '',
		'bio'	=> '',
		'url'	=> '',
	), $atts ) );

	if( empty( $content ) )
		$content = '<strong>No bio entered!</strong><br />Please enter a bio for this team member.';

	if( is_numeric( $photo ) ){
		$attachment = wp_get_attachment_image_src( $photo, 'large' );
		$photo = ( is_array( $attachment ) )? $attachment[0] : 'http://placehold.it/600x600&text=Placeholder';
	}

	$format = '<div class="clearfix teammember">
		<div class="one-third first photo"><img src="%1$s" /></div>
		<div class="two-thirds bio">%2$s</div>
	</div>';

	if( ! empty( $name ) ){
		$format_content = '<h2>%1$s%2$s</h2>%3$s';
		$title = ( ! empty( $title ) )? '<div class="archive-title">' . $title . '</div>' : '';
		$content = sprintf( $format_content, $name, $title, $bio );
	}

	if( ! empty( $url ) && true == filter_var( $url, FILTER_VALIDATE_URL ) )
		$photo = $url;

	$html = sprintf( $format, $photo, $content );

	return $html;
}

add_action( 'init', 'agentpress_team_member_ui' );
function agentpress_team_member_ui(){
	if( ! function_exists( 'shortcode_ui_register_for_shortcode' ) )
		return;

	shortcode_ui_register_for_shortcode( 'teammember', array(
		'label' => 'Team Member',
		'listItemImage' => 'dashicons-id',
		'attrs' => array(
			array(
				'label'	=> 'Name',
				'attr'	=> 'name',
				'type'	=> 'text',
			),
			array(
				'label'	=> 'Title',
				'attr'	=> 'title',
				'type'	=> 'text',
			),
			array(
				'label'	=> 'Bio',
				'attr'	=> 'bio',
				'type'	=> 'textarea',
			),
			array(
				'label'	=> 'Photo',
				'attr'	=> 'photo',
				'type'	=> 'attachment',
			),
		),
	) );
}

/**
 * Adds a url attribute to [teammember] shortcodes.
 *
 * We use a valid URL value in the url attribute to override
 * the attachment ID found in the photo attribute. This
 * allows this shortcode to work properly when it is in
 * content which has been “RAMPed” from a staging to
 * production environement where the attachment IDs are not
 * the same.
 *
 * @since 1.x.x.
 *
 * @param int $post_id Post ID.
 * @return void
 */
add_action( 'save_post', 'agentpress_team_member_add_url' );
function agentpress_team_member_add_url( $post_id ){

	// Skip if this is a post revision
	if( wp_is_post_revision( $post_id ) )
		return;

	$post = get_post( $post_id );
	$post_content = $post->post_content;

	// Skip if missing [teammember] shortcode
	if( ! has_shortcode( $post_content, 'teammember' ) )
		return;

	preg_match_all( '/\[teammember\s(.*)\]/', $post_content, $matches );

	if( ! $matches )
		return;

	$shortcodes = $matches[0];
	$attributes = $matches[1];
	foreach( $shortcodes as $key => $shortcode ){

		if( ! stristr( $shortcode, 'photo=' ) )
			continue;

		preg_match( '/photo="([0-9]+)"/', $shortcode, $match );
		if( ! $match )
			continue;

		$attachment_id = $match[1];

		$attachment = wp_get_attachment_image_src( $attachment_id, 'large' );
		if( is_array( $attachment ) )
			$photo_url = $attachment[0];

		if( ! stristr( $attributes[$key], 'url=' ) ){
			$new_attributes = $attributes[$key] . ' url="' . $photo_url . '"';
			$new_shortcode = str_replace( $attributes[$key], $new_attributes, $shortcode );
			$post_content = str_replace( $shortcode, $new_shortcode, $post_content );
		}
	}

	// Unhook this function to prevent an infinite loop:
	remove_action( 'save_post', 'agentpress_team_member_add_url' );

	wp_update_post( array( 'ID' => $post_id, 'post_content' => $post_content ) );

	add_action( 'save_post', 'agentpress_team_member_add_url' );
}
?>