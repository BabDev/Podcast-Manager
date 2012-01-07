<?php
/**
* Podcast Manager for Joomla!
*
* @package     PodcastManager
* @subpackage  com_podcastmanager
*
* @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
* @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

defined('_JEXEC') or die;

/**
 * Podcast Manager helper class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.6
 */
class PodcastManagerHelper
{
	/**
	 * The extension name
	 *
	 * @var    string
	 * @since  1.6
	 */
	public static $extension = 'com_podcastmanager';

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_PODCASTMANAGER_SUBMENU_FEEDS'),
			'index.php?option=com_podcastmanager&view=feeds',
			$vName == 'feeds'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_PODCASTMANAGER_SUBMENU_PODCASTS'),
			'index.php?option=com_podcastmanager&view=podcasts',
			$vName == 'podcasts'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_PODCASTMANAGER_SUBMENU_FILES'),
			'index.php?option=com_podcastmedia&view=media',
			$vName == 'media'
		);
	}

	/**
	 * Counts the number of active podcastmedia plugins
	 *
	 * @return  string  The number of active plugins
	 *
	 * @since   2.0
	 */
	public static function countMediaPlugins()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(extension_id)');
		$query->from('#__extensions');
		$query->where('folder = ' . $db->quote('podcastmedia'));
		$query->where('enabled = 1');
		$db->setQuery($query);
		$count = $db->loadResult();

		return $count;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   integer  $feedId     The feed ID.
	 * @param   integer  $podcastId  The podcast ID
	 *
	 * @return  JObject  A JObject containing the allowed actions
	 *
	 * @since   1.6
	 */
	public static function getActions($feedId = 0, $podcastId = 0)
	{
		$user		= JFactory::getUser();
		$result		= new JObject;

		if (empty($podcastId) && empty($feedId))
		{
			$assetName = 'com_podcastmanager';
		}
		elseif (empty($podcastId))
		{
			$assetName = 'com_podcastmanager.feed.' . (int) $feedId;
		}
		else
		{
			$assetName = 'com_podcastmanager.podcast.' . (int) $podcastId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.edit.own', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	 * Method to return a list of all feeds that a user has permission for a given action
	 *
	 * @param   string  $action  The action to check.
	 *
	 * @return  array  List of feeds that this group can do this action to (empty array if none).  Feeds must be published.
	 *
	 * @since   2.0
	 */
	public static function getAuthorisedFeeds($action)
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('f.id AS id, a.name as asset_name');
		$query->from('#__podcastmanager_feeds AS f');
		$query->innerJoin('#__assets AS a ON f.asset_id = a.id');
		$query->where('f.published = 1');
		$db->setQuery($query);
		$allFeeds = $db->loadObjectList('id');
		$allowedFeeds = array();
		foreach ($allFeeds as $feed)
		{
			if ($user->authorise($action, $feed->asset_name))
			{
				$allowedFeeds[] = (int) $feed->id;
			}
		}
		return $allowedFeeds;
	}
}
