<?php
/**
* Multisites is a package that allows multi-homing for bitweaver
*
* @package  multisites
* @version $Header: /cvsroot/bitweaver/_bit_multisites/Multisites.php,v 1.5 2006/01/31 20:18:41 bitweaver Exp $
* @author   xing <xing@synapse.plus.com>
*/

/**
 * Multisite Management
 *
 * @package  multisites
 */
class Multisites extends BitBase {
	/**
	* id of the currently active domain
	*/
	var $mMultisiteId;

	/**
	* Initialisation of this class
	*/
	function Multisites() {
		BitBase::BitBase();
		$this->load();
	}

	/**
	* Load the currenlty active domain data from the db
	**/
	function load() {
		$query = "SELECT * FROM `".BIT_DB_PREFIX."multisites` WHERE `server_name`=?";
		$result = $this->mDb->query( $query, array( $_SERVER['SERVER_NAME'] ) );
		if( !empty( $result ) ) {
			$res = $result->fetchRow();
			$this->mMultisiteId = $res['multisite_id'];
			$this->mInfo = $res;
		} 
		
		if ( @BitBase::verifyId( $this->mMultisiteId ) ) {
			$query = "SELECT * FROM `".BIT_DB_PREFIX."multisite_preferences` WHERE `multisite_id`=?";
			$result = $this->mDb->query( $query, array( $this->mMultisiteId ) );
			if( !empty( $result ) ) {
				while( $res = $result->fetchRow() ) {
					$this->mPrefs[$res['name']] = $res['value'];
				}
			}
		}
		return( count( $this->mInfo ) );
	}

	/**
	* Get the list of servers and their preferences
	* @param if $pMultisiteId is set, it only gets specified server
	**/
	function getMultisites( $pMultisiteId=NULL ) {
		$where = '';
		$bindvals = array();
		if( @BitBase::verifyId( $pMultisiteId ) ) {
			$where = " WHERE `multisite_id`=?";
			$bindvals[] = $pMultisiteId;
		}

		$query = "SELECT * FROM `".BIT_DB_PREFIX."multisites`".$where;
		$result = $this->mDb->query( $query, $bindvals );
		while( $res = $result->fetchRow() ) {
			$ret[$res['multisite_id']] = $res;
		}

		$query = "SELECT * FROM `".BIT_DB_PREFIX."multisite_preferences`".$where;
		$result = $this->mDb->query( $query, $bindvals );
		while( $res = $result->fetchRow() ) {
			$ret[$res['multisite_id']]['prefs'][$res['name']] = $res['value'];
		}

		return( empty( $ret ) ? NULL : $ret );
	}

	/**
	* Store / Update server data
	*
	* @param array pParams hash of values that will be used to store the page
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	* @access public
	**/
	function store( &$pParamHash ) {
		if( $this->verify($pParamHash ) ) {
			$this->mDb->StartTrans();
			if( @BitBase::verifyId( $pParamHash['multisite_id'] ) ) {
				$msId = array ( "name" => "multisite_id", "value" => $pParamHash['multisite_id'] );
				$result = $this->mDb->associateUpdate( BIT_DB_PREFIX."multisites", $pParamHash['server_store'], $msId );
				$this->expungePreferences( $pParamHash['multisite_id'] );
				foreach( $pParamHash['prefs_store'] as $pref ) {
					$result = $this->mDb->associateInsert( BIT_DB_PREFIX."multisite_preferences", $pref );
				}
			} else {
				$result = $this->mDb->associateInsert( BIT_DB_PREFIX."multisites", $pParamHash['server_store'] );
				$msId = $this->mDb->getOne( "SELECT MAX(`multisite_id`) FROM `".BIT_DB_PREFIX."multisites`" );
				foreach( $pParamHash['prefs_store'] as $pref ) {
					$pref['multisite_id'] = $msId;
					$result = $this->mDb->associateInsert( BIT_DB_PREFIX."multisite_preferences", $pref );
				}
			}
			$this->load();
			$this->mDb->CompleteTrans();
		} else {
			$this->mErrors[] = "There was a problem trying to save the settings.";
		}
		return( count( $this->mErrors ) == 0 );
	}

	/**
	* Make sure the data is safe to store
	*
	* @param array pParams reference to hash of values that will be used to store the page, they will be modified where necessary
	* @return bool TRUE on success, FALSE if verify failed. If FALSE, $this->mErrors will have reason why
	* @access private
	**/
	function verify( &$pParamHash ) {
		if( @BitBase::verifyId( $pParamHash['multisite_id'] ) ) {
			$pParamHash['server_store']['multisite_id'] = $pParamHash['multisite_id'];
		}

		if( !empty( $pParamHash['server_name'] ) ) {
			$query = "SELECT * FROM `".BIT_DB_PREFIX."multisites` WHERE `server_name`=?";
			$result = $this->mDb->query( $query, array( trim( $pParamHash['server_name'] ) ) );
			$site = $result->fetchRow();
			if( empty( $site ) || ( $site['multisite_id'] == $pParamHash['multisite_id'] ) ) {
				$pParamHash['server_store']['server_name'] = trim( $pParamHash['server_name'] );
			} else {
				$this->mErrors[] = 'There already is a server in the database with this value.';
			}
		} else {
			$this->mErrors[] = "A Server Name is required to save this setting.";
		}

		if( !empty( $pParamHash['server_prefs'] ) ) {
			foreach( $pParamHash['server_prefs'] as $pref => $value ) {
				$pParamHash['prefs_store'][] = array(
					'multisite_id' => $pParamHash['multisite_id'],
					'name' => $pref,
					'value' => $value,
				);
			}
		} else {
			$pParamHash['prefs_store'] = NULL;
		}

		if( !empty( $pParamHash['description'] ) ) {
			$pParamHash['server_store']['description'] = $pParamHash['description'];
		} else {
			$pParamHash['server_store']['description'] = NULL;
		}
		return( count( $this->mErrors ) == 0 );
	}

	/**
	* remove server from db
	* 
	* @param $pMultisiteId is the id of the server we need to delete
	* @access public
	**/
	function expunge( $pMultisiteId=NULL ) {
		$ret = FALSE;
		if( @BitBase::verifyId( $pMultisiteId ) ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."multisites` WHERE `multisite_id` = ?";
			$ret = $this->mDb->query( $query, array( $pMultisiteId ) );
			$this->expungePreferences( $pMultisiteId );
		}
		return $ret;
	}

	/**
	* remove all preferences associated with a given server
	* 
	* @param $pMultisiteId is the id of the server we need to delete
	* @access private
	**/
	function expungePreferences( $pMultisiteId=NULL ) {
		$ret = FALSE;
		if( @BitBase::verifyId( $pMultisiteId ) ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."multisite_preferences` WHERE `multisite_id` = ?";
			$ret = $this->mDb->query( $query, array( $pMultisiteId ) );
		}
		return $ret;
	}
}
?>
