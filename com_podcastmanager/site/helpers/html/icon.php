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

		JHtml::_('bootstrap.tooltip');

		// Show checked_out icon if the feed is checked out by a different user
		if (property_exists($feed, 'checked_out') && property_exists($feed, 'checked_out_time') && $feed->checked_out > 0
			&& $feed->checked_out != JFactory::getUser()->id)
		{
			$date    = JHtml::_('date', $podcast->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT') . ' :: '
				. JText::sprintf('COM_PODCASTMANAGER_CHECKED_OUT_BY', JFactory::getUser($article->checked_out)->name)
				. ' <br /> ' . $date;

			$text = '<span class="hasTooltip icon-lock" title="' . JHtml::_('tooltipText', $tooltip . '', 0) . '"></span> '
				. JText::_('JLIB_HTML_CHECKED_OUT');

			return JHtml::_('link', '#', $text);
		}

		$url = PodcastManagerHelperRoute::getFeedEditRoute($feed->id, base64_encode($uri));

		if ($feed->published == 0)
		{
			$overlib = JText::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = JText::_('JPUBLISHED');
		}

		$date   = JHtml::_('date', $feed->created);
		$author = $feed->author;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= htmlspecialchars($author, ENT_COMPAT, 'UTF-8');

		$icon = $feed->published ? 'edit' : 'eye-close';

		$text = '<span class="hasTooltip icon-' . $icon . ' tip" title="'
			. JHtml::_('tooltipText', JText::_('COM_PODCASTMANAGER_EDIT_FEED'), $overlib, 0, 0) . '"></span>' . JText::_('JGLOBAL_EDIT');

		return JHtml::_('link', JRoute::_($url), $text);
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

		JHtml::_('bootstrap.tooltip');

		// Show checked_out icon if the feed is checked out by a different user
		if (property_exists($podcast, 'checked_out') && property_exists($podcast, 'checked_out_time') && $podcast->checked_out > 0
			&& $podcast->checked_out != JFactory::getUser()->id)
		{
			$date    = JHtml::_('date', $podcast->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT') . ' :: '
				. JText::sprintf('COM_PODCASTMANAGER_CHECKED_OUT_BY', JFactory::getUser($article->checked_out)->name)
				. ' <br /> ' . $date;

			$text = '<span class="hasTooltip icon-lock" title="' . JHtml::_('tooltipText', $tooltip . '', 0) . '"></span> '
				. JText::_('JLIB_HTML_CHECKED_OUT');

			return JHtml::_('link', '#', $text);
		}

		$url = PodcastManagerHelperRoute::getPodcastEditRoute($podcast->id, base64_encode($uri));

		if ($podcast->published == 0)
		{
			$overlib = JText::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = JText::_('JPUBLISHED');
		}

		$date   = JHtml::_('date', $podcast->created);
		$author = $podcast->itAuthor;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= htmlspecialchars($author, ENT_COMPAT, 'UTF-8');

		$icon = $podcast->published ? 'edit' : 'eye-close';

		$text = '<span class="hasTooltip icon-' . $icon . ' tip" title="'
			. JHtml::_('tooltipText', JText::_('COM_PODCASTMANAGER_EDIT_PODCAST'), $overlib, 0, 0) . '"></span>' . JText::_('JGLOBAL_EDIT');

		return JHtml::_('link', JRoute::_($url), $text);
	}
}
