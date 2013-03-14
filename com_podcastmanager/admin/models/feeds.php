<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Feed management model class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.7
 */
class PodcastManagerModelFeeds extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.7
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'published', 'a.published',
				'language', 'a.language'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.7
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId('getItems');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$items = parent::getItems();

		// If empty or an error, just return.
		if (empty($items))
		{
			return array();
		}

		// Getting the following metric by joins is WAY TOO SLOW.

		// Get the feeds in the list.
		$db = $this->getDbo();
		$feedNames = JArrayHelper::getColumn($items, 'id');

		// Quote the strings.
		$feedNames = implode(',', array_map(array($db, 'quote'), $feedNames));

		// Get the published feed counts.
		$query = $db->getQuery(true);
		$query->select('p.feedname, COUNT(DISTINCT p.id) AS count_published');
		$query->from($db->quoteName('#__podcastmanager', 'p'));
		$query->where($db->quoteName('p.published') . ' = 1');
		$query->where($db->quoteName('p.feedname') . ' IN (' . $feedNames . ')');
		$query->group('p.feedname');
		$db->setQuery($query);
		$countPublished = $db->loadAssocList('feedname', 'count_published');

		if ($db->getErrorNum())
		{
			$this->setError($db->getErrorMsg());

			return false;
		}

		// Get the unpublished feed counts.
		$query->clear('where');
		$query->where($db->quoteName('p.published') . ' = 0');
		$query->where($db->quoteName('p.feedname') . ' IN (' . $feedNames . ')');
		$db->setQuery($query);
		$countUnpublished = $db->loadAssocList('feedname', 'count_published');

		if ($db->getErrorNum())
		{
			$this->setError($db->getErrorMsg());

			return false;
		}

		// Get the trashed feed counts.
		$query->clear('where');
		$query->where($db->quoteName('p.published') . ' = -2');
		$query->where($db->quoteName('p.feedname') . ' IN (' . $feedNames . ')');
		$db->setQuery($query);
		$countTrashed = $db->loadAssocList('feedname', 'count_published');

		if ($db->getErrorNum())
		{
			$this->setError($db->getErrorMsg());

			return false;
		}

		// Inject the values back into the array.
		foreach ($items as $item)
		{
			$item->count_published = isset($countPublished[$item->id]) ? $countPublished[$item->id] : 0;
			$item->count_unpublished = isset($countUnpublished[$item->id]) ? $countUnpublished[$item->id] : 0;
			$item->count_trashed = isset($countTrashed[$item->id]) ? $countTrashed[$item->id] : 0;
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery  A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   1.7
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the needed fields from the table.
		$query->select($this->getState('list.select', 'a.id, a.name, a.alias, a.published, a.language, a.checked_out, a.created_by'));
		$query->from($db->quoteName('#__podcastmanager_feeds', 'a'));

		// Join over the language
		$query->select($db->quoteName('l.title', 'language_title'));
		$query->join('LEFT', $db->quoteName('#__languages', 'l') . ' ON l.lang_code = a.language');

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where($db->quoteName('a.published') . ' = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(' . $db->quoteName('a.published') . ' IN (0, 1))');
		}

		// Filter on the language.
		$language = $this->getState('filter.language');

		if (!empty($language))
		{
			$query->where($db->quoteName('a.language') . ' = ' . $db->quote($language));
		}

		// Handle the list ordering.
		$ordering = $this->getState('list.ordering');
		$direction = $this->getState('list.direction');

		if (!empty($ordering))
		{
			$query->order($db->escape($ordering) . ' ' . $db->escape($direction));
		}

		$query->group('a.id, a.name, a.published, a.language, a.checked_out, a.created_by, l.title');

		return $query;
	}

	/**
	 * Method to auto-populate the model state.  Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction.
	 *
	 * @return  void
	 *
	 * @since   1.7
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Load the filter state.
		$published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.published', $published);

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_podcastmanager');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'asc');
	}
}
