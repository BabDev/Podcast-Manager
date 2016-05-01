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

use Joomla\Utilities\ArrayHelper;

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
	 * @see     JControllerLegacy
	 */
	public function __construct($config = [])
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = [
				'id', 'a.id',
				'name', 'a.name',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'published', 'a.published',
				'language', 'a.language'
			];
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
			return [];
		}

		// Getting the following metric by joins is WAY TOO SLOW.

		// Get the feeds in the list.
		$db        = $this->getDbo();
		$feedNames = ArrayHelper::getColumn($items, 'id');

		// Quote the strings.
		$feedNames = implode(',', array_map([$db, 'quote'], $feedNames));

		// Get the published feed counts.
		$query = $db->getQuery(true)
			->select('p.feedname, COUNT(DISTINCT p.id) AS count_published')
			->from($db->quoteName('#__podcastmanager', 'p'))
			->where($db->quoteName('p.published') . ' = 1')
			->where($db->quoteName('p.feedname') . ' IN (' . $feedNames . ')')
			->group('p.feedname');

		try
		{
			$countPublished = $db->setQuery($query)->loadAssocList('feedname', 'count_published');
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Get the unpublished feed counts.
		$query->clear('where')
			->where($db->quoteName('p.published') . ' = 0')
			->where($db->quoteName('p.feedname') . ' IN (' . $feedNames . ')');

		try
		{
			$countUnpublished = $db->setQuery($query)->loadAssocList('feedname', 'count_published');
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Get the trashed feed counts.
		$query->clear('where')
			->where($db->quoteName('p.published') . ' = -2')
			->where($db->quoteName('p.feedname') . ' IN (' . $feedNames . ')');

		try
		{
			$countTrashed = $db->setQuery($query)->loadAssocList('feedname', 'count_published');
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Inject the values back into the array.
		foreach ($items as $item)
		{
			$item->count_published   = isset($countPublished[$item->id]) ? $countPublished[$item->id] : 0;
			$item->count_unpublished = isset($countUnpublished[$item->id]) ? $countUnpublished[$item->id] : 0;
			$item->count_trashed     = isset($countTrashed[$item->id]) ? $countTrashed[$item->id] : 0;
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
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the needed fields from the table.
		$query->select($this->getState('list.select', 'a.id, a.name, a.alias, a.published, a.language, a.checked_out, a.checked_out_time, a.created_by'));
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

		// Filter by search in name
		if ($search = trim($this->getState('filter.search')))
		{
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
			$query->where('(a.name LIKE ' . $search . ')');
		}

		// Handle the list ordering.
		$ordering  = $this->getState('list.ordering');
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
		$this->setState('filter.published', $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string'));

		$this->setState('filter.language', $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', ''));

		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.search', 'filter_search'));

		// Load the parameters.
		$this->setState('params', JComponentHelper::getParams('com_podcastmanager'));

		// List state information.
		parent::populateState('a.id', 'asc');
	}
}
