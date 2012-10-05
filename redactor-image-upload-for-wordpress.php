<?php
/**
 * Redactor Image Upload For WordPress
 *
 * This script is meant to work with WordPress and the Redactor.js WYSIWYG jQuery Plugin.
 * It provides functionality that's necessary for users to upload files from Redactor's editor
 * on a WordPress page, and then upload the image into the WordPress uploads directory.
 *
 * It aims to use WordPress conventions by placing it in the uploads/YEAR/MONTH directories
 * and will create said directories if they don't already exist. The script will also 
 * look to rename the uploaded file if another one exists on the server by the same name.
 *
 * The script is meant to be kept in a /lib/ directory of a given theme or plugin, but should
 * work regardless of where it's placed.
 * 
 * @author	Tom McFarlin <tom@tommcfarlin.com>
 * @package	redactor-image-upload-for-wordpress
 * @see		http://tommcfarlin.com/redactor-for-wordpress/
 * @since 	0.1
 */

/*--------------------------------------------------------------------------------------------*
 * POST Functionality
 *--------------------------------------------------------------------------------------------*/
 
// If the files array isn't empty
if( ! empty( $_FILES ) ) {

	// Verify that the file being uploaded is of a supported type
	if( is_valid_file_type( $_FILES['file']['type'] ) ) {

		// Get the fully-qualified path to the uploads directory using the location of this script
		// and append the filename
		$file = get_wp_uploads_directory( get_script_path() ) . $_FILES['file']['name'];

		// Read the parts of the filename so we can upload it, determine if other files exist with
		// the same name, and rename the file, if needed. 
		$file_parts = explode( '.', $_FILES['file']['name'] );
		$filename = $file_parts[0];
		$filetype = $file_parts[ count( $file_parts ) - 1 ];

		// First, check to see if the file exists...
		if( filename_already_exists( $filename, $filetype ) ) {

			// If so, we need to count the total number of files that already exist with this name
			$total = get_total_filenames( $filename, $filetype );

			// Now let's rebuild the file name adding an incremented value so not to overwrite existing files
			$file = $filename . '-' . ( $total + 1 ) . '.' . $filetype;
			
			// Update $_FILES so that it references the new filename
			$_FILES['file']['name'] = $file;
			
			// And since we had to rename the file, update the $file reference
			$file = get_wp_uploads_directory( get_script_path() ) . $_FILES['file']['name'];

		} // end if/else

		// Now actually copy the files over to the uploads directory
		copy( $_FILES['file']['tmp_name'], $file );
		
		// Send the JSON back to the browser for Redactor
		echo stripslashes( json_encode( array( 'filelink'	=>	get_wp_uploads_url() . $_FILES['file']['name'] ) ) );

	} // end if

} // end if

/*--------------------------------------------------------------------------------------------*
 * Helper Functions
 *--------------------------------------------------------------------------------------------*/

/**
 * Determines if a file with the specified filename already exists.
 *
 * @param	$filename	The name of the file
 * @param	$filetype	The type of the file
 * @return				True if there is at least one file of the same name and same type
 * @since	0.1
 */
function filename_already_exists( $filename, $filetype ) {
	return 1 <= get_total_filenames( $filename, $filetype );
} // end 

/**
 * Determines how many files exist that already have the same name.
 *
 * @param	$filename	The name of the file
 * @param	$filetype	The type of the file
 * @return				The total number of files that exist with a similar name.
 * @since	0.1
 */
function get_total_filenames( $filename, $filetype ) {
	return count( glob( get_wp_uploads_directory( get_script_path() ) . $filename . '*' . '.' . $filetype ) );
} // end get_total_filenames

/**
 * Determines where this particular file is located within the context of the WordPress theme
 * or plugin.
 *
 * @return	The path of where this script is located
 * @since	0.1
 */
function get_script_path() {
	return dirname( __FILE__ );
} // end get_script_path

/**
 * Locates the path to the WordPress uploads directory. If the current year and/or month directories
 * don't exist, then it will create them.
 *
 * @param	$script_path	The location of this script.
 * @return					The path to the WordPress uploads directory (including year and month)
 * since	1.0
 */
function get_wp_uploads_directory( $script_path ) {
	
	// First, get the location of where /uploads/ is located
	$paths = explode( 'wp-content', $script_path );
	$path = $paths[0] . 'wp-content/uploads/';
	
	// Look for the year directory. If it doesn't exist, create it.
	if ( file_exists( $path . '/' . date( 'Y' ) ) ) {
		$path .= date( 'Y' );
	} else {
		mkdir( $path . '/' . date( 'Y' ) );
	} // end if
	$path .= '/';

	// Look for themonth directory. If it doesn't exist, create it.
	if ( file_exists( $path . '/' . date( 'm' ) ) ) {
		$path .= date( 'm' );
	} else {
		mkdir( $path . '/' . date( 'm' ) );
	} // end if
	$path .= '/';

	return $path;
	
} // end get_wp_uploads_directory

/**
 * Returns the actual URL to the WordPress uploads directory.
 *
 * @return	The URL to the WordPress upload directory.
 * @since	0.1
 */
function get_wp_uploads_url() {
	
	// Get the parts of the upload path
	$path = get_wp_uploads_directory( get_script_path() );
	$path_parts = explode( 'wp-content', $path );
	
	// Build up the URL to the uploads directory
	$uploads = 'http://' . $_SERVER['HTTP_HOST'] . '/wp-content' . $path_parts[ count( $path_parts ) - 1 ];
	
	return $uploads;
	
} // end get_wp_uploads_url

/**
 * Determines if the specified type if a valid file type to be uploaded.
 *
 * @param	$type	The file type attempting to be uploaded.
 * @return			Whether or not the specified file type is able to be uploaded.
 * @since	0.1
 */ 
function is_valid_file_type( $type ) { 

	$is_valid_file_type = false;

	switch (strtolower( $type ) ) {
	
		case 'image/png':
		case 'image/jpg':
		case 'image/jpeg':
		case 'image/pjpeg':
		case 'image/gif':
			$is_valid_file_type = true;
			break;
		
		default:
			$is_valid_file_type = false;
			break;
			
	} // end switch/case
	
	return $is_valid_file_type

} // end is_valid_file_type
 
?>