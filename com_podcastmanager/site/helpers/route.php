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

// Restricted access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

abstract class PodcastManagerHelperRoute
{
	protected static $lookup;

	/**
	 * @param	int	The route of the weblink
	 */
	public static function getPodcastRoute($id, $catid)
	{
		$needles = array(
			'podcast'  => array((int) $id)
		);

		//Create the link
		$link = 'index.php?option=com_podcastmanager&view=podcast&id='. $id;

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
		else if ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	/**
	 * @param	int		$id		The id of the podcast.
	 *
	 * @param	string	$return	The return page variable.
	 */
	public static function getFormRoute($id, $return = null)
	{
		// Create the link.
		if ($id) {
			$link = 'index.php?option=com_podcastmanager&task=podcast.edit&p_id='. $id;
		}
		else {
			$link = 'index.php?option=com_podcastmanager&task=podcast.add&p_id=0';
		}

		if ($return) {
			$link .= '&return='.$return;
		}

		return $link;
	}

	protected static function _findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null) {
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_podcastmanager');
			$items		= $menus->getItems('component_id', $component->id);
			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view'])) {
					$view = $item->query['view'];

					if (!isset(self::$lookup[$view])) {
						self::$lookup[$view] = array();
					}

					if (isset($item->query['id'])) {
						self::$lookup[$view][$item->query['id']] = $item->id;
					}
				}
			}
		}

		if ($needles) {
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view])) {
					foreach($ids as $id)
					{
						if (isset(self::$lookup[$view][(int)$id])) {
							return self::$lookup[$view][(int)$id];
						}
					}
				}
			}
		}
		else {
			$active = $menus->getActive();
			if ($active) {
				return $active->id;
			}
		}

		return null;
	}
}
