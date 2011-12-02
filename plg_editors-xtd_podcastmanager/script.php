<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  plg_editors-xtd_podcastmanager
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
 * @subpackage  plg_editors-xtd_podcastmanager
 * @since       1.6
 */
class PlgEditorsXtdPodcastManagerInstallerScript
{
	/**
	 * Function to act prior to installation process begins
	 *
	 * @param   string  $type    The action being performed
	 * @param   string  $parent  The function calling this method
	 *
	 * @return  mixed  Boolean false on failure, void otherwise
	 *
	 * @since   1.7
	 */
	public function preflight($type, $parent)
	{
		// Requires Joomla! 1.7.3 + Platform 11.3
		//@TODO: Revert version check to CMS version on 2.5 Alpha/Beta release
		$jversion = new JVersion;
		$jplatform = new JPlatform;
		if (version_compare($jplatform->getShortVersion(), '11.3', 'lt'))
		{
			JError::raiseNotice(null, JText::_('PLG_EDITORS-XTD_PODCASTMANAGER_ERROR_INSTALL_J17'));
			return false;
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
		$query->update('#__extensions');
		$query->set('enabled = 1');
		$query->where('name = ' . $db->quote('plg_editors-xtd_podcastmanager'));
		$db->setQuery($query);
		if (!$db->query())
		{
			JError::raiseNotice(1, JText::_('PLG_EDITORS-XTD_PODCASTMANAGER_ERROR_ACTIVATING_PLUGIN'));
		}
	}
}
