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
 * 		@type str $photo URL of team member's photo.
 * }
 * @param str $content Text of team member's bio.
 * @return str HTML for team member's bio.
 */
add_shortcode( 'teammember', 'agentpress_team_member' );
function agentpress_team_member( $atts, $content ){
	extract( shortcode_atts( array(
		'photo' => 'http://placehold.it/600x600',
	), $atts ) );

	if( empty( $content ) )
		$content = '<code>Please enter a bio for this team member.</code>';

	$format = '<div class="clearfix teammember">
		<div class="one-third first"><img src="%1$s" /></div>
		<div class="two-thirds">%2$s</div>
	</div>';

	$html = sprintf( $format, $photo, $content );

	return $html;
}
?>