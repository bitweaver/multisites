<?php
/**
* Multisites is a package that allows multi-homing for bitweaver and restriction of content to certain sites
*
* @package  multisites
* @version $Header: /cvsroot/bitweaver/_bit_multisites/Multisites.php,v 1.17 2007/12/04 14:49:07 joasch Exp $
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
	var $mPrefs;

	/**
	* Initialisation of this class
	*/
	function Multisites() {
		BitBase::BitBase();
		$this->load();
	}

	/**
	* Load the currently active domain data from the db
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
					$this->mPrefs[$res['name']] = $res['pref_value'];
				}
			}
		}
		return( count( $this->mInfo ) );
	}

	/**
	* Get the list of servers and their preferences
	* @param if $pMultisiteId is set, it only gets specified server
	* @param if $pContentId is set, it selects multisites selected for the specfied content
	**/
	function getMultisites( $pMultisiteId=NULL , $pContentId=NULL ) {
		$where = '';
		$join = '';
		$bindvals = array();
		if( @BitBase::verifyId( $pMultisiteId ) ) {
			$where = " WHERE ms.`multisite_id`=?";
			$bindvals[] = $pMultisiteId;
		}

		$select= "SELECT * FROM `".BIT_DB_PREFIX."multisites` ms ";
		$query = $select.$where;
		$result = $this->mDb->query( $query, $bindvals );
		while( $res = $result->fetchRow() ) {
			$ret[$res['multisite_id']] = $res;
		}

		$join = " LEFT JOIN `".BIT_DB_PREFIX."multisite_preferences` mp ON (ms.multisite_id=mp.multisite_id) ".$join;
		$query = $select.$join.$where;
		$result = $this->mDb->query( $query, $bindvals );
		while( $res = $result->fetchRow() ) {
			$ret[$res['multisite_id']]['prefs'][$res['name']] = $res['pref_value'];
		}

		if( !empty( $pContentId ) ) {
			$join = " LEFT JOIN `".BIT_DB_PREFIX."multisite_content` mc ON (ms.multisite_id=mc.multisite_id)";
			if( @BitBase::verifyId( $pContentId ) ) {
				if ($where != '') {
					$where = $where." AND mc.content_id=?";
				}
				else {
					$where = " WHERE mc.content_id=?";
				}
				$bindvals[] = $pContentId;
			}

			$query = $select.$join.$where;
			$result = $this->mDb->query( $query, $bindvals );
			while( $res = $result->fetchRow() ) {
				if ( !empty($res['content_id']) ) {
					$ret[$res['multisite_id']][0]['selected'] = TRUE;
				}
			}
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
				$result = $this->mDb->associateUpdate( BIT_DB_PREFIX."multisites", $pParamHash['server_store'], array( "multisite_id" => $pParamHash['multisite_id'] ) );
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
					'pref_value' => $value,
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
			$this->expungeRestrictions($pMultisiteId);
			$this->expungePreferences( $pMultisiteId );
			$query = "DELETE FROM `".BIT_DB_PREFIX."multisites` WHERE `multisite_id` = ?";
			$ret = $this->mDb->query( $query, array( $pMultisiteId ) );
		}
		return $ret;
	}

	/**
	 * remove restrictions by multisite_id from db
	 *
	 * @param $pMultisiteId
	 * @access public
	 **/
	function expungeRestrictions( $pMultisiteId, $pContentId = NULL ) {
		$ret = FALSE;
		if ( !empty($pMultisiteId) && @BitBase::verifyId( $pMultisiteId ) ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."multisite_content` WHERE multisite_id = ?";
			$ret = $this->mDb->query( $query, array( $pMultisiteId ) );
		}
		if ( !empty($pContentId) && @BitBase::verifyId( $pContentId ) ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."multisite_content` WHERE content_id =?";
			$ret = $this->mDb->query( $query, array( $pContentId ) );
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

	/**
	 * Store content restriction
	 * @param $pParamHash an array of restrictions to be stored.
	 * @param $pParamHash[multisite_id] The id of the site to restrict to
	 * @param $pParamHash[content_id] The id of the content to restrict
	 * @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	 * @access public
	 **/
	function insertRestriction( &$pParamHash ) {
		if( $this->verifyRestrictions($pParamHash) ) {
			$this->mDb->StartTrans();
			foreach( $pParamHash['member_store'] as $item ) {
				$result = $this->mDb->associateInsert( BIT_DB_PREFIX."multisite_content", $item );
			}
			$this->mDb->CompleteTrans();
		} else {
			error_log( "Error inserting multisite restriction: " . vc($this->mErrors) );
		}
		return( count( $this->mErrors ) == 0 );
	}

	/**
	* verify, clean up and prepare data to be stored
	* @param $pParamHash all information that is being stored. will update $pParamHash by reference with fixed array of items
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	* @access private
	**/
	function verifyRestrictions( &$pParamHash ) {
		foreach( $pParamHash as $key => $item ) {
			if( isset( $item['multisite_id'] ) && @BitBase::verifyId( $item['multisite_id'] ) ) {
				$tmp['member_store'][$key]['multisite_id'] = $item['multisite_id'];
			} else {
				$this->mErrors['store_members'] = tra( 'The multisite_id is missing.' );
			}

			if( isset( $item['content_id'] ) && @BitBase::verifyId( $item['content_id'] ) ) {
				$tmp['member_store'][$key]['content_id'] = $item['content_id'];
			} else {
				$this->mErrors['store_members'] = 'The content_id is not valid.';
			}
		}

		$pParamHash = $tmp;
		return( count( $this->mErrors ) == 0 );
	}
}

// ============= SERVICE FUNCTIONS =============

function multisites_content_display( &$pObject ) {
	// TODO: Add a feature to display sites the content is shown on to permissioned users
}

function multisites_content_edit( $pObject=NULL ) {
	global $gBitSmarty, $gBitUser, $gBitSystem;
	$multisitesList = array();

	if( $gBitSystem->isFeatureActive('multisites_per_site_content') && $gBitUser->hasPermission( 'p_multisites_restrict_content' )) {
		$multisites = new Multisites();
		if ($multisitesList = $multisites->getMultisites( NULL, !empty( $pObject->mContentId ) ? $pObject->mContentId : NULL )) {
			$gBitSmarty->assign( 'multisitesList', $multisitesList);
		}
	}
}

function multisites_content_expunge( $pObject=NULL ) {
	$multisites = new Multisites();
	$multisites->expungeRestrictions(NULL, $pObject->mContentId);
}

function multisites_content_preview() {
	global $gBitSmarty, $gBitUser, $gBitSystem;

	if( $gBitSystem->isFeatureActive('multisites_per_site_content') && $gBitUser->hasPermission( 'p_multisites_restrict_content' )) {
		$multisites = new Multisites();

		if ($multisitesList = $multisites->getMultisites()) {
			foreach( $multisitesList as $key => $site ) {
				if (!empty( $_REQUEST['multisites']['multisite'] ) && in_array( $key, $_REQUEST['multisites']['multisite'] ) ) {
					$multisitesList[$key][0]['selected'] = TRUE;
				} else {
					$multisitesList[$key][0]['selected'] = FALSE;
				}
			}
			$gBitSmarty->assign( 'multisitesList', $multisitesList );
		}
	}
}

function multisites_content_store( $pObject, $pParamHash ) {
	global $gBitSmarty, $gBitUser, $gBitSystem;

	if( $gBitSystem->isFeatureActive('multisites_per_site_content') && $gBitUser->hasPermission( 'p_multisites_restrict_content' )) {
		if( is_object( $pObject  ) && empty( $pParamHash['content_id'] ) ) {
			$pParamHash['content_id'] = $pObject->mContentId;
		}

		if( !empty( $pParamHash['content_id'] ) ) {
			$multisites = new Multisites();
			$multisitesList = $multisites->getMultisites( NULL, $pParamHash['content_id'] );

			// Now we need to work out if we need to save at all
			$selectedItem = array();
			if( !empty( $multisitesList )) {
				foreach( $multisitesList as $site ) {
					if( !empty( $site[0]['selected'] )) {
						$selectedItem[] = $site['multisite_id'];
					}
				}
			}

			// Quick and Dirty check to start of with
			if( empty( $_REQUEST['multisites'] ) || count( $_REQUEST['multisites']['multisite']) != count( $selectedItem ) ) {
				$modified = TRUE;
			} else {
				// more thorough check
				foreach( $selectedItem as $item ) {
					if( !in_array( $item, $_REQUEST['multisites']['multisite'] ) ) {
						$modified = TRUE;
					}
				}
			}

			if( !empty( $modified ) ) {
				// first remove all entries with this content_id
				if ($multisites->expungeRestrictions( NULL, $pParamHash['content_id'] ) && !empty( $_REQUEST['multisites'] ) ) {
					// insert the content restrictions
					foreach( $_REQUEST['multisites']['multisite'] as $m_id ) {
						$siteHash[] = array(
								    'multisite_id' => $m_id,
								    'content_id' => $pParamHash['content_id']
								    );
					}
					if( !$multisites->insertRestriction( $siteHash ) ) {
						$gBitSmarty->assign( 'msg', tra( "There was a problem setting the site restriction." ) );
						$gBitSmarty->display( 'error.tpl' );
						die;
					}
				}
			}
		}
	}
}

// Limits the list of content to those with an entry that matches the server
// name or an entry that has no multistes restrictions if the user can't view everything.
function multisites_content_sql ( &$pObject, $pParamHash = '') {
	global $gBitSystem, $gBitUser;
	$ret = array();
	// We only limit content if the user has activated this feature and they are not an administrator
	if( $gBitSystem->isFeatureActive('multisites_per_site_content') && !$gBitUser->hasPermission('p_multisites_view_restricted') ) {
		$ret['join_sql'] = " LEFT OUTER JOIN `".BIT_DB_PREFIX."multisite_content` mc ON (lc.`content_id` = mc.`content_id`) LEFT JOIN `".BIT_DB_PREFIX."multisites` ms ON (mc.`multisite_id` = ms.`multisite_id`) ";
		$ret['where_sql'] = " AND ( ms.`server_name` IS NULL OR ms.`server_name` =? ) ";
		$ret['bind_vars'][] = $_SERVER['SERVER_NAME'];
	}
	return $ret;
}
?>
