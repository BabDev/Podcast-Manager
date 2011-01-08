<?php 
/**
* Podcast Manager for Joomla!
*
* @version		$Id$
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

jimport( 'joomla.application.component.modeladmin' );

class PodcastManagerModelPodcast extends JModelAdmin {

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_podcastmanager.podcast', 'podcast', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'Podcast', $prefix = 'PodcastManagerTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	function save($data)
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Initialise variables
		$row	= JTable::getInstance('Podcast', 'PodcastManagerTable');
		
		if(!$row->bind($data)) {
			JError::raiseError(500, $row->getError());
		}
		
		if(!$row->podcast_id) // undefined or empty string or 0
			$row->podcast_id = null; // new podcast: let auto_increment take care of it

		if(!$row->store()) {
			JError::raiseError(500, $row->getError());
		}
		
		//TODO: Why isn't this loading proper method?
		$this->setRedirect('index.php?option=com_podcastmanager', JText::_('Metadata Saved.'));

		// clear cache
		$cache =& JFactory::getCache('com_podcastmanager');
		$cache->clean('com_podcastmanager');
		
		return $row;
	}
}
