<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class PodcastManagerModelFeeds extends JModelList
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
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
		$query->join('LEFT', '`#__podcastmanager` AS p1 ON p1.feedname = a.feedname AND p1.published = 1');

		// Self join to find the number of unpublished menu items in the menu.
		$query->select('COUNT(DISTINCT p2.id) AS count_unpublished');
		$query->join('LEFT', '`#__podcastmanager` AS p2 ON p2.feedname = a.feedname AND p2.published = 0');

		// Self join to find the number of trashed menu items in the menu.
		$query->select('COUNT(DISTINCT p3.id) AS count_trashed');
		$query->join('LEFT', '`#__podcastmanager` AS p3 ON p3.feedname = a.feedname AND p3.published = -2');

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
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// List state information.
		parent::populateState('a.id', 'asc');
	}
}
