<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  plg_content_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     PodcastManager
 * @subpackage  plg_content_podcastmanager
 * @since       1.6
 */
class PlgContentPodcastManagerInstallerScript
{
	/**
	 * Function to act prior to installation process begins
	 *
	 * @param   string                   $type    The action being performed
	 * @param   JInstallerAdapterPlugin  $parent  The function calling this method
	 *
	 * @return  mixed  Boolean false on failure, void otherwise
	 *
	 * @since   1.7
	 */
	public function preflight($type, $parent)
	{
		// Make sure we aren't uninstalling first
		if ($type != 'uninstall')
		{
			// Check if Podcast Manager is installed
			if (!is_dir(JPATH_BASE . '/components/com_podcastmanager'))
			{
				JError::raiseNotice(null, JText::_('PLG_CONTENT_PODCASTMANAGER_ERROR_COMPONENT'));

				return false;
			}
		}

		return true;
	}

	/**
	 * Function to perform changes when plugin is initially installed
	 *
	 * @param   string  $parent  The function calling this method
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function install($parent)
	{
		$this->activateButton();
	}

	/**
	 * Function to perform changes during update
	 *
	 * @param   JInstallerAdapterPlugin  $parent  The class calling this method
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

		// If coming from 1.x, remove old media folders
		if (version_compare($version, '2.0', 'lt'))
		{
			$this->_removeMediaFolders();
		}
	}

	/**
	 * Function to activate the button at installation
	 *
	 * @return  void
	 *
	 * @since   1.7
	 */
	protected function activateButton()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__extensions'));
		$query->set($db->quoteName('enabled') . ' = 1');
		$query->where($db->quoteName('name') . ' = ' . $db->quote('plg_editors-plg_content_podcastmanager'));
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			JError::raiseNotice(1, JText::_('PLG_EDITORS-PLG_CONTENT_PODCASTMANAGER_ERROR_ACTIVATING_PLUGIN'));
		}
	}

	/**
	 * Function to get the currently installed version from the manifest cache
	 *
	 * @return  string  The version that is installed
	 *
	 * @since   2.0
	 */
	private function _getVersion()
	{
		// Get the record from the database
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . ' = ' . $db->quote('podcastmanager'), 'AND');
		$query->where($db->quoteName('folder') . ' = ' . $db->quote('content'), 'AND');
		$db->setQuery($query);

		try
		{
			$manifest = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $e->getMessage()));
			$version = 'Error';

			return $version;
		}

		// Decode the JSON
		$record = json_decode($manifest->manifest_cache);

		// Get the version
		return $record->version;
	}

	/**
	 * Function to remove old media folders for players removed in 2.0
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	private function _removeMediaFolders()
	{
		jimport('joomla.filesystem.folder');

		$base = JPATH_SITE . '/plugins/content/podcastmanager/';

		// The folders to remove
		$folders = array('podcast', 'soundmanager');

		// Remove the folders
		foreach ($folders as $folder)
		{
			if (is_dir($base . $folder))
			{
				JFolder::delete($base . $folder);
			}
		}
	}
}
