<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Access check.
$input = JFactory::getApplication()->input;
$user = JFactory::getUser();
$asset = $input->getInt('asset', '');
$author = $input->getInt('author', '');

if (!$user->authorise('core.manage', 'com_podcastmanager')
	&& (!$asset or (!$user->authorise('core.edit', $asset)
	&& !$user->authorise('core.create', $asset)
	&& count($user->getAuthorisedCategories($asset, 'core.create')) == 0)
	&& !($user->id == $author && $user->authorise('core.edit.own', $asset))))
{
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

$podmedparams = JComponentHelper::getParams('com_podcastmedia');

// Load the admin helper
JLoader::register('PodcastMediaHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/podcastmedia.php');

// Set the path definitions
$popup_upload = $input->getCmd('pop_up', null);
$path         = 'file_path';

$view = $input->getCmd('view', '');

define('COM_PODCASTMEDIA_BASE', JPATH_ROOT . '/' . $podmedparams->get($path, 'media/com_podcastmanager'));
define('COM_PODCASTMEDIA_BASEURL', JUri::root() . $podmedparams->get($path, 'media/com_podcastmanager'));

$controller = JControllerLegacy::getInstance('PodcastMedia', ['base_path' => JPATH_COMPONENT_ADMINISTRATOR]);
$controller->execute($input->getCmd('task', ''));
$controller->redirect();
