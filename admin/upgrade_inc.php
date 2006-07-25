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
// TODO: Test this upgrade stuff
// add the content restriction stuff
	array( 'CREATE' => array (
	'multisite_content' => "
		multisite_id I4 NOTNULL PRIMARY,
		content_id I4 NOTNULL PRIMARY
		CONSTRAINT '
			, CONSTRAINT `multisite_content_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content`( `content_id` )
			, CONSTRAINT `multisite_multisite_ref` FOREIGN KEY (`multisite_id`) REFERENCES `".BIT_DB_PREFIX."multisites`( `multisite_id` )'
	")),
	)),
array( 'PHP' => "
global \$gBitInstaller;
\$gBitInstaller->registerPreferences( MULTISITES_PKG_NAME, array(
		array( MULTISITES_PKG_NAME, 'multisites_per_site_content','y' ),
		array( MULTISITES_PKG_NAME, 'multisites_use_jstab', 'y' ),
		array( MULTISITES_PKG_NAME, 'multisites_limit_member_number','10' ),
		));

\$gBitInstaller->registerUserPermissions( MULTISITES_PKG_NAME, array(
	array( 'p_multisites_restrict_content', 'Can restrict content to certain sites', 'editor', MULTISITES_PKG_NAME ),
	array( 'p_multisites_view_restricted', 'Can view all site restricted content', 'admin', MULTISITES_PKG_NAME ),
	));
"),
), // BWR1 => BWR2
), // BWR1
);

if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( MULTISITES_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}
?>
