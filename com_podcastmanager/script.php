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
		// Check the currently installed version
		$version	= $this->getVersion();

		// If upgrading from 1.6, run the 1.7 schema updates
		if (substr($version, 0, 3) == '1.6') {
			// Update the tables then create the new feed
			$this->db17Update();
			$this->createFeed();
		}

		// If upgrading from 1.7 Beta releases, update the description field
		if (strpos($version, '1.7 Beta') != false) {
			$db = JFactory::getDBO();
			$query	= 'ALTER TABLE `#__podcastmanager_feeds` CHANGE `description` `description` varchar(255) NOT NULL default '.$db->quote('');
			$db->setQuery($query);
			if (!$db->query()) {
				JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
				return false;
			}
		}
	}

	/**
	 * Function to create a new feed based on the 1.6 parameters when upgrading to 1.7
	 *
	 * @return	void
	 * @since	1.7
	 */
	protected function createFeed() {
		// Get the record from the database
		$db = JFactory::getDBO();
		$query = 'SELECT `params` FROM `#__extensions` WHERE `element` = "com_podcastmanager"';
		$db->setQuery($query);
		$record = $db->loadObject();

		// Decode the JSON
		$params	= json_decode($record->params);

		// Query to create new feed record
		$query	= 'INSERT INTO `#__podcastmanager_feeds` (`id`, `name`, `subtitle`, `description`, `copyright`,'.
				  ' `explicit`, `block`, `ownername`, `owneremail`, `keywords`, `author`, `image`, `category1`,'.
				  ' `category2`, `category3`, `published`) VALUES'.
				  ' (1, '.$db->quote($params->title).', '.$db->quote($params->itSubtitle).', '.$db->quote($params->description).','.
				  $db->quote($params->copyright).', '.$db->quote($params->itExplicit).', '.$db->quote($params->itBlock).','.
				  $db->quote($params->itOwnerName).', '.$db->quote($params->itOwnerEmail).', '.$db->quote($params->itKeywords).','.
				  $db->quote($params->itAuthor).', '.$db->quote($params->itImage).', '.$db->quote($params->itCategory1).','.
				  $db->quote($params->itCategory2).', '.$db->quote($params->itCategory3).', '.$db->quote('1').');';
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
			return false;
		}

		// Set the feed on existing podcasts to this feed
		$query	= 'UPDATE `#__podcastmanager` SET `feedname` = '.$db->quote('1');
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
			return false;
		}
	}

	/**
	 * Function to update the Podcast Manager tables from the 1.6 to 1.7 schema
	 *
	 * @return	void
	 * @since	1.7
	 */
	protected function db17Update() {
		echo '<p>Podcast Manager 1.6 to 1.7 SQL changes</p>';
		$db = JFactory::getDBO();

		// Get the update file
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

	/**
	 * Function to get the currently installed version from the manifest cache
	 *
	 * @return	string	$version	The base version that is installed
	 * @since	1.7
	 */
	protected function getVersion() {
		// Get the record from the database
		$db = JFactory::getDBO();
		$query = 'SELECT `manifest_cache` FROM `#__extensions` WHERE `element` = "com_podcastmanager"';
		$db->setQuery($query);
		$manifest = $db->loadObject();

		// Decode the JSON
		$record	= json_decode($manifest->manifest_cache);

		// Get the version
		$version	= $record->version;

		return $version;
	}
}
