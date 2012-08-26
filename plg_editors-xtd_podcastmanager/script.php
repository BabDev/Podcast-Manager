<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  plg_editors-xtd_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     PodcastManager
 * @subpackage  plg_editors-xtd_podcastmanager
 * @since       1.6
 */
class PlgEditorsXtdPodcastManagerInstallerScript
{
	/**
	 * Function to act prior to installation process begins
	 *
	 * @param   string            $type    The action being performed
	 * @param   JInstallerPlugin  $parent  The function calling this method
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
				JError::raiseNotice(null, JText::_('PLG_EDITORS-XTD_PODCASTMANAGER_ERROR_COMPONENT'));
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
	 * Function to activate the button at installation
	 *
	 * @return  void
	 *
	 * @since   1.7
	 */
	protected function activateButton()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__extensions'));
		$query->set($db->quoteName('enabled') . ' = 1');
		$query->where($db->quoteName('name') . ' = ' . $db->quote('plg_editors-xtd_podcastmanager'));
		$db->setQuery($query);
		if (!$db->query())
		{
			JError::raiseNotice(1, JText::_('PLG_EDITORS-XTD_PODCASTMANAGER_ERROR_ACTIVATING_PLUGIN'));
		}
	}
}
