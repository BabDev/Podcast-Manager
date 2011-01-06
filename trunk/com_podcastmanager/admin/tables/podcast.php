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

class PodcastManagerTable extends JTable {
	
	var $podcast_id;
	var $filename;
	var $itAuthor;
	var $itBlock;
	var $itCategory;
	var $itDuration;
	var $itExplicit;
	var $itKeywords;
	var $itSubtitle;
	
	function __construct( &$db )
	{
		parent::__construct( '#__podcastmanager', 'podcast_id', $db );
	}
}
