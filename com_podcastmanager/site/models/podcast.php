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

jimport('joomla.application.component.modelitem');

class PodcastManagerModelPodcast extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_podcastmanager.podcast';

	/**
	 * Method to check out a podcast for editing.
	 *
	 * @param	integer		The id of the row to check out.
	 *
	 * @return	boolean		True on success, false on failure.
	 * @since	1.8
	 */
	public function checkout($podcastId = null)
	{
		// Get the podcast id.
		$podcastId = (!empty($podcastId)) ? $podcastId : (int)$this->getState('podcast.id');

		if ($podcastId) {
			// Initialise the table.
			$table = JTable::getInstance('Podcast', 'PodcastManagerTable');

			// Attempt to check the row out.
			if (!$table->checkout($podcastId)) {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the return URL.
	 *
	 * @return	string	The return URL.
	 * @since	1.8
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}

	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.8
	 */
	public function &getItem($id = null)
	{
		if ($this->_item === null) {
			$this->_item = false;

			if (empty($id)) {
				$id = $this->getState('podcast.id');
			}

			// Get a level row instance.
			$table = JTable::getInstance('Podcast', 'PodcastManagerTable');

			// Attempt to load the row.
			if ($table->load($id)) {
				// Check published state.
				if ($published = $this->getState('filter.published')) {
					if ($table->published != $published) {
						return $this->_item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');
			} else if ($error = $table->getError()) {
				$this->setError($error);
			}
		}

		return $this->_item;
	}

	/**
	 * Returns a JTable object, always creating it
	 *
	 * @param	string	$type	The table type to instantiate
	 * @param	string	$prefix	A prefix for the table class name. Optional.
	 * @param	array	$config	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 * @since	1.8
	*/
	public function getTable($type = 'Podcast', $prefix = 'PodcastManagerTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.8
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();

		// Load state from the request.
		$pk = JRequest::getInt('p_id');
		$this->setState('podcast.id', $pk);

		$feedId	= JRequest::getInt('feedname');
		$this->setState('podcast.feedname', $categoryId);

		$return = JRequest::getVar('return', null, 'default', 'base64');

		if (!JUri::isInternal(base64_decode($return))) {
			$return = null;
		}

		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', JRequest::getCmd('layout'));
	}
}
