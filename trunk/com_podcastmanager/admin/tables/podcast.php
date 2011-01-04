<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class TablePodcast extends JTable {
	
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
		parent::__construct( '#__podcast', 'podcast_id', $db );
	}
}
