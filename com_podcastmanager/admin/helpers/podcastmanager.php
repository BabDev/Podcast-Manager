<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
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
		JHtmlSidebar::addEntry(
			JText::_('COM_PODCASTMANAGER_SUBMENU_FEEDS'),
			'index.php?option=com_podcastmanager&view=feeds',
			$vName == 'feeds'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_PODCASTMANAGER_SUBMENU_PODCASTS'),
			'index.php?option=com_podcastmanager&view=podcasts',
			$vName == 'podcasts'
		);
		JHtmlSidebar::addEntry(
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
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('COUNT(extension_id)')
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('podcastmedia'))
			->where($db->quoteName('enabled') . ' = 1');
		$count = $db->setQuery($query)->loadResult();

		return (int) $count;
	}

	/**
	 * Method to process the file through the getID3 library to extract key data
	 *
	 * @param   string  $filename  The filename to be processed
	 *
	 * @return  mixed  The processed data for the form.
	 *
	 * @since   2.0
	 * @throws  RuntimeException
	 */
	public static function fillMetaData($filename)
	{
		// Throw an error if for some reason getID3 isn't found
		if (!is_file(JPATH_PLATFORM . '/getid3/getid3.php'))
		{
			throw new RuntimeException(JText::_('COM_PODCASTMANAGER_GETID3_NOT_FOUND'));
		}

		// Import the getID3 library
		JLoader::register('getID3', JPATH_PLATFORM . '/getid3/getid3.php');
		define('GETID3_HELPERAPPSDIR', JPATH_PLATFORM . '/getid3');

		// Only push through getID3 if the file actually exists and is local
		if (preg_match('/^http/', $filename))
		{
			throw new RuntimeException(JText::_('COM_PODCASTMANAGER_GETID3_CANNOT_PROCESS_REMOTE'));
		}

		$filename = JPATH_ROOT . '/' . $filename;

		if (!is_file($filename))
		{
			throw new RuntimeException(JText::_('COM_PODCASTMANAGER_GETID3_FILE_NOT_FOUND'));
		}

		$data           = new stdClass;
		$data->messages = [];

		// Instantiate getID3 and get the metadata
		$getID3   = new getID3;
		$getID3->setOption(['encoding' => 'UTF-8']);
		$fileInfo = $getID3->analyze($filename);

		// Check if there's an error from getID3
		if (isset($fileInfo['error']))
		{
			$data->messages['error'] = [];

			foreach ($fileInfo['error'] as $error)
			{
				$data->messages['error'][] = $error;
			}
		}

		// Check if there's a warning from getID3
		if (isset($fileInfo['warning']))
		{
			$data->messages['warning'] = [];

			foreach ($fileInfo['warning'] as $warning)
			{
				$data->messages['warning'][] = $warning;
			}
		}

		if (isset($fileInfo['tags']))
		{
			$t    = $fileInfo['tags'];
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
				$artist = isset($tags['album_artist']) ? $tags['album_artist'] : (isset($tags['artist']) ? $tags['artist'] : null);

				if (!is_null($artist))
				{
					$data->itAuthor = $artist;
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
			$level     = 'component';
		}
		elseif (empty($podcastId))
		{
			$assetName = 'com_podcastmanager.feed.' . (int) $feedId;
			$level     = 'feed';
		}
		else
		{
			$assetName = 'com_podcastmanager.podcast.' . (int) $podcastId;
			$level     = 'feed';
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
		$user  = JFactory::getUser();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName(['f.id', 'a.name'], ['id', 'asset_name']))
			->from($db->quoteName('#__podcastmanager_feeds', 'f'))
			->innerJoin($db->quoteName('#__assets', 'a') . ' ON f.asset_id = a.id')
			->where($db->quoteName('f.published') . ' = 1');

		$allFeeds     = $db->setQuery($query)->loadObjectList('id');
		$allowedFeeds = [];

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
		$router = JApplicationCms::getInstance('site')->getRouter();

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
		$url = $uri->toString(['path', 'query', 'fragment']);

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

		$replacement = str_replace(['http://', 'https://'], '', $url);

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

	/**
	 * Method to insert records for the UCM tables
	 *
	 * @return  void
	 *
	 * @since   2.2
	 */
	public static function insertUcmRecords()
	{
		// Insert the rows in the #__content_types table if they don't exist already
		$db = JFactory::getDbo();

		// Get the type ID for a Podcast Manager feed
		$query = $db->getQuery(true)
			->select($db->quoteName('type_id'))
			->from($db->quoteName('#__content_types'))
			->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_podcastmanager.feed'));

		try
		{
			$feedTypeId = $db->setQuery($query)->loadResult();
		}
		catch (RuntimeException $e)
		{
			// TODO - Error handling
		}

		// Get the type ID for a Podcast Manager podcast
		$query->clear('where')
			->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_podcastmanager.podcast'));

		try
		{
			$podcastTypeId = $db->setQuery($query)->loadResult();
		}
		catch (RuntimeException $e)
		{
			// TODO - Error handling
		}

		// If we don't have the feed type ID, assume the type data doesn't exist yet
		if (!$feedTypeId)
		{
			// This object contains all fields that are mapped to the core_content table
			$commonObject                           = new stdClass;
			$commonObject->core_title               = 'name';
			$commonObject->core_alias               = 'alias';
			$commonObject->core_body                = 'description';
			$commonObject->core_state               = 'published';
			$commonObject->core_checked_out_time    = 'checked_out_time';
			$commonObject->core_checked_out_user_id = 'checked_out';
			$commonObject->core_created_user_id     = 'created_by';
			$commonObject->core_created_by_alias    = 'author';
			$commonObject->core_created_time        = 'created';
			$commonObject->core_modified_user_id    = 'modified_by';
			$commonObject->core_modified_time       = 'modified';
			$commonObject->core_language            = 'language';
			$commonObject->core_content_item_id     = 'id';
			$commonObject->asset_id                 = 'asset_id';

			// This object contains unique fields
			$specialObject              = new stdClass;
			$specialObject->subtitle    = 'subtitle';
			$specialObject->boilerplate = 'boilerplate';
			$specialObject->bp_position = 'bp_position';
			$specialObject->copyright   = 'copyright';
			$specialObject->explicit    = 'explicit';
			$specialObject->block       = 'block';
			$specialObject->ownername   = 'ownername';
			$specialObject->owneremail  = 'owneremail';
			$specialObject->keywords    = 'keywords';
			$specialObject->newFeed     = 'newFeed';
			$specialObject->image       = 'image';
			$specialObject->category1   = 'category1';
			$specialObject->category2   = 'category2';
			$specialObject->category3   = 'category3';

			// Prepare the object
			$fieldMappings = [
				'common'  => [
					$commonObject
				],
				'special' => [
					$specialObject
				]
			];

			// Set the table columns to insert table to
			$columnsArray = [
				$db->quoteName('type_title'), $db->quoteName('type_alias'), $db->quoteName('table'),
				$db->quoteName('rules'), $db->quoteName('field_mappings'), $db->quoteName('router'),
				$db->quoteName('content_history_options')
			];

			// Insert the data. @TODO - Break the columns into objects for better management
			$query->clear()
				->insert($db->quoteName('#__content_types'))
				->columns($columnsArray)
				->values(
					$db->quote('Podcast Manager Feed') . ', '
					. $db->quote('com_podcastmanager.feed') . ', '
					. $db->quote('{"special":{"dbtable":"#__podcastmanager_feeds","key":"id","type":"Feed","prefix":"PodcastManagerTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}')
					. ', '
					. $db->quote('') . ', '
					. $db->quote(json_encode($fieldMappings)) . ', '
					. $db->quote('PodcastManagerHelperRoute::getFeedHtmlRoute') . ', '
					. $db->quote('{"formFile":"administrator\\/components\\/com_podcastmanager\\/models\\/forms\\/feed.xml", "hideFields":["asset_id","checked_out","checked_out_time"],"ignoreChanges":["modified_by","modified","checked_out","checked_out_time"],"convertToInt":[],"displayLookup":[{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}')
				);

			try
			{
				$db->setQuery($query)->execute();
			}
			catch (RuntimeException $e)
			{
				// TODO - Error handling
			}
		}

		// If we don't have the podcast type ID, assume the type data doesn't exist yet
		if (!$podcastTypeId)
		{
			// This object contains all fields that are mapped to the core_content table
			$commonObject                           = new stdClass;
			$commonObject->core_title               = 'title';
			$commonObject->core_alias               = 'alias';
			$commonObject->core_body                = 'itSummary';
			$commonObject->core_state               = 'published';
			$commonObject->core_checked_out_time    = 'checked_out_time';
			$commonObject->core_checked_out_user_id = 'checked_out';
			$commonObject->core_created_user_id     = 'created_by';
			$commonObject->core_created_by_alias    = 'itAuthor';
			$commonObject->core_created_time        = 'created';
			$commonObject->core_modified_user_id    = 'modified_by';
			$commonObject->core_modified_time       = 'modified';
			$commonObject->core_language            = 'language';
			$commonObject->core_publish_up          = 'publish_up';
			$commonObject->core_content_item_id     = 'id';
			$commonObject->asset_id                 = 'asset_id';

			// This object contains unique fields
			$specialObject             = new stdClass;
			$specialObject->filename   = 'filename';
			$specialObject->feedname   = 'feedname';
			$specialObject->itBlock    = 'itBlock';
			$specialObject->itDuration = 'itDuration';
			$specialObject->itExplicit = 'itExplicit';
			$specialObject->itImage    = 'itImage';
			$specialObject->itKeywords = 'itKeywords';
			$specialObject->itSubtitle = 'itSubtitle';
			$specialObject->mime       = 'mime';

			// Prepare the object
			$fieldMappings = [
				'common'  => [
					$commonObject
				],
				'special' => [
					$specialObject
				]
			];

			// Set the table columns to insert table to
			$columnsArray = [
				$db->quoteName('type_title'), $db->quoteName('type_alias'), $db->quoteName('table'),
				$db->quoteName('rules'), $db->quoteName('field_mappings'), $db->quoteName('router'),
				$db->quoteName('content_history_options')
			];

			// Insert the link.
			$query->clear()
				->insert($db->quoteName('#__content_types'))
				->columns($columnsArray)
				->values(
					$db->quote('Podcast Manager Podcast') . ', '
					. $db->quote('com_podcastmanager.podcast') . ', '
					. $db->quote('{"special":{"dbtable":"#__podcastmanager","key":"id","type":"Podcast","prefix":"PodcastManagerTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}')
					. ', '
					. $db->quote('') . ', '
					. $db->quote(json_encode($fieldMappings)) . ', '
					. $db->quote('PodcastManagerHelperRoute::getPodcastRoute') . ', '
					. $db->quote('{"formFile":"administrator\\/components\\/com_podcastmanager\\/models\\/forms\\/podcast.xml", "hideFields":["asset_id","checked_out","checked_out_time"],"ignoreChanges":["modified_by","modified","checked_out","checked_out_time"],"convertToInt":["publish_up"],"displayLookup":[{"sourceColumn":"feedname","targetTable":"#__podcastmanager_feeds","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}')
				);

			try
			{
				$db->setQuery($query)->execute();
			}
			catch (RuntimeException $e)
			{
				// TODO - Error handling
			}
		}
	}
}
