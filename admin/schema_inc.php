<?php
// $Header: /cvsroot/bitweaver/_bit_multisites/admin/schema_inc.php,v 1.5 2006/05/06 15:48:09 squareing Exp $
$tables = array(
	'multisites' => "
		multisite_id I4 AUTO PRIMARY,
		server_name C(100) PRIMARY NOTNULL,
		description C(180)
	",
	'multisite_preferences' => "
		multisite_id I4 NOTNULL,
		name C(40) NOTNULL,
		pref_value C(250)
	",
);

global $gBitInstaller;

foreach( array_keys( $tables ) as $tableName ) {
    $gBitInstaller->registerSchemaTable( MULTISITES_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( MULTISITES_PKG_NAME, array(
	'description' => "Multisites allows you to set up bitweaver for Multi-homing.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );
?>
