<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  mod_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Include the routing helper
JLoader::register('PodcastManagerHelperRoute', JPATH_SITE . '/components/com_podcastmanager/helpers/route.php');

$params->def('text', '');
$params->def('urischeme', 'http');
$params->def('plainlink', 1);

$plainlink  = $params->get('otherlink', '');
$feed       = $params->get('feedname', '');

if (!$plainlink)
{
	$plainlink = JRoute::_(PodcastManagerHelperRoute::getFeedRssRoute($feed), false, 2);
}

$img = JHtml::_('image', 'mod_podcastmanager/podcast-mini2.png', JText::_('MOD_PODCASTMANAGER_PODCASTFEED'), null, true);

if ($params->get('urischeme') == 'http')
{
	$link = $plainlink;
}
else
{
	$link = str_replace(array('http:', 'https:'), $params->get('urischeme') . ':', $plainlink);
}

require JModuleHelper::getLayoutPath('mod_podcastmanager', $params->get('layout', 'default'));
