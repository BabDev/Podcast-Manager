<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* @package		PodcastManager
* @subpackage	com_podcastmanager
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');
jimport('joomla.filesystem.file');

/**
 * Feed RAW view class.
 *
 * @since		1.8
 */
class PodcastManagerViewFeed extends JView
{
	/**
	 * Display the view
	 *
	 * @param   string $tpl	The name of the template file to parse
	 *
	 * @return	void
	 * @since	1.6
	 */
	function display($tpl = null)
	{
		// Get the component params
		$params = JComponentHelper::getParams('com_podcastmanager');

		// Get the data from the model
		$items	= $this->get('Items');
		$feed	= $this->get('Feed');

		$document = JFactory::getDocument();
		$document->setMimeEncoding('application/rss+xml');

		if($params->get('cache', true)) {
			$cache = JFactory::getCache('com_podcastmanager', 'output');
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
		$xw->writeAttribute('href', JURI::root(false).'index.php?option=com_podcastmanager&view=feed&feedname='.$feed->id.'&format=raw');
		$xw->writeAttribute('rel', 'self');
		$xw->writeAttribute('type', 'application/rss+xml');
		$xw->endElement();

		$xw->writeElement('title', $feed->name);
		$xw->writeElement('link', JURI::base());

		$lang = JFactory::getLanguage();
		$xw->writeElement('language', $lang->getTag());

		$xw->writeElement('copyright', $feed->copyright);

		$xw->writeElement('itunes:subtitle', $feed->subtitle);
		$xw->writeElement('itunes:author', $feed->author);

		$itBlock = $feed->block;

		if ($itBlock == 1) {
			$xw->writeElement('itunes:block', 'yes');
		}

		$itExplicit = $feed->explicit;

		if ($itExplicit == 1) {
			$xw->writeElement('itunes:explicit', 'yes');
		} else if ($itExplicit == 2) {
			$xw->writeElement('itunes:explicit', 'clean');
		} else {
			$xw->writeElement('itunes:explicit', 'no');
		}

		$xw->writeElement('itunes:keywords', $feed->keywords);

		$xw->writeElement('itunes:summary', $feed->description);

		$xw->writeElement('description', $feed->description);

		$xw->startElement('itunes:owner');
		$xw->writeElement('itunes:name', $feed->ownername);
		$xw->writeElement('itunes:email', $feed->owneremail);
		$xw->endElement();

		$xw->startElement('itunes:image');

		$imageURL = $feed->image;

		if (!preg_match('/^http/', $imageURL))
		{
			$imageURL = JURI::root().$imageURL;
		}

		$xw->writeAttribute('href', $imageURL);
		$xw->endElement();

		$this->setCategories($xw, $params, $feed);

		$this->setItems($xw, $params, $items);

		$xw->endElement(); // channel
		$xw->endElement(); // rss

		echo $xw->outputMemory(true);

		if (isset($cache)) {
			$cache->end(); // cache output
		}
	}

	/**
	 * Function to set the feed categories
	 *
	 * @param	object	$xw		XMLWriter object containing generated feed output
	 * @param	array	$params	The component parameters
	 * @param	object	$feed	An object containing the feed record
	 *
	 * @return	void
	 * @since	1.6
	 */
	private function setCategories(&$xw, $params, $feed)
	{
		$cats = array('category1', 'category2', 'category3');
		$i = 1;

		foreach ($cats as $cat) {
			$pieces = explode('>', $feed->$cat);

			if ($pieces[0] != '') {
				$xw->startElement('itunes:category');
				$xw->writeAttribute('text', trim($pieces[0]));

				if (count($pieces) > 1) {
					$xw->startElement('itunes:category');
					$xw->writeAttribute('text', trim($pieces[1]));
					$xw->endElement();
				}

				$xw->endElement();
				$i++;
			}
		}
	}

	/**
	 * Function to generate the feed items
	 *
	 * @param	object	$xw		XMLWriter object containing generated feed output
	 * @param	array	$params	The component parameters
	 * @param	object	$feed	An object containing the feed record
	 *
	 * @return	void
	 * @since	1.6
	 */
	private function setItems(&$xw, $params, $items)
	{
		foreach ($items as $item) {
			// Set the file path on the file structure
			$filepath	= $item->filename;

			// Check if the file is from off site
			if (preg_match('/^http/', $filepath)) {
				// The file is off set, no verification necessary
				$filename	= $filepath;
			} else {
				// The file is stored on site, check if it exists
				$filepath	= JPATH_ROOT.'/'.$filename;

				// Check if the file exists
				if (JFile::exists($filepath)) {
					$filename	= JURI::base().$filename;
				}
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
			$xw->writeAttribute('length', filesize($filepath));
			$xw->writeAttribute('type', $params->get('mimetype', 'audio/mpeg'));
			$xw->endElement();

			$xw->writeElement('guid', $filename);

			$itBlock	= $item->itBlock;
			$itExplicit	= $item->itExplicit;

			if ($itBlock == 1) {
				$xw->writeElement('itunes:block', 'yes');
			}

			if ($itExplicit == 1) {
				$xw->writeElement('itunes:explicit', 'yes');
			} else if ($itExplicit == 2) {
				$xw->writeElement('itunes:explicit', 'clean');
			} else {
				$xw->writeElement('itunes:explicit', 'no');
			}

			$xw->writeElement('pubDate', date('r', strtotime($item->publish_up)));

			$xw->writeElement('itunes:duration', $item->itDuration);
			$xw->writeElement('itunes:keywords', $item->itKeywords);

			$xw->endElement();
		}
	}
}
