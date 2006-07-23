<?php
// $Header: /cvsroot/bitweaver/_bit_multisites/bit_setup_inc.php,v 1.6 2006/07/23 00:56:02 jht001 Exp $

global $gBitSystem, $gMultisites, $gBitLanguage, $gLibertySystem;

$registerHash = array(
	'package_name' => 'multisites',
	'package_path' => dirname( __FILE__ ).'/',
	'service' => LIBERTY_SERVICE_METADATA,
);

$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'multisites' ) ) {
	require_once( MULTISITES_PKG_PATH.'Multisites.php' );
	$gMultisites = new Multisites;
	
	// Register service hooks
	$gLibertySystem->registerService( LIBERTY_SERVICE_METADATA, 
					  MULTISITES_PKG_NAME, 
	array(
	        // Data Hooks
	        // TODO: Add hook to display site restricitons when allowed to
	        //		'content_display_function' => 'multisites_content_display',
		'content_preview_function' => 'multisites_content_preview',
		'content_edit_function' => 'multisites_content_edit',
		'content_store_function' => 'multisites_content_store',
		'content_expunge_function' => 'multisites_content_expunge',
		// Template Hooks
		'content_edit_'.( $gBitSystem->isFeatureActive( 'multisites_use_jstab' ) ? 'tab_' : 'mini_' ).'tpl' => 
			'bitpackage:multisites/multisites_input_'.( $gBitSystem->isFeatureActive( 'multisites_use_jstab' ) ? '' : 'mini_' ).'inc.tpl',
		// TODO: Add hook to display site restrictions when allowed to
		//		'content_view_tpl' => 'bitpackage:multisites/display_members.tpl',
		// SQL Hooks
		'content_list_sql_function' => 'multisites_content_sql',
		'content_load_sql_function' => 'multisites_content_sql',
	));

	// override existing settings with the ones set
	if( !empty( $gMultisites->mPrefs ) ) {
		foreach( $gMultisites->mPrefs as $pref => $value ) {
			if( !empty( $value ) ) {
				$override[$pref] = $value;
			}
		}

		$gBitSystem->mConfig = array_merge( $gBitSystem->mConfig, $override );
		if( !empty( $gMultisites->mPrefs['bitlanguage'] ) ) {
			$gBitLanguage->setLanguage( $gMultisites->mPrefs['bitlanguage'] );
		}
	}
}
?>
