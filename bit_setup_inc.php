<?php
// $Header: /cvsroot/bitweaver/_bit_multisites/bit_setup_inc.php,v 1.2 2006/02/17 23:51:44 squareing Exp $

global $gBitSystem, $gMultisites, $gBitLanguage;

$registerHash = array(
	'package_name' => 'multisites',
	'package_path' => dirname( __FILE__ ).'/',
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'multisites' ) ) {
	require_once( MULTISITES_PKG_PATH.'Multisites.php' );
	$gMultisites = new Multisites;

	// override existing settings with the ones set
	if( !empty( $gMultisites->mPrefs ) ) {
		foreach( $gMultisites->mPrefs as $pref => $value ) {
			if( !empty( $value ) ) {
				$override[$pref] = $value;
			}
		}

		$gBitSystem->mPrefs = array_merge( $gBitSystem->mPrefs, $override );
		if( !empty( $gMultisites->mPrefs['bitlanguage'] ) ) {
			$gBitLanguage->setLanguage( $gMultisites->mPrefs['bitlanguage'] );
		}
	}
}
?>
