<?php
/**
 * Podcast Manager for Joomla!
 *
 * @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 */

// Restricted access
defined('_JEXEC') or die();

class PodcastManagerTablePodcast extends JTable
{
	var $id;
	var $filename;
	var $title;
	var $published;
	var $created;
	var $modified;
	var $modified_by;
	var $checked_out;
	var $checked_out_time;
	var $publish_up;
	var $itAuthor;
	var $itBlock;
	var $itCategory;
	var $itDuration;
	var $itExplicit;
	var $itKeywords;
	var $itSubtitle;
	var $itSummary;
	var $language;

	function __construct(&$db)
	{
		parent::__construct('#__podcastmanager', 'id', $db);
	}

	/**
	 * Overriden JTable::store to set modified data and user id.
	 *
	 * @param	boolean	True to update fields even if they are null.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
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
