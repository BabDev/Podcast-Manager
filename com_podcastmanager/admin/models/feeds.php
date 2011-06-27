<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class PodcastManagerModelFeeds extends JModelList
{
	/**
	 * The class constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.
	 *
	 * @return	void
	 * @since	1.7
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'published', 'a.published',
				'language', 'a.language',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	1.7
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId('getItems');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load the list items.
		$items = parent::getItems();

		// If emtpy or an error, just return.
		if (empty($items)) {
			return array();
		}

		// Getting the following metric by joins is WAY TOO SLOW.
		// Faster to do three queries for very large menu trees.

		// Get the menu types of menus in the list.
		$db = $this->getDbo();
		$feedNames = JArrayHelper::getColumn($items, 'id');

		// Quote the strings.
		$feedNames = implode(
			',',
			array_map(array($db, 'quote'), $feedNames)
		);

		// Get the published menu counts.
		$query = $db->getQuery(true)
			->select('p.feedname, COUNT(DISTINCT a.id) AS count_published')
			->from('#__podcastmanager AS p, #__podcastmanager_feeds AS a')
			->where('p.published = 1')
			->where('p.feedname IN ('.$feedNames.')')
			->group('p.feedname')
			;
		$db->setQuery($query);
		$countPublished = $db->loadAssocList('feedname', 'count_published');

		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Get the unpublished menu counts.
		$query->clear('where')
			->where('p.published = 0')
			->where('p.feedname IN ('.$feedNames.')')
			;
		$db->setQuery($query);
		$countUnpublished = $db->loadAssocList('feedname', 'count_unpublished');

		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Get the trashed menu counts.
		$query->clear('where')
			->where('p.published = -2')
			->where('p.feedname IN ('.$feedNames.')')
			;
		$db->setQuery($query);
		$countTrashed = $db->loadAssocList('feedname', 'count_trashed');

		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Inject the values back into the array.
		foreach ($items as $item) {
			$item->count_published		= isset($countPublished[$item->id]) ? $countPublished[$item->id] : 0;
			$item->count_unpublished	= isset($countUnpublished[$item->id]) ? $countUnpublished[$item->id] : 0;
			$item->count_trashed		= isset($countTrashed[$item->id]) ? $countTrashed[$item->id] : 0;
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	$query	An SQL query
	 * @since	1.7
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__podcastmanager_feeds` AS a');

		$query->group('a.id');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.id')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));

		return $query;
	}

	/**
	 * Method to auto-populate the model state.  Calling getState in this method will result in recursion.
	 *
	 * @param   string	$ordering	An optional ordering field.
	 * @param   string	$direction	An optional direction.
	 *
	 * @since	1.7
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// List state information.
		parent::populateState('a.id', 'asc');
	}
}
