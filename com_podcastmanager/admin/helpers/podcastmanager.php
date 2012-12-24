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
abstract class PodcastManagerHelper
{
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
		// Use the sidebar layout for 3.0, submenu module in 2.5
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$class = 'JHtmlSidebar';
		}
		else
		{
			$class = 'JSubMenuHelper';
		}

		$class::addEntry(
			JText::_('COM_PODCASTMANAGER_SUBMENU_FEEDS'),
			'index.php?option=com_podcastmanager&view=feeds',
			$vName == 'feeds'
		);
		$class::addEntry(
			JText::_('COM_PODCASTMANAGER_SUBMENU_PODCASTS'),
			'index.php?option=com_podcastmanager&view=podcasts',
			$vName == 'podcasts'
		);
		$class::addEntry(
			JText::_('COM_PODCASTMANAGER_SUBMENU_FILES'),
			'index.php?option=com_podcastmedia&view=media',
			$vName == 'media'
		);
	}

	/**
	 * Counts the number of active podcastmedia plugins
	 *
	 * @return  integer  The number of active plugins
	 *
	 * @since   2.0
	 */
	public static function countMediaPlugins()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(extension_id)');
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('folder') . ' = ' . $db->quote('podcastmedia'));
		$query->where($db->quoteName('enabled') . ' = 1');
		$db->setQuery($query);
		$count = $db->loadResult();

		return (int) $count;
	}

	/**
	 * Method to process the file through the getID3 library to extract key data
	 *
	 * @param   mixed  $data  The data object for the form
	 *
	 * @return  mixed  The processed data for the form.
	 *
	 * @since   2.0
	 */
	public static function fillMetaData($data)
	{
		jimport('getid3.getid3');
		define('GETID3_HELPERAPPSDIR', JPATH_PLATFORM . '/getid3');

		$filename = $_COOKIE['podManFile'];

		// Set the filename field (fallback for if session data doesn't retain)
		$data->filename = $_COOKIE['podManFile'];

		if (!preg_match('/^http/', $filename))
		{
			$filename = JPATH_ROOT . '/' . $filename;
		}

		// Only push through getID3 if the file actually exists and is local
		if (!preg_match('/^http/', $filename) && is_file($filename))
		{
			$getID3 = new getID3;
			$getID3->setOption(array('encoding' => 'UTF-8'));
			$fileInfo = $getID3->analyze($filename);

			// Check if there's an error from getID3
			if (isset($fileInfo['error']))
			{
				foreach ($fileInfo['error'] as $error)
				{
					JError::raiseNotice('500', $error);
				}
			}

			// Check if there's a warning from getID3
			if (isset($fileInfo['warning']))
			{
				foreach ($fileInfo['warning'] as $warning)
				{
					JError::raiseWarning('500', $warning);
				}
			}

			if (isset($fileInfo['tags_html']))
			{
				$t = $fileInfo['tags_html'];
				$tags = isset($t['id3v2']) ? $t['id3v2'] : (isset($t['id3v1']) ? $t['id3v1'] : (isset($t['quicktime']) ? $t['quicktime'] : null));

				if ($tags)
				{
					// Set the title field
					if (isset($tags['title']))
					{
						$data->title = $tags['title'][0];
					}
					// Set the album field
					if (isset($tags['album']))
					{
						$data->itSubtitle = $tags['album'][0];
					}
					// Set the artist field
					if (isset($tags['artist']))
					{
						$data->itAuthor = $tags['artist'][0];
					}
				}
			}

			// Set the duration field
			if (isset($fileInfo['playtime_string']))
			{
				$data->itDuration = $fileInfo['playtime_string'];
			}

			// Set the MIME type
			if (isset($fileInfo['mime_type']))
			{
				$data->mime = $fileInfo['mime_type'];
			}
		}

		return $data;
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
		$user   = JFactory::getUser();
		$result = new JObject;

		if (empty($podcastId) && empty($feedId))
		{
			$assetName = 'com_podcastmanager';
			$level = 'component';
		}
		elseif (empty($podcastId))
		{
			$assetName = 'com_podcastmanager.feed.' . (int) $feedId;
			$level = 'feed';
		}
		else
		{
			$assetName = 'com_podcastmanager.podcast.' . (int) $podcastId;
			$level = 'feed';
		}

		$actions = JAccess::getActions('com_podcastmanager', $level);

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
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
		$query->select($db->quoteName(array('f.id', 'a.name'), array('id', 'asset_name')));
		$query->from($db->quoteName('#__podcastmanager_feeds', 'f'));
		$query->innerJoin($db->quoteName('#__assets', 'a') . ' ON f.asset_id = a.id');
		$query->where($db->quoteName('f.published') . ' = 1');
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

	/**
	 * Method to get the route for a feed
	 *
	 * @param   string  $url  The URL to process
	 *
	 * @return  string  The routed URL
	 *
	 * @since   2.0
	 */
	public static function getFeedRoute($url)
	{
		// Get the router.
		$app = JApplication::getInstance('site');
		$router = $app->getRouter();

		// Make sure that we have our router
		if (!$router)
		{
			return null;
		}

		if ((strpos($url, '&') !== 0) && (strpos($url, 'index.php') !== 0))
		{
			return $url;
		}

		// Build route.
		$uri = $router->build($url);
		$url = $uri->toString(array('path', 'query', 'fragment'));

		// Replace spaces.
		$url = preg_replace('/\s/u', '%20', $url);

		// Replace '/administrator'
		$url = str_replace('/administrator', '', $url);

		// Strip .html, just in case
		$url = str_replace('.html', '', $url);

		$url = htmlspecialchars($url);

		return $url;
	}

	/**
	 * Method to return the URL to a media file with optional stat tracking information added
	 *
	 * @param   string  $url  The media file URL
	 *
	 * @return  string  The URL for the file based on the stat tracking configuration
	 *
	 * @since   2.1
	 */
	public static function getMediaUrl($url)
	{
		static $params;

		// Get the component params if we don't have them already
		if (!$params)
		{
			$params = JComponentHelper::getParams('com_podcastmanager');
		}

		// Get the values for the tracking service
		$tracking  = $params->get('tracking', 'none');
		$trackUser = $params->get('trackname', '');

		$replacement = str_replace(array('http://', 'https://'), '', $url);

		switch ($tracking)
		{
			case 'blubrry':
				return 'http://media.blubrry.com/' . $trackUser . '/' . $replacement;

			case 'podtrac':
				return 'http://www.podtrac.com/pts/redirect.mp3/' . $replacement;

			default:
				return $url;
		}

	}
}
