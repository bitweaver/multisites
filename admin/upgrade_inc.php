<?php

global $gBitSystem, $gUpgradeFrom, $gUpgradeTo;

$upgrades = array(

	'BWR1' => array(
		'BWR2' => array(
// de-tikify tables
array( 'DATADICT' => array(
	array( 'RENAMETABLE' => array(
		'bit_multisites' => 'multisites',
		'bit_multisite_preferences' => 'multisite_preferences',
	)),
)),

		)
	),
);

if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( MULTISITES_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}
?>
