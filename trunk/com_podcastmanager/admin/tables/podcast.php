<?php
/**
* Podcast Manager for Joomla!
*
* @version		$Id$
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

class PodcastManagerTablePodcast extends JTable {
	
	var $id;
	var $filename;
	var $title;
	var $published;
	var $itAuthor;
	var $itBlock;
	var $itCategory;
	var $itDuration;
	var $itExplicit;
	var $itKeywords;
	var $itSubtitle;
	var $language;
	
	function __construct( &$db )
	{
		parent::__construct( '#__podcastmanager', 'id', $db );
	}
}
