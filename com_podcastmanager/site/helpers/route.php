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
 * Routing helper class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.8
 */
abstract class PodcastManagerHelperRoute
{
	/**
	 * The format for the feed to route
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected static $format;

	/**
	 * An array of data to reference
	 *
	 * @var    array
	 * @since  1.8
	 */
	protected static $lookup;

	/**
	 * The type of link to lookup (feed/podcast)
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected static $type;

	/**
	 * Method to get the route to the selected feed's HTML view
	 *
	 * @param   integer  $id  The id of the feed.
	 *
	 * @return  string  The link to the item
	 *
	 * @since   2.0
	 */
	public static function getFeedHtmlRoute($id)
	{
		$needles = [
			'feed' => [(int) $id]
		];

		// Set some vars for further processing
		static::$format = 'html';
		static::$type = 'feed';

		if ($id < 1)
		{
			return '';
		}

		if ($item = static::findItem($needles))
		{
			return 'index.php?Itemid=' . $item;
		}

		// Create the link
		$link = 'index.php?option=com_podcastmanager&view=feed&layout=feed&feedname=' . $id;

		if ($item = static::findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = static::findItem())
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Method to get the route to the selected feed's RSS view
	 *
	 * @param   integer  $id  The id of the feed.
	 *
	 * @return  string  The link to the item
	 *
	 * @since   2.0
	 */
	public static function getFeedRssRoute($id)
	{
		$needles = [
			'feed' => [(int) $id]
		];

		// Set some vars for further processing
		static::$format = 'raw';
		static::$type = 'feed';

		if ($id < 1)
		{
			return '';
		}

		if ($item = static::findItem($needles))
		{
			return 'index.php?Itemid=' . $item;
		}

		// Create the link
		$link = 'index.php?option=com_podcastmanager&format=raw&feedname=' . $id;

		if ($item = static::findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = static::findItem())
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Method to get the route to the feed edit view
	 *
	 * @param   integer  $id      The id of the feed.
	 * @param   string   $return  The return page variable.
	 *
	 * @return  string  The link to the item
	 *
	 * @since   1.8
	 */
	public static function getFeedEditRoute($id, $return = null)
	{
		// Create the link.
		$link = 'index.php?option=com_podcastmanager&task=form.edit&layout=edit&feedname=' . $id;

		if ($return)
		{
			$link .= '&return=' . $return;
		}

		return $link;
	}

	/**
	 * Method to get the route to the selected podcast
	 *
	 * @param   integer  $id  The id of the podcast.
	 *
	 * @return  string  The link to the item
	 *
	 * @since   1.8
	 */
	public static function getPodcastRoute($id)
	{
		$needles = [
			'podcast' => [(int) $id]
		];

		// Create the link
		$link = 'index.php?option=com_podcastmanager&view=podcast&id=' . $id;

		if ($item = static::findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = static::findItem())
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Method to get the route to the podcast edit view
	 *
	 * @param   integer  $id      The id of the podcast.
	 * @param   string   $return  The return page variable.
	 *
	 * @return  string  The link to the item
	 *
	 * @since   1.8
	 */
	public static function getPodcastEditRoute($id, $return = null)
	{
		// Create the link.
		$link = 'index.php?option=com_podcastmanager&task=podcast.edit&layout=edit&p_id=' . $id;

		if ($return)
		{
			$link .= '&return=' . $return;
		}

		return $link;
	}

	/**
	 * Method to lookup whether the item is within the menu structure
	 *
	 * @param   array  $needles  The menu items.
	 *
	 * @return  mixed
	 *
	 * @since   1.8
	 */
	protected static function findItem($needles = null)
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (static::$lookup === null)
		{
			static::$lookup = [];

			$component = JComponentHelper::getComponent('com_podcastmanager');
			$items = $menus->getItems('component_id', $component->id);

			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];

					if (!isset(static::$lookup[$view]))
					{
						static::$lookup[$view] = [];
					}

					// Some trickery to get the right link for the feeds
					if (isset(static::$type) && static::$type == 'feed')
					{
						if ($item->query['format'] == static::$format)
						{
							if (isset($item->query['feedname']))
							{
								static::$lookup[$view][$item->query['feedname']] = $item->id;
							}
						}
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(static::$lookup[$view]))
				{
					foreach ($ids as $id)
					{
						if (isset(static::$lookup[$view][(int) $id]))
						{
							return static::$lookup[$view][(int) $id];
						}
					}
				}
			}
		}
		else
		{
			$active = $menus->getActive();

			if ($active && $active->component == 'com_podcastmanager')
			{
				return $active->id;
			}
		}

		return null;
	}
}
