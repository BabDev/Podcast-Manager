<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

JLoader::register('PodcastManagerHelper', JPATH_ADMINISTRATOR . '/components/com_podcastmanager/helpers/podcastmanager.php');

/**
 * Feed RAW view class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.6
 */
class PodcastManagerViewFeed extends JViewLegacy
{
	/**
	 * Stat tracking service
	 *
	 * @var    string
	 * @since  2.1
	 */
	protected $tracking;

	/**
	 * Username for the stat tracking service
	 *
	 * @var    string
	 * @since  2.1
	 */
	protected $trackUser;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function display($tpl = null)
	{
		static $log;

		// Get the component params
		$params = JComponentHelper::getParams('com_podcastmanager');

		// Enable logging
		if ($params->get('enableLogging', '0') == '1')
		{
			if ($log == null)
			{
				$options['format'] = '{DATE}\t{TIME}\t{LEVEL}\t{CODE}\t{MESSAGE}';
				$options['text_file'] = 'podcastmanager.php';
				$log = JLog::addLogger($options);
			}
		}

		// Store the values for the tracking service
		$this->tracking  = $params->get('tracking', 'none');
		$this->trackUser = $params->get('trackname', '');

		// Get the data from the model
		$items = $this->get('Items');
		$feed  = $this->get('Feed');

		$document = JFactory::getDocument();
		$document->setMimeEncoding('application/rss+xml');

		$xw = new xmlWriter;
		$xw->openMemory();
		$xw->setIndent(true);
		$xw->setIndentString("\t");

		$xw->startDocument('1.0', 'UTF-8');

		$xw->startElement('rss');
		$xw->writeAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
		$xw->writeAttribute('version', '2.0');

		$xw->startElement('channel');

		$xw->writeElement('title', $feed->name);
		$xw->writeElement('link', JUri::base());

		$feedLang = $feed->language;

		if ($feedLang == '*')
		{
			$feedLang = JFactory::getLanguage()->getTag();
		}

		$xw->writeElement('language', $feedLang);

		$xw->writeElement('copyright', $feed->copyright);

		if (strlen($feed->newFeed) > 1)
		{
			$xw->writeElement('itunes:new-feed-url', $feed->newFeed);
		}

		$xw->writeElement('itunes:subtitle', $feed->subtitle);
		$xw->writeElement('itunes:author', $feed->author);

		$itBlock = $feed->block;

		if ($itBlock == 1)
		{
			$xw->writeElement('itunes:block', 'yes');
		}

		$itExplicit = $feed->explicit;

		if ($itExplicit == 1)
		{
			$xw->writeElement('itunes:explicit', 'yes');
		}
		elseif ($itExplicit == 2)
		{
			$xw->writeElement('itunes:explicit', 'clean');
		}
		else
		{
			$xw->writeElement('itunes:explicit', 'no');
		}

		$xw->writeElement('itunes:keywords', $feed->keywords);

		$xw->writeElement('itunes:summary', $feed->description);

		$xw->writeElement('description', $feed->description);

		$xw->startElement('itunes:owner');
		$xw->writeElement('itunes:name', $feed->ownername);
		$xw->writeElement('itunes:email', $feed->owneremail);
		$xw->endElement();

		$imageURL = $feed->image;

		if (strlen($imageURL) > 1)
		{
			$xw->startElement('itunes:image');

			if (!preg_match('/^http/', $imageURL))
			{
				$imageURL = JUri::root() . $imageURL;
			}

			$xw->writeAttribute('href', $imageURL);
			$xw->endElement();
		}

		$this->_setCategories($xw, $feed);

		$this->_setItems($xw, $items);

		// End channel element
		$xw->endElement();

		// End RSS element
		$xw->endElement();

		echo $xw->outputMemory(true);
	}

	/**
	 * Function to set the feed categories
	 *
	 * @param   XMLWriter  &$xw   XMLWriter object containing generated feed output
	 * @param   object     $feed  An object containing the feed record
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	private function _setCategories(&$xw, $feed)
	{
		$cats = array('category1', 'category2', 'category3');
		$i = 1;

		foreach ($cats as $cat)
		{
			$pieces = explode('>', $feed->$cat);

			if ($pieces[0] != '')
			{
				$xw->startElement('itunes:category');
				$xw->writeAttribute('text', trim($pieces[0]));

				if (count($pieces) > 1)
				{
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
	 * @param   XMLWriter  &$xw    XMLWriter object containing generated feed output
	 * @param   object     $items  An object containing the feed record
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	private function _setItems(&$xw, $items)
	{
		foreach ($items as $item)
		{
			// Set the file path on the file structure
			$filepath = $item->filename;

			// Check if the file is from off site
			if (preg_match('/^http/', $filepath))
			{
				// The file is off site, no verification necessary
				$filename = $filepath;

				// Since PHP's filesize can't parse off-site media, use a fake value
				$filesize = '8427391';
			}
			else
			{
				// The file is stored on site, check if it exists
				$filepath = JPATH_ROOT . '/' . $item->filename;

				// Check if the file exists
				if (is_file($filepath))
				{
					$filename = JUri::base() . $item->filename;
				}

				// Process the filesize now
				$filesize = filesize($filepath);
			}

			if (!isset($filename))
			{
				// Write the DB error to the log
				JLog::add((JText::sprintf('COM_PODCASTMANAGER_ERROR_FINDING_FILE', $item->filename)), JLog::ERROR);
			}
			else
			{
				// Process the URL through the helper to get the stat tracking details if applicable
				$filename = PodcastManagerHelper::getMediaUrl($filename);

				// Start writing the element
				$xw->startElement('item');

				$xw->writeElement('title', $item->title);
				$xw->writeElement('itunes:author', $item->itAuthor);
				$xw->writeElement('itunes:subtitle', $item->itSubtitle);
				$xw->writeElement('itunes:summary', $item->itSummary);
				$xw->writeElement('description', $item->itSummary);

				$imageURL = $item->itImage;

				if (strlen($imageURL) > 1)
				{
					$xw->startElement('itunes:image');

					if (!preg_match('/^http/', $imageURL))
					{
						$imageURL = JUri::root() . $imageURL;
					}

					$xw->writeAttribute('href', $imageURL);
					$xw->endElement();
				}

				// Write the enclosure element
				$xw->startElement('enclosure');
				$xw->writeAttribute('url', $filename);
				$xw->writeAttribute('length', $filesize);

				// Set the MIME type
				if (strlen($item->mime) >= 3)
				{
					$mime = $item->mime;
				}
				else
				{
					$mime = 'audio/mpeg';
				}

				$xw->writeAttribute('type', $mime);
				$xw->endElement();

				$xw->writeElement('guid', $filename);

				$itBlock = $item->itBlock;
				$itExplicit = $item->itExplicit;

				if ($itBlock == 1)
				{
					$xw->writeElement('itunes:block', 'yes');
				}

				if ($itExplicit == 1)
				{
					$xw->writeElement('itunes:explicit', 'yes');
				}
				elseif ($itExplicit == 2)
				{
					$xw->writeElement('itunes:explicit', 'clean');
				}
				else
				{
					$xw->writeElement('itunes:explicit', 'no');
				}

				$xw->writeElement('pubDate', date('r', strtotime($item->publish_up)));

				$xw->writeElement('itunes:duration', $item->itDuration);
				$xw->writeElement('itunes:keywords', $item->itKeywords);

				$xw->endElement();
			}
		}
	}
}
