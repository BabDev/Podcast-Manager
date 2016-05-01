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
	 * @return  boolean
	 *
	 * @since   1.7
	 * @throws  RuntimeException
	 */
	public function preflight($type, $parent)
	{
		// Make sure we aren't uninstalling first
		if ($type != 'uninstall')
		{
			// Check if Podcast Manager is installed
			if (!is_dir(JPATH_BASE . '/components/com_podcastmanager'))
			{
				throw new RuntimeException(JText::_('PLG_CONTENT_PODCASTMANAGER_ERROR_COMPONENT'));
			}
		}

		return true;
	}

	/**
	 * Function to perform changes when plugin is initially installed
	 *
	 * @param   JInstallerAdapterPlugin  $parent  The function calling this method
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
		$this->removeMediaElementJs();
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
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__extensions'))
			->set($db->quoteName('enabled') . ' = 1')
			->where($db->quoteName('name') . ' = ' . $db->quote('plg_content_podcastmanager'));
		$db;

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_EDITORS-PLG_CONTENT_PODCASTMANAGER_ERROR_ACTIVATING_PLUGIN'), 'notice');
		}
	}

	/**
	 * Remove the MediaElement.JS media previously shipped with this extension
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	private function removeMediaElementJs()
	{
		jimport('joomla.filesystem.folder');

		$folder = JPATH_ROOT . '/media/mediaelements';

		if (is_dir($folder))
		{
			JFolder::delete($folder);
		}
	}
}
