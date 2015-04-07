<?php

/**
 * Customize Background Image Control Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
 
add_action( 'customize_register', 'agentpress_customizer' );
function agentpress_customizer(){

	global $wp_customize;

	$wp_customize->add_section( 'agentpress-image', array(
		'title'    		=> __( 'Background Image', 'agentpress' ),
		'description' 	=> __( '<p>Personalize the top of your site home page by uploading an image.</p><p> The image used on the demo is <strong>1600 x 870 pixels</strong>.</p>', 'agentpress' ),
		'priority' 		=> 35,
	) );
	
	$wp_customize->add_setting( 'agentpress-home-image', array(
		'default'  => '',
		'type'     => 'option',
	) );
	 
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'home-image',
			array(
				'label' => __( 'Home Image Upload', 'agentpress' ),
				'section'  => 'agentpress-image',
				'settings' => 'agentpress-home-image',
			)
		)
	);
	
}
