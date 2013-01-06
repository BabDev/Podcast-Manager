<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  mod_podcastmanagerfeed
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Import the external requirements
JLoader::register('PodcastManagerHelper', JPATH_ADMINISTRATOR . '/components/com_podcastmanager/helpers/podcastmanager.php');
JLoader::register('PodcastManagerHelperRoute', JPATH_SITE . '/components/com_podcastmanager/helpers/route.php');
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_podcastmanager/models', 'PodcastManagerModel');

/**
 * Podcast Manager feed listing module.
 *
 * @package     PodcastManager
 * @subpackage  mod_podcastmanagerfeed
 * @since       1.8
 */
abstract class ModPodcastManagerFeedHelper
{
	/**
	 * Function to get the list of items
	 *
	 * @param   JRegistry  &$params  The module params
	 *
	 * @return  array  An array of items
	 *
	 * @since   1.8
	 */
	public static function getList(&$params)
	{
		// Get an instance of the generic feed model
		$model = JModelLegacy::getInstance('Feed', 'PodcastManagerModel', array('ignore_request' => true));

		// Set application parameters in model
		$app       = JFactory::getApplication('site');
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 5));
		$model->setState('filter.published', 1);
		$model->setState('filter.publish_date', true);

		// Feed filter
		$model->setState('feed.id', $params->get('feed', ''));

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

		// Set ordering
		$model->setState('list.ordering', 'a.publish_up');
		$model->setState('list.direction', 'DESC');

		$items = $model->getItems();

		foreach ($items as &$item)
		{
			$item->link = $item->filename;

			// Check if the file is from off site
			if (!preg_match('/^http/', $item->link))
			{
				$item->link = JUri::base() . $item->filename;
			}

			// Process the URL through the helper to get the stat tracking details if applicable
			$item->link = PodcastManagerHelper::getMediaUrl($item->link);
		}

		// If we're displaying the media player, and the plugin is enabled, then render it here
		if ($params->get('show_item_player', '0') == 1 && JPluginHelper::isEnabled('content', 'podcastmanager'))
		{
			$dispatcher = JDispatcher::getInstance();

			// Get the Podcast Manager Content plugin
			JPluginHelper::importPlugin('content', 'podcastmanager');

			// Handle each item separately
			foreach ($items as &$item)
			{
				// Set the text object to prevent errors
				$item->text = '';

				// Preload the player syntax
				$item->player = '{podcast ' . $item->title . '}';

				// Trigger the plugin
				$dispatcher->trigger('onContentPrepare', array('mod_podcastmanagerfeed.module', &$item, &$params));
			}
		}

		return $items;
	}
}
