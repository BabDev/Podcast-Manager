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
	function save($data)
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Initialise variables
		$row	= JTable::getInstance('podcastmanager', 'Table');
		
		$post	= JRequest::get('post');
		
		if(!$row->bind($post)) {
			JError::raiseError(500, $row->getError());
		}
		
		if(!$row->podcast_id) // undefined or empty string or 0
			$row->podcast_id = null; // new podcast: let auto_increment take care of it

		if(!$row->store()) {
			JError::raiseError(500, $row->getError());
		}
		
		$this->setRedirect('index.php?option=com_podcastmanager', JText::_('Metadata Saved.'));

		// clear cache
		$cache =& JFactory::getCache('com_podcastmanager', 'output');
		$cache->clean('com_podcastmanager');
		
		return $row;
	}
}
