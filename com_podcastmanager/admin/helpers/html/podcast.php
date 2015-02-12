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
 * HTML Utility class for Podcast Manager
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.8
 */
abstract class JHtmlPodcast
{
	/**
	 * Cached array of the feed items.
	 *
	 * @var    array
	 * @since  1.8
	 */
	protected static $items = array();

	/**
	 * Returns a list of feeds.
	 *
	 * @param   array  $config  An array of configuration options. By default, only published and unpublished feeds are returned.
	 *
	 * @return  array  An array of items.
	 *
	 * @since   1.8
	 */
	public static function feeds($config = array('filter.published' => array(0, 1)))
	{
		$hash = md5('com_podcastmanager.' . serialize($config));

		if (!isset(static::$items[$hash]))
		{
			$config = (array) $config;
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select($db->quoteName(array('a.id', 'a.name')));
			$query->from($db->quoteName('#__podcastmanager_feeds', 'a'));

			// Filter on the published state
			if (isset($config['filter.published']))
			{
				if (is_numeric($config['filter.published']))
				{
					$query->where($db->quoteName('a.published') . ' = ' . (int) $config['filter.published']);
				}
				elseif (is_array($config['filter.published']))
				{
					JArrayHelper::toInteger($config['filter.published']);
					$query->where($db->quoteName('a.published') . ' IN (' . implode(',', $config['filter.published']) . ')');
				}
			}

			$query->order('a.id');

			$db->setQuery($query);
			$items = $db->loadObjectList();

			// Assemble the list options.
			static::$items[$hash] = array();

			foreach ($items as &$item)
			{
				static::$items[$hash][] = JHtml::_('select.option', $item->id, $item->name);
			}

			// "No Feed" option:
			static::$items[$hash][] = JHtml::_('select.option', '0', JText::_('JNONE'));
		}

		return static::$items[$hash];
	}
}
