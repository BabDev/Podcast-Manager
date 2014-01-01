<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2014 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Podcast management model class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.6
 */
class PodcastManagerModelPodcasts extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'feedname', 'a.feedname',
				'published', 'a.published',
				'created', 'a.created',
				'language', 'a.language'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery  A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from($db->quoteName('#__podcastmanager', 'a'));

		// Join over the language
		$query->select($db->quoteName('l.title', 'language_title'));
		$query->join('LEFT', $db->quoteName('#__languages', 'l') . ' ON l.lang_code = a.language');

		// Join over the feed name
		$query->select($db->quoteName('f.name', 'feed_name'));
		$query->join('LEFT', $db->quoteName('#__podcastmanager_feeds', 'f') . ' ON f.id = a.feedname');

		// Join over the users for the checked out user.
		$query->select($db->quoteName('uc.name', 'editor'));
		$query->join('LEFT', $db->quoteName('#__users', 'uc') . ' ON uc.id=a.checked_out');

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

		// Filter by feed ID
		$feedname = $this->getState('filter.feedname');

		if (is_numeric($feedname))
		{
			$query->where($db->quoteName('a.feedname') . ' = ' . (int) $feedname);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(' . $db->quoteName('a.title') . ' LIKE ' . $search . ')');
			}
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

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.feedname');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.  Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$input = JFactory::getApplication('administrator')->input;

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.published', $published);

		$feedname = $input->get('feedname', '', 'var');

		if ($feedname)
		{
			if ($feedname != $this->getUserStateFromRequest($this->context . '.filter.feedname', 'filter_feedname', ''))
			{
				$this->setState($this->context . '.filter.feedname', $feedname);
			}
		}
		else
		{
			$feedname = $this->getUserStateFromRequest($this->context . '.filter.feedname', 'filter_feedname', '');
		}

		$this->setState('filter.feedname', $feedname);

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_podcastmanager');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.created', 'desc');
	}
}
