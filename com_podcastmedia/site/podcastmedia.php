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

$podmedparams = JComponentHelper::getParams('com_podcastmedia');
// Make sure the user is authorized to view this page
$user = JFactory::getUser();
$asset = JRequest::getCmd('asset');
$author = JRequest::getCmd('author');
if (!$asset or
		!$user->authorise('core.edit', $asset)
	&&	!$user->authorise('core.create', $asset)
	&& 	count($user->getAuthorisedCategories($asset, 'core.create')) == 0
	&&	!($user->id==$author && $user->authorise('core.edit.own', $asset)))
{
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Set the path definitions
define('COM_PODCASTMEDIA_BASE',		JPATH_ROOT.'/'.$podmedparams->get('file_path', 'media/com_podcastmanager'));
define('COM_PODCASTMEDIA_BASEURL',	JURI::root().$podmedparams->get('file_path', 'media/com_podcastmanager'));

$lang = JFactory::getLanguage();
	$lang->load($option, JPATH_ADMINISTRATOR, null, false, false)
||	$lang->load($option, JPATH_COMPONENT_ADMINISTRATOR, null, false, false)
||	$lang->load($option, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
||	$lang->load($option, JPATH_COMPONENT_ADMINISTRATOR, $lang->getDefault(), false, false);

// Load the admin HTML view
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/podcastmedia.php';

// Require the base controller
require_once JPATH_COMPONENT.'/controller.php';

// Make sure the user is authorized to view this page
$user	= JFactory::getUser();
$app	= JFactory::getApplication();
$cmd	= JRequest::getCmd('task', null);

if (strpos($cmd, '.') != false) {
	// We have a defined controller/task pair -- lets split them out
	list($controllerName, $task) = explode('.', $cmd);

	// Define the controller name and path
	$controllerName	= strtolower($controllerName);
	$controllerPath	= JPATH_COMPONENT_ADMINISTRATOR.'/controllers/'.$controllerName.'.php';

	// If the controller file path exists, include it ... else lets die with a 500 error
	if (file_exists($controllerPath)) {
		require_once $controllerPath;
	}
	else {
		JError::raiseError(500, JText::_('JERROR_INVALID_CONTROLLER'));
	}
}
else {
	// Base controller, just set the task :)
	$controllerName = null;
	$task = $cmd;
}

// Set the name for the controller and instantiate it
$controllerClass = 'PodcastMediaController'.ucfirst($controllerName);

if (class_exists($controllerClass)) {
	$controller = new $controllerClass();
}
else {
	JError::raiseError(500, JText::_('JERROR_INVALID_CONTROLLER_CLASS'));
}

// Set the model and view paths to the administrator folders
$controller->addViewPath(JPATH_COMPONENT_ADMINISTRATOR.'/views');
$controller->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.'/models');

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
