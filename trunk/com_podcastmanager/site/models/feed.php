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

jimport('joomla.application.component.model');

class PodcastManagerModelFeed extends JModel
{
	private $data = array();
	
	public function &getData()
	{	
		if (empty($this->data)) {
			$this->data['content'] =& $this->getKeyedContent();
			$this->data['metadata'] =& $this->getMetaData();
		}
		
		return $this->data;
	}
	
	private function &getMetaData()
	{
		$metadata = array();
		
		if (isset($this->data['content'])) {
			$metaList = $this->_getList("SELECT * FROM #__podcastmanager");
			
			foreach ($metaList as &$row) {
				$metadata[$row->filename] =& $row;
			}
		}
		
		return $metadata;
	}
	
	/*
	 * Gets content and puts rows in an array keyed by filename
	 * 
	 */
	private function &getKeyedContent()
	{
		$content = array();
		
		$query = $this->buildQuery();
		$articles = $this->_getList($query);
		
		foreach ($articles as &$row) {
			preg_match('/\{podcast\s(.*)\}/', $row->introtext, $matches);
			
			$pieces = explode(' ', $matches[1]);
			
			$content[$pieces[0]] =& $row;
		}
		
		return $content;
	}
	
	private function buildQuery()
	{
		$date =& JFactory::getDate();
		$now = $date->toMySQL();
		
		$params =& JComponentHelper::getParams('com_podcastmanager');
		
		$category_id = $params->get('category_id', 0);
		$count = $params->get('count', 5);
		
		$nullDate = $this->_db->Quote($this->_db->getNullDate());
		
		$query = "SELECT * FROM #__content"
		. "\n WHERE state = '1' AND introtext LIKE '%{podcast%}%'"
		. "\n AND access = 0"
		. "\n AND ( publish_up = {$nullDate} OR publish_up <= '" . $now . "' )"
		. "\n AND ( publish_down = {$nullDate} OR publish_down >= '". $now ."' )";
		
		if ($category_id != 0) {
			$query .= "\n AND catid = '{$category_id}'";
		}
		
		$query .= "\n ORDER BY publish_up DESC LIMIT {$count}";
		
		return $query;
	}
}
