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
			JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $e->getMessage()), 'warning');
		}
	}
}
