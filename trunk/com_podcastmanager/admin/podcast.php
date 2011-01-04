<?php 
/*
	Podcast Suite
	(c) 2005 - 2008 Joseph L. LeBlanc
	Released under the GPLv2 License
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

JTable::addIncludePath( JPATH_COMPONENT.DS.'tables' );

class PodcastController extends JController
{
	function &save()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$row =& JTable::getInstance('podcast', 'Table');
		
		$post = JRequest::get('post');
		
		if(!$row->bind($post)) {
			JError::raiseError(500, $row->getError());
		}
		
		if(!$row->podcast_id) // undefined or empty string or 0
			$row->podcast_id = null; // new podcast: let auto_increment take care of it

		if(!$row->store()) {
			JError::raiseError(500, $row->getError());
		}
		
		$this->setRedirect('index.php?option=com_podcast', JText::_('Metadata Saved.'));

		// clear cache
		$cache =& JFactory::getCache('com_podcast', 'output');
		$cache->clean('com_podcast');
		
		return $row;
	}
	
	function publish()
	{
		$db =& JFactory::getDBO();
		
		// Save the metadata first, will override the redirect below
		$podcast =& $this->save();

		$query = 'SELECT id FROM #__content WHERE introtext LIKE \'%{enclose%' . $podcast->filename . '%}%\' AND state > \'-1\'';
		$db->setQuery($query);
		$id = $db->loadResult();
		
		$msg = JText::_('Metadata Saved.') . ' ' . JText::_('Add and publish your article with show notes below.');
		
		if ($id) {
			$this->setRedirect('index.php?option=com_content&task=edit&cid[]=' . $id, $msg);
		} else {
			$row =& $this->saveArticle($podcast);
			$this->setRedirect('index.php?option=com_content&task=edit&cid[]=' . $row->id, $msg);
		}
	}
	
	// This is a bootstrap function to get an article in the database
	// for the podcast episode. This should only be used for new
	// episiodes; existing ones should be loaded through the content
	// component.
	// 
	// This code was mostly copied/adjusted from the com_content component.
	private function &saveArticle(&$podcast)
	{
		$row =& JTable::getInstance('content');
		
		$row->title = JRequest::getString('title', '');
		$row->introtext = JRequest::getVar( 'text', '', 'post', 'string', JREQUEST_ALLOWRAW );
		
		$config =& JFactory::getConfig();
		$tzoffset = $config->getValue('config.offset');
		$date =& JFactory::getDate($row->created, $tzoffset);
		$row->created = $date->toMySQL();
		
		$date =& JFactory::getDate($row->publish_up, $tzoffset);
		$row->publish_up = $date->toMySQL();
		
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
		}
		
		// Store the content to the database
		if (!$row->store()) {
			JError::raiseError( 500, $db->stderr() );
		}
		
		return $row;
	}
	
	function display()
	{
		$view = JRequest::getVar('view', null);
		
		if (!$view) {
			$task = $this->getTask();
			
			if ($task == 'add' || $task == 'edit') {
				JRequest::setVar('view', 'podcast');
			} elseif ($task == 'info') {
				JRequest::setVar('view', 'info');
			} else {
				JRequest::setVar('view', 'files');
			}
		}
		
		parent::display();
	}
}

$controller = new PodcastController();
$controller->registerTask( 'edit', 'display');
$controller->registerTask( 'add' , 'display' );
$controller->execute(JRequest::getVar('task', null));
$controller->redirect();

