<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

$controller	= JController::getInstance('PodcastManager');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
