<?php
// $Header: /cvsroot/bitweaver/_bit_multisites/bit_setup_inc.php,v 1.1 2005/06/24 19:56:29 bitweaver Exp $

global $gBitSystem, $gMultisites, $gBitLanguage;
$gBitSystem->registerPackage( 'multisites', dirname( __FILE__ ).'/' );

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
