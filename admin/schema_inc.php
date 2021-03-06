<?php
// $Header$
$tables = array(
	'multisites' => "
		multisite_id I4 AUTO PRIMARY,
		server_name C(100) NOTNULL PRIMARY,
		description C(180)
	",
	'multisite_preferences' => "
		multisite_id I4 NOTNULL PRIMARY,
		name C(40) NOTNULL PRIMARY,
		pref_value C(250)
	",
	'multisite_content' => "
		multisite_id I4 NOTNULL PRIMARY,
		content_id I4 NOTNULL PRIMARY
		CONSTRAINT '
			, CONSTRAINT `multisite_content_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content`( `content_id` )'
	",
	// this doesn't work and i can't figure out why - lsces - you can't link to just half of a primary key
	//, CONSTRAINT `multisite_multisite_ref` FOREIGN KEY (`multisite_id`) REFERENCES `".BIT_DB_PREFIX."multisites`( `multisite_id` )'
);

global $gBitInstaller;

foreach( array_keys( $tables ) as $tableName ) {
    $gBitInstaller->registerSchemaTable( MULTISITES_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( MULTISITES_PKG_NAME, array(
	'description' => "Multisites allows you to set up bitweaver for Multi-homing.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

$gBitInstaller->registerPreferences( MULTISITES_PKG_NAME, array(
		array( MULTISITES_PKG_NAME, 'multisites_per_site_content','y' ),
		array( MULTISITES_PKG_NAME, 'multisites_use_jstab', 'y' ),
		array( MULTISITES_PKG_NAME, 'multisites_limit_member_number','10' ),
		));

$gBitInstaller->registerUserPermissions( MULTISITES_PKG_NAME, array(
	array( 'p_multisites_restrict_content', 'Can restrict content to certain sites', 'editors', MULTISITES_PKG_NAME ),
	array( 'p_multisites_view_restricted', 'Can view all site restricted content', 'admin', MULTISITES_PKG_NAME ),
	));

?>
