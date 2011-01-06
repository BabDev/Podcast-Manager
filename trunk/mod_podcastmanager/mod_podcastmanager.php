<?php
/**
* Podcast Manager for Joomla!
*
* @version		$Id: mod_podcastmanager.php 5 2011-01-04 03:06:30Z mbabker $
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

$params->def('text', '');
$params->def('urischeme', 'http');
$params->def('plainlink', 1);

$plainlink = $params->get('otherlink', '');
$img = $params->get('otherimage', '');

if(!$plainlink) {
	$plainlink = JRoute::_(JURI::root(false) . 'index.php?option=com_podcastmanager&view=feed&format=raw');
}

if($img) {
	$img = JHTML::_('image', $img, JText::_('MOD_PODCASTMANAGER_PODCASTFEED'));
} else {
	$img = JHTML::_('image', 'modules/mod_podcastmanager/media/images/podcast-mini2.png', JText::_('MOD_PODCASTMANAGER_PODCASTFEED'));
}

if($params->get('urischeme') == 'http') {
	$link = $plainlink;
} else {
	$link = str_replace(array('http:', 'https:'), $params->get('urischeme') . ':', $plainlink);
}

require(JModuleHelper::getLayoutPath('mod_podcastmanager'));
?>
