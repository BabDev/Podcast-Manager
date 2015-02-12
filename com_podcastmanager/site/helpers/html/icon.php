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

use Joomla\Registry\Registry;

/**
 * Icon helper class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.8
 */
abstract class JHtmlIcon
{
	/**
	 * Method to create an edit icon for a feed
	 *
	 * @param   object    $feed    The feed object
	 * @param   Registry  $params  The item parameters
	 *
	 * @return  string|boolean  The formatted HTML for the edit icon
	 *
	 * @since   1.8
	 */
	public static function feedEdit($feed, $params)
	{
		$uri = JUri::getInstance();

		if ($params && $params->get('popup'))
		{
			return true;
		}

		if ($feed->published < 0)
		{
			return true;
		}

		JHtml::_('behavior.tooltip');
		$url = PodcastManagerHelperRoute::getFeedEditRoute($feed->id, base64_encode($uri));

		if ($feed->published == 0)
		{
			$overlib = JText::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = JText::_('JPUBLISHED');
		}

		$date = JHtml::_('date', $feed->created);
		$author = $feed->author;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= htmlspecialchars($author, ENT_COMPAT, 'UTF-8');

		$icon = $feed->published ? 'edit' : 'eye-close';
		$text = '<span class="hasTip icon-' . $icon . ' tip" title="' . JText::_('COM_PODCASTMANAGER_EDIT_FEED') . ' :: ' . $overlib . '"></span>&#160;' . JText::_('JGLOBAL_EDIT') . '&#160;';

		$button = JHtml::_('link', JRoute::_($url), $text);

		return '<span class="hasTip" title="' . JText::_('JGLOBAL_EDIT') . ' :: ' . $overlib . '">' . $button . '</span>';
	}

	/**
	 * Method to create an edit icon for a podcast
	 *
	 * @param   object    $podcast  The podcast object
	 * @param   Registry  $params   The item parameters
	 *
	 * @return  string|boolean  The formatted HTML for the edit icon
	 *
	 * @since   1.8
	 */
	public static function podcastEdit($podcast, $params)
	{
		$uri = JUri::getInstance();

		if ($params && $params->get('popup'))
		{
			return true;
		}

		if ($podcast->published < 0)
		{
			return true;
		}

		JHtml::_('behavior.tooltip');
		$url = PodcastManagerHelperRoute::getPodcastEditRoute($podcast->id, base64_encode($uri));

		if ($podcast->published == 0)
		{
			$overlib = JText::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = JText::_('JPUBLISHED');
		}

		$date = JHtml::_('date', $podcast->created);
		$author = $podcast->itAuthor;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= htmlspecialchars($author, ENT_COMPAT, 'UTF-8');

		$icon = $podcast->published ? 'edit' : 'eye-close';
		$text = '<span class="hasTip icon-' . $icon . ' tip" title="' . JText::_('COM_PODCASTMANAGER_EDIT_PODCAST') . ' :: ' . $overlib . '"></span>&#160;' . JText::_('JGLOBAL_EDIT') . '&#160;';

		$button = JHtml::_('link', JRoute::_($url), $text);

		return '<span class="hasTip" title="' . JText::_('JGLOBAL_EDIT') . ' :: ' . $overlib . '">' . $button . '</span>';
	}
}
