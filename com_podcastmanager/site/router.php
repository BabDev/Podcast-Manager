<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

/**
 * Function to build the route
 *
 * @param   array  &$query  An array of URL arguments
 *
 * @return  array  An array of the route segments
 *
 * @since   1.6
 */
function podcastManagerBuildRoute(&$query)
{
	$segments = array();

	// Get a menu item based on Itemid or currently active
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();

	if (empty($query['Itemid']))
	{
		$menuItem = $menu->getActive();
	}
	else
	{
		$menuItem = $menu->getItem($query['Itemid']);
	}
	$mView	= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
	$mId	= (empty($menuItem->query['feedname'])) ? null : $menuItem->query['feedname'];

	if (isset($query['view']))
	{
		$view = $query['view'];
		if (empty($query['Itemid']))
		{
			$segments[] = $query['view'];
		}
		unset($query['view']);
	}

	// Are we dealing with a podcast feed that is attached to a menu item?
	if (isset($query['view']) && ($mView == $query['view']) && (isset($query['feedname'])) && ($mId == intval($query['feedname'])))
	{
		unset($query['view']);
		unset($query['feedname']);
		return $segments;
	}

	if (isset($view) and ($view == 'feed'))
	{
		if ($mId != intval($query['feedname']) || $mView != $view)
		{
			if ($view == 'feed')
			{
				$segments[] = $query['feedname'];
			}
		}
		unset($query['feedname']);
	}

	if (isset($query['layout']))
	{
		if (!empty($query['Itemid']) && isset($menuItem->query['layout']))
		{
			if ($query['layout'] == $menuItem->query['layout'])
			{
				unset($query['layout']);
			}
		}
		else
		{
			if ($query['layout'] == 'feed')
			{
				unset($query['layout']);
			}
		}
	};

	return $segments;
}

/**
 * Function to parse the route
 *
 * @param   array  $segments  An array of segments
 *
 * @return  array  An array of variables
 *
 * @since   1.6
 */
function podcastManagerParseRoute($segments)
{
	$input = JFactory::getApplication()->input;
	$vars = array();

	// Get the active menu item.
	$app  = JFactory::getApplication();
	$menu = $app->getMenu();
	$item = $menu->getActive();

	// Count route segments
	$count = count($segments);

	// Standard routing for the feed views.
	if (!isset($item))
	{
		$vars['view']     = $segments[0];
		$vars['feedname'] = $segments[$count - 1];
		return $vars;
	}

	$vars['view']     = $input->get('view', '', 'cmd');
	$vars['feedname'] = $input->get('feedname', '', 'int');

	return $vars;
}
