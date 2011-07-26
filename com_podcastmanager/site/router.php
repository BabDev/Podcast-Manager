<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

/**
 * Function to build the route
 *
 * @param	array	$query
 *
 * @return	array	$segments	An array of the route segments
 * @since	1.6
 */
function PodcastManagerBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view'])) {
		unset($query['view']);
	}

	return $segments;
}

/**
 * Function to parse the route
 *
 * @param	array	$segments	An array of segments
 *
 * @return	array	$vars		An array of variables
 * @since	1.6
 */
function PodcastManagerParseRoute($segments)
{
	$vars = array();

	$vars['view'] 		= JRequest::getCmd('view');
	$vars['feedname']	= JRequest::getInt('feedname');

	return $vars;
}