<?php

/* Metaboxes */

add_filter( 'cmb_meta_boxes', 'jp_student_informations' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function jp_student_informations( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_jp_';

	/**
	 * Information
	 */
	$meta_boxes['student_information'] = array(
		'id'         => 'student_information',
		'title'      => __( 'Whitelist', 'cmb' ),
		'pages'      => array( 'tc_whitelist', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
		'fields'     => array(
			
			array(
				'name' => __( 'Whitelist Name', 'cmb' ),
				'desc' => __( 'Whitelist Name (required)', 'cmb' ),
				'id'   => $prefix . 'student_reg',
				'type' => 'text_medium',
				// 'repeatable' => true,
			),
			
		),
	);
	

	// Add other metaboxes as needed

	return $meta_boxes;
}

add_action( 'init', 'student_info_initialize', 9999 );
/**
 * Initialize the metabox class.
 */
function student_info_initialize() {

	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once 'init.php';

}
