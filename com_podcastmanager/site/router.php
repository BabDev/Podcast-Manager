<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011 Michael Babker. All rights reserved.
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

	if (isset($query['view']))
	{
		$segments[] = $query['view'];
		unset($query['view']);
	}

	if (isset($query['feedname']))
	{
		$segments[] = $query['feedname'];
		unset($query['feedname']);
	}

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

	$vars['view'] = $input->get('view', '', 'cmd');
	$vars['feedname'] = $input->get('feedname', '', 'int');

	return $vars;
}
