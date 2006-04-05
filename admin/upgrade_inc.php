<?php

global $gBitSystem, $gUpgradeFrom, $gUpgradeTo;

$upgrades = array(

	'BWR1' => array(
		'BWR2' => array(
// de-tikify tables
array( 'DATADICT' => array(
	array( 'RENAMETABLE' => array(
		'tiki_multisites' => 'multisites',
		'tiki_multisite_preferences' => 'multisite_preferences',
	)),
	array( 'RENAMECOLUMN' => array(
		'multisite_preferences' => array(
			'`value`' => '`pref_value` C(250)'
		),
	)),
)),

		)
	),
);

if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( MULTISITES_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}
?>
