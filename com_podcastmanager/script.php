<?php
/**
* Podcast Manager for Joomla!
*
* @package     PodcastManager
* @subpackage  com_podcastmanager
*
* @copyright   Copyright (C) 2011 Michael Babker. All rights reserved.
* @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.7
 */
class Com_PodcastManagerInstallerScript
{
	/**
	 * Function to act prior to installation process begins
	 *
	 * @param   string  $type    The action being performed
	 * @param   string  $parent  The function calling this method
	 *
	 * @return  void
	 *
	 * @since   1.7
	 */
	function preflight($type, $parent)
	{
		// Requires Joomla! 1.7
		$jversion = new JVersion();
		if (version_compare($jversion->getShortVersion(), '1.7', 'lt'))
		{
			JError::raiseNotice(null, JText::_('COM_PODCASTMANAGER_ERROR_INSTALL_J17'));
			return false;
		}

		// Bugfix for "Can not build admin menus"
		if (in_array($type, array('install', 'discover_install')))
		{
			$this->_bugfixDBFunctionReturnedNoError();
		}
		else
		{
			$this->_bugfixCantBuildAdminMenus();
		}
	}

	/**
	 * Function to perform changes during uninstall
	 *
	 * @param   string  $parent  The function calling this method
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	function uninstall($parent)
	{
		// Build a menu record for the media component to prevent the "cannot delete admin menu" error
		// Get the component's ID from the database
		$option	= 'com_podcastmedia';
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where('element = '.$db->quote($option));
		$db->setQuery($query);
		$component_id = $db->loadResult();

		// Add the record
		$table	= JTable::getInstance('menu');

		$data = array();
		$data['menutype'] = 'main';
		$data['client_id'] = 1;
		$data['title'] = $option;
		$data['alias'] = $option;
		$data['link'] = 'index.php?option='.$option;
		$data['type'] = 'component';
		$data['published'] = 0;
		$data['parent_id'] = 1;
		$data['component_id'] = $component_id;
		$data['img'] = 'class:component';
		$data['home'] = 0;

		// All the table processing without error checks since we're hacking to prevent an error message
		if (!$table->setLocation(1, 'last-child') || !$table->bind($data) || !$table->check() || !$table->store())
		{
			continue;
		}
	}

	/**
	 * Function to perform updates when method=upgrade is used
	 *
	 * @param       string  $parent  The function calling this method
	 *
	 * @return      void
	 *
	 * @since       1.7
	 * @deprecated  2.0  Update method unnecessary upon removal of legacy upgrade
	 */
	function update($parent)
	{
		JLog::add('com_podcastmanager update method is deprecated.', JLog::WARNING, 'deprecated');
		// Check the currently installed version
		$version	= $this->getVersion();
		if ($version == 'Error')
		{
			JError::raiseNotice(null, JText::_('COM_PODCASTMANAGER_ERROR_INSTALL_UPDATE'));
			return;
		}

		// If upgrading from 1.6, run the 1.7/1.8 schema updates
		if (substr($version, 0, 3) == '1.6')
		{
			// Update the tables then create the new feed
			$this->db17Update();
			$this->createFeed();
		}

		// If upgrading from 1.7 Beta releases, update the description field
		if (strpos($version, '1.7 Beta') != false)
		{
			$db = JFactory::getDBO();
			$query	= 'ALTER TABLE '.$db->quoteName('#__podcastmanager_feeds')
					. ' CHANGE '.$db->quoteName('description')
					. $db->quoteName('description').' varchar(5120) NOT NULL default '.$db->quote('');
			$db->setQuery($query);
			if (!$db->query())
			{
				JError::raiseWarning(null, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
			}
		}
	}

	/**
	 * Function to create a new feed based on the 1.6 parameters when upgrading to 1.7
	 *
	 * @return      void
	 *
	 * @since       1.7
	 * @deprecated  2.0
	 */
	protected function createFeed()
	{
		// Get the record from the database
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select($db->quoteName('params'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element').' = '.$db->quote('com_podcastmanager'));
		$db->setQuery($query);
		if (!$db->loadObject())
		{
			JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
			return;
		}
		else
		{
			$record = $db->loadObject();
		}

		// Decode the JSON
		$params	= json_decode($record->params);

		// Query to create new feed record
		$addFeed	= 'INSERT INTO `#__podcastmanager_feeds` (`id`, `name`, `subtitle`, `description`, `copyright`,'
					. ' `explicit`, `block`, `ownername`, `owneremail`, `keywords`, `author`, `image`, `category1`,'
					. ' `category2`, `category3`, `published`) VALUES'
					. ' (1, '.$db->quote($params->title).', '.$db->quote($params->itSubtitle).', '.$db->quote($params->description).','
					. $db->quote($params->copyright).', '.$db->quote($params->itExplicit).', '.$db->quote($params->itBlock).','
					. $db->quote($params->itOwnerName).', '.$db->quote($params->itOwnerEmail).', '.$db->quote($params->itKeywords).','
					. $db->quote($params->itAuthor).', '.$db->quote($params->itImage).', '.$db->quote($params->itCategory1).','
					. $db->quote($params->itCategory2).', '.$db->quote($params->itCategory3).', '.$db->quote('1').');';
		$db->setQuery($addFeed);
		if (!$db->query())
		{
			JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
		}

		// Set the feed on existing podcasts to this feed
		$feed	= $db->getQuery(true);
		$query->update($db->quoteName('#__podcastmanager'));
		$query->set($db->quoteName('feedname').' = '.$db->quote('1'));
		$db->setQuery($feed);
		if (!$db->query())
		{
			JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
		}
	}

	/**
	 * Function to update the Podcast Manager tables from the 1.6 to 1.7 schema
	 *
	 * @return      void
	 *
	 * @since       1.7
	 * @deprecated  2.0
	 */
	protected function db17Update()
	{
		$db = JFactory::getDBO();

		// Get the update file
		$SQLupdate	= file_get_contents(dirname(__FILE__).'/admin/sql/updates/mysql/1.7.0.sql');
		$SQLupdate	.= file_get_contents(dirname(__FILE__).'/admin/sql/updates/mysql/1.7.1.sql');
		$SQLupdate	.= file_get_contents(dirname(__FILE__).'/admin/sql/updates/mysql/1.8.1.sql');

		if ($SQLupdate === false)
		{
			return;
		}

		// Create an array of queries from the sql file
		jimport('joomla.installer.helper');
		$queries = JInstallerHelper::splitSql($SQLupdate);

		if (count($queries) == 0)
		{
			continue;
		}

		// Process each query in the $queries array (split out of sql file).
		foreach ($queries as $query)
		{
			$query = trim($query);
			if ($query != '' && $query{0} != '#')
			{
				$db->setQuery($query);
				if (!$db->query())
				{
					JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
					return;
				}
			}
		}
	}

	/**
	 * Function to get the currently installed version from the manifest cache
	 *
	 * @return  string  $version  The version that is installed
	 *
	 * @since   1.7
	 */
	protected function getVersion()
	{
		// Get the record from the database
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element').' = '.$db->quote('com_podcastmanager'));
		$db->setQuery($query);
		if (!$db->loadObject())
		{
			JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
			$version = 'Error';
			return $version;
		}
		else
		{
			$manifest = $db->loadObject();
		}

		// Decode the JSON
		$record	= json_decode($manifest->manifest_cache);

		// Get the version
		$version	= $record->version;

		return $version;
	}

	/**
	 * Joomla! 1.6+ bugfix for "DB function returned no error"
	 *
	 * @author	Nicholas K. Dionysopoulos (https://www.akeebabackup.com)
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	private function _bugfixDBFunctionReturnedNoError()
	{
		$db = JFactory::getDbo();

		// Fix broken #__assets records
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__assets');
		$query->where($db->quoteName('name').' = '.$db->quote('com_podcastmanager'));
		$db->setQuery($query);
		$ids = $db->loadResultArray();
		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				$query->clear();
				$query->delete('#__assets');
				$query->where($db->quoteName('id').' = '.$db->quote($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// Fix broken #__extensions records
		$query->clear();
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where($db->quoteName('element').' = '.$db->quote('com_podcastmanager'));
		$db->setQuery($query);
		$ids = $db->loadResultArray();
		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				$query->clear();
				$query->delete('#__extensions');
				$query->where($db->quoteName('extension_id').' = '.$db->quote($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// Fix broken #__menu records
		$query->clear();
		$query->select($db->quoteName('id'));
		$query->from('#__menu');
		$query->where($db->quoteName('type').' = '.$db->quote('component'));
		$query->where($db->quoteName('menutype').' = '.$db->quote('main'));
		$query->where($db->quoteName('link').' LIKE '.$db->quote('index.php?option=com_podcastmanager%'));
		$db->setQuery($query);
		$ids = $db->loadResultArray();
		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				$query->clear();
				$query->delete('#__menu');
				$query->where($db->quoteName('id').' = '.$db->quote($id));
				$db->setQuery($query);
				$db->query();
			}
		}
	}

	/**
	 * Joomla! 1.6+ bugfix for "Can not build admin menus"
	 *
	 * @author	Nicholas K. Dionysopoulos (https://www.akeebabackup.com)
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	private function _bugfixCantBuildAdminMenus()
	{
		$db = JFactory::getDbo();

		// If there are multiple #__extensions record, keep one of them
		$query = $db->getQuery(true);
		$query->select($db->quoteName('extension_id'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element').' = '.$db->quote('com_podcastmanager'));
		$db->setQuery($query);
		$ids = $db->loadResultArray();
		if (count($ids) > 1)
		{
			asort($ids);
			$extension_id = array_shift($ids); // Keep the oldest id

			foreach ($ids as $id)
			{
				$query->clear();
				$query->delete($db->quoteName('#__extensions'));
				$query->where($db->quoteName('extension_id').' = '.$db->quote($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// @todo

		// If there are multiple assets records, delete all except the oldest one
		$query->clear();
		$query->select('id');
		$query->from($db->quoteName('#__assets'));
		$query->where($db->quoteName('name').' = '.$db->quote('com_podcastmanager'));
		$db->setQuery($query);
		$ids = $db->loadObjectList();
		if (count($ids) > 1)
		{
			asort($ids);
			$asset_id = array_shift($ids); // Keep the oldest id

			foreach ($ids as $id)
			{
				$query->clear();
				$query->delete($db->quoteName('#__assets'));
				$query->where($db->quoteName('id').' = '.$db->quote($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// Remove #__menu records for good measure!
		$query->clear();
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__menu'));
		$query->where($db->quoteName('type').' = '.$db->quote('component'));
		$query->where($db->quoteName('menutype').' = '.$db->quote('main'));
		$query->where($db->quoteName('link').' LIKE '.$db->quote('index.php?option=com_podcastmanager%'));
		$db->setQuery($query);
		$ids = $db->loadResultArray();
		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				$query->clear();
				$query->delete($db->quoteName('#__menu'));
				$query->where($db->quoteName('id').' = '.$db->quote($id));
				$db->setQuery($query);
				$db->query();
			}
		}
	}
}
