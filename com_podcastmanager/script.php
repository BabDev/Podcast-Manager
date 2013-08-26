<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
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
	 * @param   string               $type    The action being performed
	 * @param   JInstallerComponent  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.7
	 */
	public function preflight($type, $parent)
	{
		// Requires PHP 5.3 (need this check because of earlier version use)
		if (version_compare(PHP_VERSION, '5.3', 'lt'))
		{
			JError::raiseNotice(null, JText::_('COM_PODCASTMANAGER_ERROR_INSTALL_PHPVERSION'));

			return false;
		}

		// Requires Joomla! 2.5.6 (need this check because of earlier version use)
		if (version_compare(JVERSION, '2.5.6', 'lt'))
		{
			JError::raiseNotice(null, JText::_('COM_PODCASTMANAGER_ERROR_INSTALL_JVERSION'));

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

		return true;
	}

	/**
	 * Function to perform changes during update
	 *
	 * @param   JInstallerComponent  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	public function update($parent)
	{
		// Get the pre-update version
		$version = $this->_getVersion();

		// If in error, throw a message about the language files
		if ($version == 'Error')
		{
			JError::raiseNotice(null, JText::_('COM_PODCASTMANAGER_ERROR_INSTALL_UPDATE'));

			return;
		}

		// If coming from 1.x, remove language files in administrator/language and language
		if (version_compare($version, '2.0', 'lt'))
		{
			$this->_removeLanguageFiles();
		}

		// If coming from versions earlier than 2.1, remove the component media folders
		if (version_compare($version, '2.1', 'lt'))
		{
			$this->_removeMediaFolders();
		}
	}

	/**
	 * Function to perform changes during uninstall
	 *
	 * @param   JInstallerComponent  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	public function uninstall($parent)
	{
		// Build a menu record for the media component to prevent the "cannot delete admin menu" error
		// Get the component's ID from the database
		$option = 'com_podcastmedia';
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('extension_id'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . ' = ' . $db->quote($option));
		$db->setQuery($query);
		$component_id = $db->loadResult();

		// Add the record
		$table = JTable::getInstance('menu');

		$data = array();
		$data['menutype'] = 'main';
		$data['client_id'] = 1;
		$data['title'] = $option;
		$data['alias'] = $option;
		$data['link'] = 'index.php?option=' . $option;
		$data['type'] = 'component';
		$data['published'] = 0;
		$data['parent_id'] = 1;
		$data['component_id'] = $component_id;
		$data['img'] = 'class:component';
		$data['home'] = 0;

		// All the table processing without error checks since we're hacking to prevent an error message
		if (!$table->setLocation(1, 'last-child') || !$table->bind($data) || !$table->check() || !$table->store())
		{
			// Do nothing ;-)
		}

		// Deal with Tags support in 3.1+
		if (version_compare(JVERSION, '3.1', 'ge'))
		{
			// Remove the data in the core content tables
			$db = JFactory::getDbo();

			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE ' . $db->quote('com_podcastmanager.%'));
			$db->setQuery($query);
			$db->execute();

			$query->clear();
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($db->quoteName('type_alias') . ' LIKE ' . $db->quote('com_podcastmanager.%'));
			$db->setQuery($query);
			$db->execute();

			$query->clear();
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($db->quoteName('core_type_alias') . ' LIKE ' . $db->quote('com_podcastmanager.%'));
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Function to perform changes when component is initially installed
	 *
	 * @param   string               $type    The action being performed
	 * @param   JInstallerComponent  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   2.1
	 */
	public function postflight($type, $parent)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		if ($type == 'install')
		{
			if (!is_dir(JPATH_ROOT . '/media/com_podcastmanager'))
			{
				JFolder::create(JPATH_ROOT . '/media/com_podcastmanager');
				JFile::copy(JPATH_ROOT . '/media/index.html', JPATH_ROOT . '/media/com_podcastmanager/index.html');
			}
		}

		// Deal with Tags support in 3.1+
		if (version_compare(JVERSION, '3.1', 'ge'))
		{
			// Insert the columns in the #__content_types table if they don't exist already
			$db = JFactory::getDbo();

			// Get the type ID for a Podcast Manager feed
			$query = $db->getQuery(true);
			$query->select($db->quoteName('type_id'));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_podcastmanager.feed'));
			$db->setQuery($query);
			$feedTypeId = $db->loadResult();

			// Get the type ID for a Podcast Manager podcast
			$query->clear('where');
			$query->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_podcastmanager.podcast'));
			$db->setQuery($query);
			$podcastTypeId = $db->loadResult();

			// If we don't have the feed type ID, assume the type data doesn't exist yet
			if (!$feedTypeId)
			{
				// This object contains all fields that are mapped to the core_content table
				$commonObject = new stdClass;
				$commonObject->core_title = 'name';
				$commonObject->core_alias = 'alias';
				$commonObject->core_body = 'description';
				$commonObject->core_state = 'published';
				$commonObject->core_checked_out_time = 'checked_out_time';
				$commonObject->core_checked_out_user_id = 'checked_out';
				$commonObject->core_created_user_id = 'created_by';
				$commonObject->core_created_by_alias = 'author';
				$commonObject->core_created_time = 'created';
				$commonObject->core_modified_user_id = 'modified_by';
				$commonObject->core_modified_time = 'modified';
				$commonObject->core_language = 'language';
				$commonObject->core_content_item_id = 'id';
				$commonObject->asset_id = 'asset_id';

				// This object contains unique fields
				$specialObject = new stdClass;
				$specialObject->subtitle = 'subtitle';
				$specialObject->boilerplate = 'boilerplate';
				$specialObject->bp_position = 'bp_position';
				$specialObject->copyright = 'copyright';
				$specialObject->explicit = 'explicit';
				$specialObject->block = 'block';
				$specialObject->ownername = 'ownername';
				$specialObject->owneremail = 'owneremail';
				$specialObject->keywords = 'keywords';
				$specialObject->newFeed = 'newFeed';
				$specialObject->image = 'image';
				$specialObject->category1 = 'category1';
				$specialObject->category2 = 'category2';
				$specialObject->category3 = 'category3';

				// Prepare the object
				$fieldMappings = array(
					'common' => array(
						$commonObject
					),
					'special' => array(
						$specialObject
					)
				);

				// Set the table columns to insert table to
				$columnsArray = array(
					$db->quoteName('type_title'), $db->quoteName('type_alias'), $db->quoteName('table'),
					$db->quoteName('rules'), $db->quoteName('field_mappings'), $db->quoteName('router')
				);

				// Insert the link.
				$query->clear();
				$query->insert($db->quoteName('#__content_types'));
				$query->columns($columnsArray);
				$query->values(
					$db->quote('Podcast Manager Feed') . ', '
					. $db->quote('com_podcastmanager.feed') . ', '
					. $db->quote('{"special":{"dbtable":"#__podcastmanager_feeds","key":"id","type":"Feed","prefix":"PodcastManagerTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}') . ', '
					. $db->quote('') . ', '
					. $db->quote(json_encode($fieldMappings)) . ', '
					. $db->quote('PodcastManagerHelperRoute::getFeedHtmlRoute')
				);
				$db->setQuery($query);
				$db->execute();
			}

			// If we don't have the podcast type ID, assume the type data doesn't exist yet
			if (!$podcastTypeId)
			{
				// This object contains all fields that are mapped to the core_content table
				$commonObject = new stdClass;
				$commonObject->core_title = 'title';
				$commonObject->core_alias = 'alias';
				$commonObject->core_body = 'itSummary';
				$commonObject->core_state = 'published';
				$commonObject->core_checked_out_time = 'checked_out_time';
				$commonObject->core_checked_out_user_id = 'checked_out';
				$commonObject->core_created_user_id = 'created_by';
				$commonObject->core_created_by_alias = 'itAuthor';
				$commonObject->core_created_time = 'created';
				$commonObject->core_modified_user_id = 'modified_by';
				$commonObject->core_modified_time = 'modified';
				$commonObject->core_language = 'language';
				$commonObject->core_publish_up = 'publish_up';
				$commonObject->core_content_item_id = 'id';
				$commonObject->asset_id = 'asset_id';

				// This object contains unique fields
				$specialObject = new stdClass;
				$specialObject->filename = 'filename';
				$specialObject->feedname = 'feedname';
				$specialObject->itBlock = 'itBlock';
				$specialObject->itDuration = 'itDuration';
				$specialObject->itExplicit = 'itExplicit';
				$specialObject->itImage = 'itImage';
				$specialObject->itKeywords = 'itKeywords';
				$specialObject->itSubtitle = 'itSubtitle';
				$specialObject->mime = 'mime';

				// Prepare the object
				$fieldMappings = array(
					'common' => array(
						$commonObject
					),
					'special' => array(
						$specialObject
					)
				);

				// Set the table columns to insert table to
				$columnsArray = array(
					$db->quoteName('type_title'), $db->quoteName('type_alias'), $db->quoteName('table'),
					$db->quoteName('rules'), $db->quoteName('field_mappings'), $db->quoteName('router')
				);

				// Insert the link.
				$query->clear();
				$query->insert($db->quoteName('#__content_types'));
				$query->columns($columnsArray);
				$query->values(
					$db->quote('Podcast Manager Podcast') . ', '
					. $db->quote('com_podcastmanager.podcast') . ', '
					. $db->quote('{"special":{"dbtable":"#__podcastmanager","key":"id","type":"Podcast","prefix":"PodcastManagerTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}') . ', '
					. $db->quote('') . ', '
					. $db->quote(json_encode($fieldMappings)) . ', '
					. $db->quote('PodcastManagerHelperRoute::getPodcastRoute')
				);
				$db->setQuery($query);
				$db->execute();
			}
		}
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
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__assets'));
		$query->where($db->quoteName('name') . ' = ' . $db->quote('com_podcastmanager'));
		$db->setQuery($query);
		$ids = $db->loadColumn();

		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				$query->clear();
				$query->delete($db->quoteName('#__assets'));
				$query->where($db->quoteName('id') . ' = ' . $db->quote($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// Fix broken #__extensions records
		$query->clear();
		$query->select($db->quoteName('extension_id'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . ' = ' . $db->quote('com_podcastmanager'));
		$db->setQuery($query);
		$ids = $db->loadColumn();

		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				$query->clear();
				$query->delete($db->quoteName('#__extensions'));
				$query->where($db->quoteName('extension_id') . ' = ' . $db->quote($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// Fix broken #__menu records
		$query->clear();
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__menu'));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
		$query->where($db->quoteName('menutype') . ' = ' . $db->quote('main'));
		$query->where($db->quoteName('link') . ' LIKE ' . $db->quote('index.php?option=com_podcastmanager%'));
		$db->setQuery($query);
		$ids = $db->loadColumn();

		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				$query->clear();
				$query->delete($db->quoteName('#__menu'));
				$query->where($db->quoteName('id') . ' = ' . $db->quote($id));
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
		$query->where($db->quoteName('element') . ' = ' . $db->quote('com_podcastmanager'));
		$db->setQuery($query);
		$ids = $db->loadColumn();

		if (count($ids) > 1)
		{
			asort($ids);

			// Keep the oldest id
			$extension_id = array_shift($ids);

			foreach ($ids as $id)
			{
				$query->clear();
				$query->delete($db->quoteName('#__extensions'));
				$query->where($db->quoteName('extension_id') . ' = ' . $db->quote($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// If there are multiple assets records, delete all except the oldest one
		$query->clear();
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__assets'));
		$query->where($db->quoteName('name') . ' = ' . $db->quote('com_podcastmanager'));
		$db->setQuery($query);
		$ids = $db->loadObjectList();

		if (count($ids) > 1)
		{
			asort($ids);

			// Keep the oldest id
			$asset_id = array_shift($ids);

			foreach ($ids as $id)
			{
				$query->clear();
				$query->delete($db->quoteName('#__assets'));
				$query->where($db->quoteName('id') . ' = ' . $db->quote($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// Remove #__menu records for good measure!
		$query->clear();
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__menu'));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
		$query->where($db->quoteName('menutype') . ' = ' . $db->quote('main'));
		$query->where($db->quoteName('link') . ' LIKE ' . $db->quote('index.php?option=com_podcastmanager%'));
		$db->setQuery($query);
		$ids = $db->loadColumn();

		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				$query->clear();
				$query->delete($db->quoteName('#__menu'));
				$query->where($db->quoteName('id') . ' = ' . $db->quote($id));
				$db->setQuery($query);
				$db->query();
			}
		}
	}

	/**
	 * Function to get the currently installed version from the manifest cache
	 *
	 * @return  string  The version that is installed
	 *
	 * @since   1.7
	 */
	private function _getVersion()
	{
		static $version;

		// Only retrieve the version info once
		if (!$version)
		{
			return $version;
		}

		// Get the record from the database
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . ' = ' . $db->quote('com_podcastmanager'));
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
		$record = json_decode($manifest->manifest_cache);

		// Get the version
		$version = $record->version;

		return $version;
	}

	/**
	 * Function to remove language files from the system language folders due to changing to
	 * component language files for 2.0
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	private function _removeLanguageFiles()
	{
		jimport('joomla.filesystem.file');

		$adminBase = JPATH_ADMINISTRATOR . '/language/en-GB/';
		$siteBase = JPATH_SITE . '/language/en-GB/';

		// The language files for pre-2.0
		$adminFiles = array(
			'en-GB.com_podcastmanager.ini', 'en-GB.com_podcastmanager.sys.ini', 'en-GB.com_podcastmedia.ini', 'en-GB.com_podcastmedia.sys.ini',
			'en-GB.plg_content_podcastmanager.ini', 'en-GB.plg_content_podcastmanager.sys.ini', 'en-GB.plg_editors-xtd_podcastmanager.ini',
			'en-GB.plg_editors-xtd_podcastmanager.sys.ini'
		);
		$siteFiles = array(
			'en-GB.com_podcastmanager.ini', 'en-GB.com_podcastmanager.sys.ini', 'en-GB.com_podcastmedia.ini', 'en-GB.mod_podcastmanager.ini',
			'en-GB.mod_podcastmanager.sys.ini', 'en-GB.mod_podcastmanagerfeed.ini', 'en-GB.mod_podcastmanagerfeed.sys.ini'
		);

		// Remove the admin files
		foreach ($adminFiles as $adminFile)
		{
			if (is_file($adminBase . $adminFile))
			{
				JFile::delete($adminBase . $adminFile);
			}
		}

		// Remove the site files
		foreach ($siteFiles as $siteFile)
		{
			if (is_file($siteBase . $siteFile))
			{
				JFile::delete($siteBase . $siteFile);
			}
		}
	}

	/**
	 * Function to remove media folders from the component due to moving to the /media folder
	 *
	 * @return  void
	 *
	 * @since   2.1
	 */
	private function _removeMediaFolders()
	{
		jimport('joomla.filesystem.folder');

		// Build the folder array
		$folders = array(JPATH_ADMINISTRATOR . '/components/com_podcastmanager/media', JPATH_SITE . '/components/com_podcastmanager/media');

		// Remove the admin files
		foreach ($folders as $folder)
		{
			if (is_dir($folder))
			{
				JFolder::delete($folder);
			}
		}
	}
}
