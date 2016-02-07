<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
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
 * @subpackage  com_podcastmedia
 * @since       1.6
 */
class Com_PodcastMediaInstallerScript
{
	/**
	 * Function to perform changes during update
	 *
	 * @param   JInstallerAdapterComponent  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	public function update($parent)
	{
		// Get the pre-update version
		$version = $this->getVersion();

		// If in error, throw a message
		if ($version == 'Error')
		{
			JError::raiseNotice(null, JText::_('COM_PODCASTMEDIA_ERROR_INSTALL_UPDATE'));

			return;
		}
	}

	/**
	 * Function to perform changes when component is initially installed
	 *
	 * @param   string                      $type    The action being performed
	 * @param   JInstallerAdapterComponent  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function postflight($type, $parent)
	{
		$this->removeMenu();
	}

	/**
	 * Function to get the currently installed version from the manifest cache
	 *
	 * @return  string  The version that is installed
	 *
	 * @since   2.0
	 */
	private function getVersion()
	{
		// Get the record from the database
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('manifest_cache'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('element') . ' = ' . $db->quote('com_podcastmedia'));

		try
		{
			$manifest = $db->setQuery($query)->loadObject();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $e->getMessage()));

			return 'Error';
		}

		// Decode the JSON
		$record = json_decode($manifest->manifest_cache);

		// Get the version
		return $record->version;
	}

	/**
	 * Function to remove the menu item
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	private function removeMenu()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__menu'))
			->where($db->quoteName('title') . ' = ' . $db->quote('com_podcastmedia'));

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $e->getMessage()));
		}
	}
}
