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

/**
 * Routing class from com_podcastmanager
 *
 * @since  3.0
 */
class PodcastManagerRouter extends JComponentRouterBase
{
	/**
	 * Build the route for the com_mauticdownload component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   3.0
	 */
	public function build(&$query)
	{
		$segments = [];

		// Get a menu item based on Itemid or currently active
		$menu = JFactory::getApplication()->getMenu();

		if (empty($query['Itemid']))
		{
			$menuItem = $menu->getActive();
		}
		else
		{
			$menuItem = $menu->getItem($query['Itemid']);
		}

		$mView = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
		$mId   = (empty($menuItem->query['feedname'])) ? null : $menuItem->query['feedname'];

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
			if (isset($query['feedname']) && $mId != intval($query['feedname']) || $mView != $view)
			{
				if (isset($query['feedname']) && $view == 'feed')
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
		}

		$total = count($segments);

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   3.0
	 */
	public function parse(&$segments)
	{
		$total = count($segments);
		$vars  = [];

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}

		$app   = JFactory::getApplication();
		$input = $app->input;

		// Get the active menu item.
		$item = $app->getMenu()->getActive();

		// Count route segments
		$count = count($segments);

		// Standard routing for the feed views.
		if (!isset($item))
		{
			$vars['view']     = $segments[0];
			$vars['feedname'] = $segments[$count - 1];

			return $vars;
		}

		$vars['view']     = $input->getCmd('view', '');
		$vars['feedname'] = $input->getUint('feedname', '');

		return $vars;
	}
}
