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

		// Self join to find the number of published menu items in the menu.
		$query->select('COUNT(DISTINCT p1.id) AS count_published');
		$query->join('LEFT', '`#__podcastmanager` AS p1 ON p1.feedname = a.id AND p1.published = 1');

		// Self join to find the number of unpublished menu items in the menu.
		$query->select('COUNT(DISTINCT p2.id) AS count_unpublished');
		$query->join('LEFT', '`#__podcastmanager` AS p2 ON p2.feedname = a.id AND p2.published = 0');

		// Self join to find the number of trashed menu items in the menu.
		$query->select('COUNT(DISTINCT p3.id) AS count_trashed');
		$query->join('LEFT', '`#__podcastmanager` AS p3 ON p3.feedname = a.id AND p3.published = -2');

		$query->group('a.id');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.id')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));

		//echo nl2br(str_replace('#__','jos_',(string)$query)).'<hr/>';
		return $query;
	}

	/**
	 * Method to auto-populate the model state.  Calling getState in this method will result in recursion.
	 *
	 * @return	void
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
