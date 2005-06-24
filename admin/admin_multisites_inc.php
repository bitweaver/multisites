<?php
// $Header: /cvsroot/bitweaver/_bit_multisites/admin/admin_multisites_inc.php,v 1.1 2005/06/24 19:56:30 bitweaver Exp $
// Copyright (c) 2005 bitweaver Sample
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

include_once( THEMES_PKG_PATH.'theme_control_lib.php' );

if( !empty( $_REQUEST['ms_id'] ) && !empty( $_REQUEST['action'] ) ) {
	if( $_REQUEST['action'] == 'edit' ) {
		$editSite = $gMultisites->getMultisites( $_REQUEST['ms_id'] );
		$editSite = $editSite[$_REQUEST['ms_id']];
		$smarty->assign( 'editSite', $editSite );
	} elseif( $_REQUEST['action'] == 'delete' ) {
		if( $gMultisites->expunge( $_REQUEST['ms_id'] ) ) {
			$successMsg = "The server was successfully deleted";
		}
	}
}

$layoutSettings = array(
	'feature_top_bar' => array(
		'label' => 'Top bar menu',
	),
	'feature_top_bar_dropdown' => array(
		'label' => 'Dropdown menu',
	),
	'feature_right_column' => array(
		'label' => 'Right Module Column',
	),
	'feature_left_column' => array(
		'label' => 'Left Module Column',
	),
);
$smarty->assign( 'layoutSettings',$layoutSettings );

if( !empty( $_REQUEST['store_server'] ) ) {
	// Special handling for linked items: bitIndex and urlIndex
	if (!empty($_REQUEST["server_prefs"]["urlIndex"]) && $_REQUEST["server_prefs"]["bitIndex"] == 'custom_home') {
		$_REQUEST["server_prefs"]["bitIndex"] = $_REQUEST["server_prefs"]["urlIndex"];
	}

	// prepare the checkbox data for storage
	foreach( array_keys( $layoutSettings ) as $pref ) {
		$_REQUEST['server_prefs'][$pref] = ( isset( $_REQUEST[$pref][0] ) ? 'y' : 'n' );
	}

	if( $gMultisites->store( $_REQUEST ) ) {
		$successMsg = "The server was successfully saved";
	}
}

if( !empty( $_REQUEST['store_preferences'] ) ) {
	if( $gMultisites->storePreferences( $_REQUEST ) ) {
		$successMsg = "The preferences were successfully saved";
	}
}

$smarty->assign( 'successMsg', empty( $successMsg ) ? NULL : $successMsg );
$smarty->assign( 'warningMsg', empty( $warningMsg ) ? NULL : $warningMsg );
if( !empty( $gMultisites->mErrors ) ) {
	foreach( $gMultisites->mErrors as $error ) {
		$errorMsg[] = $error;
	}
	$smarty->assign( 'errorMsg', $errorMsg );
}

$smarty->assign( 'listMultisites', $gMultisites->getMultisites() );

// Get list of available styles
$styles = &$tcontrollib->getStyles( NULL, TRUE );
$smarty->assign( "styles", $styles );

// Get list of available languages
$languages = $gBitLanguage->listLanguages();
$smarty->assign("languages",$languages );
?>
