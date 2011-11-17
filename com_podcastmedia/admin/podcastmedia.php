<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Access check.
$input = JFactory::getApplication()->input;
$user = JFactory::getUser();
$asset = $input->get('asset', '', 'cmd');
$author = $input->get('author', '', 'cmd');

if (!$user->authorise('core.manage', 'com_podcastmanager')
	&& (!$asset or (!$user->authorise('core.edit', $asset)
	&& !$user->authorise('core.create', $asset)
	&& count($user->getAuthorisedCategories($asset, 'core.create')) == 0)
	&& !($user->id == $author && $user->authorise('core.edit.own', $asset))))
{
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

$podmedparams = JComponentHelper::getParams('com_podcastmedia');

// Load the admin HTML view
require_once JPATH_COMPONENT . '/helpers/podcastmedia.php';

// Set the path definitions
$popup_upload = $input->get('pop_up', null, 'cmd');
//$popup_upload = JRequest::getCmd('pop_up', null);
$path = "file_path";

$view = $input->get('view', '', 'cmd');
//$view = JRequest::getCmd('view');

define('COM_PODCASTMEDIA_BASE', JPATH_ROOT . '/' . $podmedparams->get($path, 'media/com_podcastmanager'));
define('COM_PODCASTMEDIA_BASEURL', JURI::root() . $podmedparams->get($path, 'media/com_podcastmanager'));

// Include dependancies
jimport('joomla.application.component.controller');

$controller = JController::getInstance('PodcastMedia');
$controller->execute($input->get('task', '', 'cmd'));
//$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
