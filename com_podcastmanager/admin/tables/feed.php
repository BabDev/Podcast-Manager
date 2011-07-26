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

/**
 * Feed table interaction class.
 *
 * @package		Podcast Manager
 * @subpackage	com_podcastmanager
 * @since		1.7
 */
class PodcastManagerTableFeed extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__podcastmanager_feeds', 'id', $db);
	}

	/**
	 * Overriden JTable::store to set modified data and user id.
	 *
	 * @param	boolean	$updateNulls	True to update fields even if they are null.
	 *
	 * @return	boolean	True on success.
	 * @since	1.7
	 */
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();

		if ($this->id) {
			// Existing item
			$this->modified		= $date->toMySQL();
			$this->modified_by	= $user->get('id');
		} else {
			// New item. A podcast's created field can be set by the user,
			// so we don't touch it if it is set.
			if (!intval($this->created)) {
				$this->created = $date->toMySQL();
			}
		}
		return parent::store($updateNulls);
	}
}
