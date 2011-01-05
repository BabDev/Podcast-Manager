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

jimport('joomla.application.component.controller');

class PodcastManagerController extends JController
{
	function display()
	{		
		$view = JRequest::getVar('view', '');
		
		if ($view == '') {
			JRequest::setVar('view', 'feed');
		}
		
		parent::display();
	}
}

$document =& JFactory::getDocument();
$document->setType('raw');

$controller = new PodcastManagerController();
$controller->execute(JRequest::getVar('task', null));
$controller->redirect();