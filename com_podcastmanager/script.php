<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @since	1.7
 */
class Com_PodcastManagerInstallerScript {

	/**
	 * Function to perform updates when method=upgrade is used
	 *
	 * @param	$parent
	 *
	 * @return	void
	 * @since	1.7
	 */
	function update($parent) {
		echo '<p>1.7 Update - SQL changes</p>';
	}

	/**
	 * Function to perform additional changes post operation
	 *
	 * @param	$type
	 * @param	$parent
	 *
	 * @return	void
	 * @since	1.7
	 * @deprecated	Current function only required for 1.6 to 1.7 update, remove post 1.7.0 Stable Release
	 */
	function postflight($type, $parent) {
		echo '<p>Podcast Manager 1.6 to 1.7 SQL changes</p>';
		$db = JFactory::getDBO();

		$SQLupdate	= file_get_contents(dirname(__FILE__).'/admin/sql/updates/mysql/1.7.0.sql');

		if ($SQLupdate === false) {
			return false;
		}

		// Create an array of queries from the sql file
		jimport('joomla.installer.helper');
		$queries = JInstallerHelper::splitSql($SQLupdate);

		if (count($queries) == 0) {
			continue;
		}

		// Process each query in the $queries array (split out of sql file).
		foreach ($queries as $query) {
			$query = trim($query);
			if ($query != '' && $query{0} != '#') {
				$db->setQuery($query);
				if (!$db->query()) {
					JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
					return false;
				}
			}
		}
	}
}
