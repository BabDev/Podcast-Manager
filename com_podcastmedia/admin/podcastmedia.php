<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// no direct access
defined('_JEXEC') or die;

// Access check.
$user	= JFactory::getUser();
$asset	= JRequest::getCmd('asset');
$author	= JRequest::getCmd('author');
 
if (!$user->authorise('core.manage', 'com_podcastmanager')
	&&	(!$asset or (
			!$user->authorise('core.edit', $asset)
		&&	!$user->authorise('core.create', $asset) 
		&& 	count($user->getAuthorisedCategories($asset, 'core.create')) == 0)
		&&	!($user->id==$author && $user->authorise('core.edit.own', $asset))))
{
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

$podmedparams	= JComponentHelper::getParams('com_podcastmedia');

// Load the admin HTML view
require_once JPATH_COMPONENT.'/helpers/podcastmedia.php';

// Set the path definitions
$popup_upload = JRequest::getCmd('pop_up',null);
$path = "file_path";

$view = JRequest::getCmd('view');

define('COM_PODCASTMEDIA_BASE',		JPATH_ROOT.'/'.$podmedparams->get($path, 'media/com_podcastmanager'));
define('COM_PODCASTMEDIA_BASEURL',	JURI::root().$podmedparams->get($path, 'media/com_podcastmanager'));

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JController::getInstance('PodcastMedia');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
