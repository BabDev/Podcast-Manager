<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* @package		PodcastManager
* @subpackage	com_podcastmanager
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');

/**
 * Icon helper class.
 *
 * @package		PodcastManager
 * @subpackage	com_podcastmanager
 * @since		1.8
 */
class JHtmlIcon
{
	/**
	 * Method to create an edit icon for a feed
	 *
	 * @param	object	$feed		The feed object
	 * @param	object	$params		The item parameters
	 * @param	array	$attribs	Optional attributes for the link
	 *
	 * @return	object	$output		The formatted HTML for the edit icon
	 * @since	1.8
	 */
	static function feedEdit($feed, $params, $attribs = array())
	{
		$user = JFactory::getUser();
		$uri = JFactory::getURI();

		if ($params && $params->get('popup')) {
			return;
		}

		if ($feed->published < 0) {
			return;
		}

		JHtml::_('behavior.tooltip');
		$url	= PodcastManagerHelperRoute::getFeedEditRoute($feed->id, base64_encode($uri));
		$icon	= $feed->published ? 'edit.png' : 'edit_unpublished.png';
		$text	= JHtml::_('image','system/'.$icon, JText::_('JGLOBAL_EDIT'), NULL, true);

		if ($feed->published == 0) {
			$overlib = JText::_('JUNPUBLISHED');
		}
		else {
			$overlib = JText::_('JPUBLISHED');
		}

		$date	= JHtml::_('date', $feed->created);
		$author	= $feed->author;

		$overlib	.= '&lt;br /&gt;';
		$overlib	.= $date;
		$overlib	.= '&lt;br /&gt;';
		$overlib	.= htmlspecialchars($author, ENT_COMPAT, 'UTF-8');

		$button	= JHtml::_('link', JRoute::_($url), $text);

		$output	= '<span class="hasTip" title="'.JText::_('JGLOBAL_EDIT').' :: '.$overlib.'">'.$button.'</span>';

		return $output;
	}

	 /**
	 * Method to create an edit icon for a podcast
	 *
	 * @param	object	$podcast	The podcast object
	 * @param	object	$params		The item parameters
	 * @param	array	$attribs	Optional attributes for the link
	 *
	 * @return	object	$output		The formatted HTML for the edit icon
	 * @since	1.8
	 */
	static function podcastEdit($podcast, $params, $attribs = array())
	{
		$user = JFactory::getUser();
		$uri = JFactory::getURI();

		if ($params && $params->get('popup')) {
			return;
		}

		if ($podcast->published < 0) {
			return;
		}

		JHtml::_('behavior.tooltip');
		$url	= PodcastManagerHelperRoute::getPodcastEditRoute($podcast->id, base64_encode($uri));
		$icon	= $podcast->published ? 'edit.png' : 'edit_unpublished.png';
		$text	= JHtml::_('image','system/'.$icon, JText::_('JGLOBAL_EDIT'), NULL, true);

		if ($podcast->published == 0) {
			$overlib = JText::_('JUNPUBLISHED');
		}
		else {
			$overlib = JText::_('JPUBLISHED');
		}

		$date	= JHtml::_('date', $podcast->created);
		$author	= $podcast->itAuthor;

		$overlib	.= '&lt;br /&gt;';
		$overlib	.= $date;
		$overlib	.= '&lt;br /&gt;';
		$overlib	.= htmlspecialchars($author, ENT_COMPAT, 'UTF-8');

		$button	= JHtml::_('link', JRoute::_($url), $text);

		$output	= '<span class="hasTip" title="'.JText::_('JGLOBAL_EDIT').' :: '.$overlib.'">'.$button.'</span>';

		return $output;
	}
}
