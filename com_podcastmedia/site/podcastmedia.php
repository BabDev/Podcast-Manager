<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Load the com_podcastmedia language file
$lang = JFactory::getLanguage();
$lang->load('com_podcastmedia', JPATH_COMPONENT_ADMINISTRATOR, null, false, false)
||	$lang->load('com_podcastmedia', JPATH_COMPONENT_ADMINISTRATOR, $lang->getDefault(), false, false);

// Hand processing over to the admin base file
require_once JPATH_COMPONENT_ADMINISTRATOR . '/podcastmedia.php';
