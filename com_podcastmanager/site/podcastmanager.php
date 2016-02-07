<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

JLoader::register('PodcastManagerHelperRoute', JPATH_COMPONENT . '/helpers/route.php');

$input = JFactory::getApplication()->input;

$controller = JControllerLegacy::getInstance('PodcastManager');
$controller->execute($input->get('task', '', 'cmd'));
$controller->redirect();
