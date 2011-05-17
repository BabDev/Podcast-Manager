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

	$vars['view'] 		= 'feed';
	$vars['feedname']	= JRequest::getInt('feedname');

	return $vars;
}