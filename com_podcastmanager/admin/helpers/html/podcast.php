<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* @package		PodcastManager
* @subpackage	com_podcastmanager
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('JPATH_LIBRARIES') or die;

/**
 * HTML Utility class for Podcast Manager
 *
 * @since		1.8
 */
abstract class JHtmlPodcast
{
	/**
	 * @var    array  Cached array of the category items.
	 */
	protected static $items = array();

	/**
	 * Returns an array of categories for the given extension.
	 *
	 * @param   string  The extension option.
	 * @param   array   An array of configuration options. By default, only published and unpulbished categories are returned.
	 *
	 * @return  array
	 */
	public static function feeds($extension, $config = array('filter.published' => array(0,1)))
	{
		$hash = md5($extension.'.'.serialize($config));

		if (!isset(self::$items[$hash])) {
			$config	= (array) $config;
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select($db->quoteName('a.id').', '.$db->quoteName('a.name'));
			$query->from($db->quoteName('#__podcastmanager_feeds').' AS a');

			// Filter on the published state
			if (isset($config['filter.published'])) {
				if (is_numeric($config['filter.published'])) {
					$query->where('a.published = '.(int) $config['filter.published']);
				} else if (is_array($config['filter.published'])) {
					JArrayHelper::toInteger($config['filter.published']);
					$query->where($db->quoteName('a.published').' IN ('.implode(',', $config['filter.published']).')');
				}
			}

			$query->order($db->quoteName('a.id'));

			$db->setQuery($query);
			$items = $db->loadObjectList();

			// Assemble the list options.
			self::$items[$hash] = array();

			foreach ($items as &$item) {
				self::$items[$hash][] = JHtml::_('select.option', $item->id, $item->name);
			}
			// "No Feed" option:
			self::$items[$hash][] = JHtml::_('select.option', '0', JText::_('JNONE'));
		}

		return self::$items[$hash];
	}
}