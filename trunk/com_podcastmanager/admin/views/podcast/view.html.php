<?php
/**
* Podcast Manager for Joomla!
*
* @version		$Id: view.html.php 9 2011-01-05 17:24:41Z mbabker $
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

//TODO: Remove global $option

jimport('joomla.application.component.view');
jimport('joomla.filesystem.file');

class PodcastManagerViewPodcast extends JView
{
	function display($tpl = null)
	{
		global $option;
		$app	= JFactory::getApplication();
		
		$params =& JComponentHelper::getParams($option);
		
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$id = (int)$cid[0];
		
		$row =& JTable::getInstance('podcast', 'Table');
		
		// TODO: may need to prefill this with article information
		$title = '';
		
		$filename = JRequest::getVar('filename', null, '', 'array');
		
		if (!$filename && !$id) {
			// move on
		} else if(!$id || !$row->load($id)) { // metadata hasn't been added yet or the given id is invalid
			
			if(!$filename) { // this should never happen if user uses interface
				$app->redirect("index.php?option=$option", JText::_('Invalid ID or Filename'), 'error');
				return;
			}
			
			$row->filename = JFile::makeSafe($filename[0]);
			if($row->filename !== $filename[0]) { // either they're messing with us or the OS is allowing filenames that Joomla isn't
				$app->redirect("index.php?option=$option", JText::_('Filename Cannot Contain Special Characters'), 'error'); // either way, let's stay safe
				return;
			}

			$this->fillMetaID3($row, $title);
		} else {
			// TODO: fill $title with article information? it's not currently used by the template anyway.
		}
		
		if (stristr($row->filename, ' ')) {
			$tpl = 'error';
		}
		
		$explicit = JHTML::_('select.booleanlist', 'itExplicit', '', $row->itExplicit);
		$block = JHTML::_('select.booleanlist', 'itBlock', '', $row->itBlock);
		
		$this->assign('text', '{enclose ' . $row->filename . '}');
		$this->assign('explicit', $explicit);
		$this->assign('block', $block);
		$this->assignRef('podcast', $row);
		$this->assignRef('title', $title);
		$this->assignRef('params', $params);
		
		parent::display($tpl);
	}
	
	private function fillMetaID3(&$row, &$title)
	{
		define('GETID3_HELPERAPPSDIR', JPATH_COMPONENT . DS . 'getid3');
		include JPATH_COMPONENT . DS . 'getid3' . DS . 'getid3.php';
		
		$params =& JComponentHelper::getParams('com_podcastmanager');
		$mediapath = $params->get('mediapath', 'components/com_podcastmanager/media');
		
		$filename = JPATH_ROOT . DS . $mediapath . DS . $row->filename;
		
		if (!JFile::exists($filename)) {
			$filename = JPATH_ROOT .  DS . $row->filename;
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