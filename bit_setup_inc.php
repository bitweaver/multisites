<?php
// $Header: /cvsroot/bitweaver/_bit_multisites/bit_setup_inc.php,v 1.4 2006/07/02 17:05:31 nickpalmer Exp $

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

		$gBitSystem->mConfig = array_merge( $gBitSystem->mConfig, $override );
		if( !empty( $gMultisites->mPrefs['bitlanguage'] ) ) {
			$gBitLanguage->setLanguage( $gMultisites->mPrefs['bitlanguage'] );
		}
	}
}
?>
