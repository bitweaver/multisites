<?php
// $Header: /cvsroot/bitweaver/_bit_multisites/admin/schema_inc.php,v 1.1.1.1.2.1 2006/02/05 21:19:21 squareing Exp $
$tables = array(
	'tiki_multisites' => "
		multisite_id I4 AUTO PRIMARY,
		server_name C(100) PRIMARY NOTNULL,
		description C(180)
	",
	'tiki_multisite_preferences' => "
		multisite_id I4 NOTNULL,
		name C(40) NOTNULL,
		value C(250)
	",
);

global $gBitInstaller;

foreach( array_keys( $tables ) as $tableName ) {
    $gBitInstaller->registerSchemaTable( MULTISITES_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo(MULTISITES_PKG_NAME, array(
	'description' => "Multisites allows you to set up bitweaver for Multi-homing.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );
?>
