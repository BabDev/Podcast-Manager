<?php
/**
 * Podcast Manager for Joomla!
 *
 * @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 */

/**
 * @param	array
 * @return	array
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
 * @param	array
 * @return	array
 */
function PodcastManagerParseRoute($segments)
{
	$vars = array();

	$vars['view'] = 'feed';

	return $vars;
}