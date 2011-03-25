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

jimport( 'joomla.application.component.view');
jimport('joomla.filesystem.file');

class PodcastManagerViewFeed extends JView
{
	function display($tpl = null)
	{
		// Get the component params
		$params = JComponentHelper::getParams('com_podcastmanager');
		
		// Get the data from the model
		$items		= $this->get('Items');
		
		$document = JFactory::getDocument();
		$document->setMimeEncoding('application/rss+xml');
		
		if($params->get('cache', true)) {
			$cache =& JFactory::getCache('com_podcastmanager', 'output');
			if($cache->start('feed', 'com_podcastmanager')) {
				return;
			}
		}

		$xw = new xmlWriter();
		$xw->openMemory();
		$xw->setIndent(true);
		$xw->setIndentString("\t");
		
		$xw->startDocument('1.0','UTF-8');
		
		$xw->startElement('rss');
		$xw->writeAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
		$xw->writeAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
		$xw->writeAttribute('version','2.0');
		
		$xw->startElement('channel');
		
		$xw->startElement('atom:link');
		$xw->writeAttribute('href', JURI::root(false) . 'index.php?option=com_podcastmanager&view=feed&format=raw');
		$xw->writeAttribute('rel', 'self');
		$xw->writeAttribute('type', 'application/rss+xml');
		$xw->endElement();
		
		$xw->writeElement('title', $params->get('title', ''));
		$xw->writeElement('link', JURI::base()); // may want to make configurable as param
		
		$lang =& JFactory::getLanguage();
		$xw->writeElement('language', $lang->getTag());
		
		$xw->writeElement('copyright', $params->get('copyright', ''));
		
		$xw->writeElement('itunes:subtitle', $params->get('itSubtitle', ''));
		$xw->writeElement('itunes:author', $params->get('itAuthor', ''));
		
		$itBlock = $params->get('itBlock', 0);
		
		if ($itBlock) {
			$xw->writeElement('itunes:block', 'yes');
		}
		
		$itExplicit = $params->get('itExplicit', 0);
		
		if ($itExplicit = 1) {
			$xw->writeElement('itunes:explicit', 'yes');
		} else if ($itExplicit = 2) {
			$xw->writeElement('itunes:explicit', 'clean');
		} else {
			$xw->writeElement('itunes:explicit', 'no');
		}		
		
		$xw->writeElement('itunes:keywords', $params->get('itKeywords', ''));
		
		$xw->writeElement('itunes:summary', $params->get('description', ''));
		
		$xw->writeElement('description', $params->get('description', ''));

		$xw->startElement('itunes:owner');
		$xw->writeElement('itunes:name', $params->get('itOwnerName', ''));
		$xw->writeElement('itunes:email', $params->get('itOwnerEmail', ''));
		$xw->endElement();
		
		$xw->startElement('itunes:image');
		
		$imageURL = $params->get('itImage', '');
		
		if (!preg_match('/^http/', $imageURL))
		{
			$imageURL = JURI::root(false) . $imageURL;
		}
				
		$xw->writeAttribute('href', $imageURL);
		$xw->endElement();
		
		$this->setCategories($xw, $params);
		
		$this->setItems($xw, $params, $items);
		
		$xw->endElement(); // channel
		$xw->endElement(); // rss
				
		echo $xw->outputMemory(true);
		
		if(isset($cache))
			$cache->end(); // cache output
	}
	
	private function setCategories(&$xw, $params)
	{
		$cats = array('itCategory1', 'itCategory2', 'itCategory3');
		
		foreach ($cats as $cat) {
			$pieces = explode('>', $params->get($cat, ''));
			
			if ($pieces[0] != '') {
				$xw->startElement('itunes:category');
				$xw->writeAttribute('text', trim($pieces[0]));

				if (count($pieces) > 1) {
					$xw->startElement('itunes:category');
					$xw->writeAttribute('text', trim($pieces[1]));
					$xw->endElement();
				}

				$xw->endElement();
			}
		}
	}
	
	private function setItems(&$xw, $params, $items)
	{
		foreach ($items as $item) {
			// Set the file path on the file structure
			$filepath	= JPATH_ROOT.'/'.$item->filename;
			
			// Check if the file exists
			if (JFile::exists($filepath)) {
				$filename = JURI::base().$item->filename;
			}
			
			// Start writing the element
			$xw->startElement('item');
			
			$xw->writeElement('title', $item->title);
			$xw->writeElement('itunes:author', $item->itAuthor);
			$xw->writeElement('itunes:subtitle', $item->itSubtitle);
			$xw->writeElement('itunes:summary', $item->itSummary);
			
			$xw->writeElement('description', $item->itSummary);
			
			// Write the enclosure element
			$xw->startElement('enclosure');
			$xw->writeAttribute('url', $filename);
			$xw->writeAttribute('length', filesize($filename));
			$xw->writeAttribute('type', $params->get('mimetype', 'audio/mpeg'));
			$xw->endElement();

			$xw->writeElement('guid', $filename);
			
			if ($item->itBlock) {
				$xw->writeElement('itunes:block', 'yes');
			}

			if ($item->itExplicit) {
				$xw->writeElement('itunes:explicit', 'yes');
			}
			
			$xw->writeElement('pubDate', date('r', strtotime($item->publish_up)));
			
			$xw->writeElement('itunes:duration', $item->itDuration);
			$xw->writeElement('itunes:keywords', $item->itKeywords);
			
			$xw->endElement(); // item
		}
	}
}
