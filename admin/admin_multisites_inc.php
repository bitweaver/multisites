<?php
// $Header: /cvsroot/bitweaver/_bit_multisites/admin/admin_multisites_inc.php,v 1.11 2008/07/17 07:04:15 lsces Exp $
// Copyright (c) 2005 bitweaver Sample
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$multisitesSettings = array(
	'multisites_per_site_content' => array(
		'label' => 'Use Per Site Content',
		'note' => 'Allow content to be limited to a subset of sites',
	),
	'multisites_use_jstab' => array(
		'label' => 'Use separate Tab',
		'note' => 'When editing content use a separate tab to set per site content.',
	),
);
$gBitSmarty->assign( 'multisitesSettings', $multisitesSettings);

$memberLimit = array(
	'0' => tra( 'None' ),
	'10' => 10,
	'20' => 20,
	'30' => 30,
	'50' => 50,
	'100' => 100,
	'9999' => tra( 'Unlimited' ),
);
$gBitSmarty->assign( 'memberLimit', $memberLimit );

if (!empty($_REQUEST['store_preferences'] )) {
       // Store off site wide preferences
	foreach( array_keys( $multisitesSettings ) as $item ) {
		simple_set_toggle( $item, MULTISITES_PKG_NAME );
	}
	simple_set_value( 'multisites_limit_member_number', MULTISITES_PKG_NAME );

	$successMsg = "The preferences were saved";
}

$gBitSmarty->assign( 'successMsg', empty( $successMsg ) ? NULL : $successMsg );
$gBitSmarty->assign( 'warningMsg', empty( $warningMsg ) ? NULL : $warningMsg );
if( !empty( $gMultisites->mErrors ) ) {
	foreach( $gMultisites->mErrors as $error ) {
		$errorMsg[] = $error;
	}
	$gBitSmarty->assign( 'errorMsg', $errorMsg );
}

?>
