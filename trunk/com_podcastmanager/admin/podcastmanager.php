<?php 
/**
* Podcast Manager for Joomla!
*
* @version		$Id: podcastmanager.php 14 2011-01-05 23:26:28Z mbabker $
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_podcastmanager')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependencies
jimport('joomla.application.component.controller');

$controller = JController::getInstance('PodcastManager');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
