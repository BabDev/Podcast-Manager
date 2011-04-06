<?php
/**
 * Podcast Manager for Joomla!
 *
 * @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 */

// Restricted access
defined('_JEXEC') or die();

/**
 * Podcast Manager component helper.
 */
class PodcastManagerHelper
{
	public static $extension = 'com_podcastmanager';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
		JText::_('COM_PODCASTMANAGER_SUBMENU_PODCASTS'),
			'index.php?option=com_podcastmanager&view=podcasts',
			$vName == 'podcasts');
		JSubMenuHelper::addEntry(
		JText::_('COM_PODCASTMANAGER_SUBMENU_INFO'),
			'index.php?option=com_podcastmanager&view=info',
			$vName == 'info');
		JSubMenuHelper::addEntry(
		JText::_('COM_PODCASTMANAGER_SUBMENU_FILES'),
			'index.php?option=com_podcastmedia&view=media',
			$vName == 'media');
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user		= JFactory::getUser();
		$result		= new JObject;
		$assetName	= 'com_podcastmanager';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
			);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
