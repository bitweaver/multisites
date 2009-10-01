<?php
// $Header: /cvsroot/bitweaver/_bit_multisites/admin/edit_sites.php,v 1.4 2009/10/01 13:45:44 wjames5 Exp $
// Copyright (c) 2005 bitweaver Multisites
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

require_once( '../../bit_setup_inc.php' );

include_once( MULTISITES_PKG_PATH.'Multisites.php');

global $gBitSmarty;

if( !empty( $_REQUEST['ms_id'] ) && !empty( $_REQUEST['action'] ) ) {
	if( $_REQUEST['action'] == 'edit' ) {
		$editSite = $gMultisites->getMultisites( $_REQUEST['ms_id'] );
		$editSite = $editSite[$_REQUEST['ms_id']];
		$gBitSmarty->assign( 'editSite', $editSite );
	} elseif( $_REQUEST['action'] == 'delete' ) {
		if( $gMultisites->expunge( $_REQUEST['ms_id'] ) ) {
			$successMsg = "The server was successfully deleted";
		}
	}
}


$layoutSettings = array(
	'site_top_bar_dropdown' => array(
		'label' => 'Dropdown menu',
		'note' => 'Use the CSS driven dropdown menus in the top bar. Compatibility and further reading can be found at <a class="external" href="http://www.htmldog.com/articles/suckerfish/dropdowns/">Suckerfish Dropdowns</a>.',
	),
	'site_top_bar' => array(
		'label' => 'Top Module Area',
		'note' => 'Check to enable the top module area site-wide.',
	),
	'site_right_column' => array(
		'label' => 'Right Module Area',
		'note' => 'Check to enable the right module area site-wide.',
	),
	'site_left_column' => array(
		'label' => 'Left Module Area',
		'note' => 'Check to enable the left module area site-wide.',
	),
	'site_bot_bar' => array(
		'label' => 'Bottom Module Area',
		'note' => 'Check to enable the bottom module area site-wide.',
	),
);
$gBitSmarty->assign( 'layoutSettings',$layoutSettings );

if( !empty( $_REQUEST['store_server'] ) ) {
 	// Special handling for linked items: bit_index and site_url_index
	if (!empty($_REQUEST["server_prefs"]["site_url_index"]) && $_REQUEST["server_prefs"]["bit_index"] == 'users_custom_home') {
		$_REQUEST["server_prefs"]["bit_index"] = $_REQUEST["server_prefs"]["site_url_index"];
	}

	// prepare the checkbox data for storage
	foreach( array_keys( $layoutSettings ) as $pref ) {
		$_REQUEST['server_prefs'][$pref] = ( isset( $_REQUEST[$pref][0] ) ? 'y' : 'n' );
	}

	if( $gMultisites->store( $_REQUEST ) ) {
		$successMsg = "The server was successfully saved";
	}
}

$gBitSmarty->assign( 'successMsg', empty( $successMsg ) ? NULL : $successMsg );
$gBitSmarty->assign( 'warningMsg', empty( $warningMsg ) ? NULL : $warningMsg );
if( !empty( $gMultisites->mErrors ) ) {
	foreach( $gMultisites->mErrors as $error ) {
		$errorMsg[] = $error;
	}
	$gBitSmarty->assign( 'errorMsg', $errorMsg );
}

$gBitSmarty->assign( 'listMultisites', $gMultisites->getMultisites() );

// Get list of available styles
$styles = $gBitThemes->getStyles( NULL, TRUE );
$gBitSmarty->assign( "styles", $styles );

// Get list of available languages
$languages = $gBitLanguage->listLanguages();
$gBitSmarty->assign("languages",$languages );

$gBitSystem->display( 'bitpackage:multisites/edit_sites.tpl', tra('Edit Sites') , array( 'display_mode' => 'edit' ));