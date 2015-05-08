<?php
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
?>