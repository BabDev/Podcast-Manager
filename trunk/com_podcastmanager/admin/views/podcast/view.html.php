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

jimport('joomla.application.component.view');
jimport('joomla.filesystem.file');

class PodcastManagerViewPodcast extends JView
{
	/** Currently unused code from the end of the 1.5 display function
		$explicit = JHTML::_('select.booleanlist', 'itExplicit', '', $row->itExplicit);
		$block = JHTML::_('select.booleanlist', 'itBlock', '', $row->itBlock);
		
		$this->assign('text', '{enclose ' . $row->filename . '}');
		$this->assign('explicit', $explicit);
		$this->assign('block', $block);
		$this->assignRef('podcast', $row);
		$this->assignRef('title', $title);
	 */	
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		/**
		$app	= JFactory::getApplication();
		$cid	= JRequest::getVar('cid', array(0), '', 'array');
		$id		= (int)$cid[0];
		$row	= JTable::getInstance('Podcast', 'PodcastManagerTable');
		
		$filename = JRequest::getVar('filename', null, '', 'array');
		var_dump($filename);
		die();
		
		if (!$id || !$row->load($id)) { // metadata hasn't been added yet or the given id is invalid
			
			if(!$filename) { // this should never happen if user uses interface
				$app->redirect("index.php?option=com_podcastmanager", JText::_('Invalid ID or Filename'), 'error');
				return;
			}
			
			$row->filename = JFile::makeSafe($filename[0]);
			if($row->filename !== $filename[0]) { // either they're messing with us or the OS is allowing filenames that Joomla isn't
				$app->redirect("index.php?option=com_podcastmanager", JText::_('Filename Cannot Contain Special Characters'), 'error'); // either way, let's stay safe
				return;
			}

			$this->fillMetaID3($row, $title);
		}
		
		if (stristr($row->filename, ' ')) {
			$tpl = 'error';
		}
		*/
		
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->podcast_id == 0);
		$canDo		= PodcastManagerHelper::getActions();

		JToolBarHelper::title(JText::_('COM_PODCASTMANAGER_VIEW_PODCAST'.($isNew ? 'ADD_PODCAST' : 'EDIT_PODCAST')), '');

		// Built the actions for new and existing records.
		if ($isNew)  {
			// For new records, check the create permission.
			if ($canDo->get('core.create')) {
				JToolBarHelper::apply('podcast.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('podcast.save', 'COM_PODCASTMANAGER_TOOLBAR_SAVE');
			}

			JToolBarHelper::cancel('podcast.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
			if ($canDo->get('core.edit')) {
				JToolBarHelper::apply('podcast.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('podcast.save', 'COM_PODCASTMANAGER_TOOLBAR_SAVE');

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create')) {
					JToolBarHelper::custom('podcast.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}

			JToolBarHelper::cancel('podcast.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	
	private function fillMetaID3(&$row, &$title)
	{
		define('GETID3_HELPERAPPSDIR', JPATH_COMPONENT.DS.'getid3');
		include JPATH_COMPONENT.DS.'getid3'.DS.'getid3.php';
		
		$params =& JComponentHelper::getParams('com_podcastmanager');
		$mediapath = $params->get('mediapath', 'components/com_podcastmanager/media');
		
		$filename = JPATH_ROOT.DS.$mediapath.DS.$row->filename;
		
		if (!JFile::exists($filename)) {
			$filename = JPATH_ROOT.DS.$row->filename;
		}
		
		$getID3 = new getID3($filename);
		$fileInfo = $getID3->analyze($filename);
		
		
		if(isset($fileInfo['tags_html'])) {
			$t = $fileInfo['tags_html'];
			$tags = isset($t['id3v2']) ? $t['id3v2'] : (isset($t['id3v1']) ? $t['id3v1'] : (isset($t['quicktime']) ? $t['quicktime'] : null));
			if($tags) {
				
				if (isset($tags['title'])) {
					$title = $tags['title'][0];
				}
				
				if (isset($tags['album'])) {
					$row->itSubtitle = $tags['album'][0];
				}
				
				if (isset($tags['artist'])) {
					$row->itAuthor = $tags['artist'][0];
				}
			}
		}
		
		if (isset($fileInfo['playtime_string'])) {
			$row->itDuration = $fileInfo['playtime_string'];
		}
	}
}