<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  plg_content_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/player.php';

/**
 * Podcast Manager content plugin.
 *
 * @package     PodcastManager
 * @subpackage  plg_content_podcastmanager
 * @since       1.6
 */
class PlgContentPodcastManager extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since   1.8
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Plugin that loads a podcast player within content
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   object   &$article  The article object.  Note $article->text is also available
	 * @param   mixed    &$params   The article params
	 * @param   integer  $page      The 'page' number
	 *
	 * @return  mixed  Player object on success, notice on failure
	 *
	 * @since   1.6
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		static $log;

		// Check if we're in the site app, otherwise, do nothing
		if (!JFactory::getApplication()->isSite())
		{
			return true;
		}

		$podmanparams = JComponentHelper::getParams('com_podcastmanager');

		if ($podmanparams->get('enableLogging', '0') == '1')
		{
			if ($log == null)
			{
				$options['format'] = '{DATE}\t{TIME}\t{LEVEL}\t{CODE}\t{MESSAGE}';
				$options['text_file'] = 'podcastmanager.php';
				$log = JLog::addLogger($options);
			}
		}

		// Handle instances coming from Podcast Manager extensions
		$podManContexts = array('com_podcastmanager.feed', 'mod_podcastmanagerfeed.module');
		if (in_array($context, $podManContexts))
		{
			$article->text = $article->player;
			$feedView = $context;
		}

		// Simple performance check to determine whether plugin should process further
		if (strpos($article->text, 'podcast') === false)
		{
			return true;
		}

		// Expression to search for
		$regex = '/\{(podcast)\s+(.*?)}/i';

		// Find all instances of plugin and put in $matches
		preg_match_all($regex, $article->text, $matches);

		foreach ($matches as $id => $podcast)
		{
			// Set $i for multiple {podcast instances
			$i = 0;

			// We only want to process ID 0
			if ($id > 0)
			{
				return true;
			}

			foreach ($podcast as $episode)
			{
				// Check if we're in a Podcast Manager instance; if so, extract data from the object
				if ((isset($feedView)) && ($feedView == $context))
				{
					$podtitle = $article->title;
					$podfilepath = $article->filename;
					$playerType = $podmanparams->get('linkhandling', 'player');
				}
				else
				{
					// Retrieve the title from the object and prepare it for a DB query
					// 9 offset for {podcast marker, -1 offset for closing }
					$podtitle = substr($episode, 9, -1);

					// Set the player type from the component params
					$playerType = $podmanparams->get('linkhandling', 'player');

					// Fix for K2 Item when {podcast marker is last text in an item with no readmore
					// -17 offset removes '}</p>{K2Splitter'
					if ($context == 'com_k2.item' && strpos($episode, '{K2Splitter'))
					{
						$podtitle = substr($episode, 9, -17);
					}

					// Check if we've received an ID
					if (strpos($podtitle, 'id') === 0)
					{
						// Explode the tag into separate elements to process overrides
						$articleTag = explode(';', $podtitle);

						// Remove the id= portion and cast as an integer
						$podtitle = (int) substr($articleTag[0], 3);

						// Check if we have element 1, the player override, and if the string has anything
						if (isset($articleTag[1]) && strlen($articleTag[1]) >= 1)
						{
							// Remove the player= portion and set the player type
							$playerType = substr($articleTag[1], 7);
						}
					}

					// Query the DB for the title string, returning the filename
					$db = JFactory::getDBO();
					$query = $db->getQuery(true);

					// Common query fields regardless of method
					$query->select($db->quoteName('filename'));
					$query->from($db->quoteName('#__podcastmanager'));

					// If the title is a string, use the "classic" lookup method
					if (is_string($podtitle))
					{
						$query->where($db->quoteName('title') . ' = ' . $db->quote($podtitle));
					}
					// If an integer, we need to also get the title of the podcast, as well as search on the ID
					elseif (is_int($podtitle))
					{
						$query->select($db->quoteName('title'));
						$query->where($db->quoteName('id') . ' = ' . (int) $podtitle);
					}
					$db->setQuery($query);
					if (!$db->loadObject())
					{
						// Write the DB error to the log
						JLog::add((JText::sprintf('PLG_CONTENT_PODCASTMANAGER_ERROR_PULLING_DATABASE', $podtitle) . '  ' . $db->stderr(true)), JLog::ERROR);

						JError::raiseNotice(null, JText::sprintf('PLG_CONTENT_PODCASTMANAGER_ERROR_PULLING_DATABASE', $podtitle));
					}
					else
					{
						$dbResult = $db->loadObject();
						$podfilepath = $dbResult->filename;

						// Set the title if we searched by ID
						if (isset($dbResult->title))
						{
							$podtitle = $dbResult->title;
						}
					}
				}

				if (isset($podfilepath))
				{
					try
					{
						// Get the player
						$player = new PodcastManagerPlayer($podmanparams, $podfilepath, $podtitle, $playerType);

						// Fix for K2 Item
						if ($context == 'com_k2.item' && strpos($matches[0][$i], '{K2Splitter'))
						{
							$string = JString::str_ireplace($matches[0][$i], '{K2Splitter}', substr($matches[0][$i], 0, -16));
						}
						else
						{
							$string = $matches[0][$i];
						}

						// Replace the {podcast marker with the player
						$article->text = JString::str_ireplace($string, $player->generate(), $article->text);
					}
					catch (RuntimeException $e)
					{
						// Write the error to the log
						JLog::add(JText::sprintf('PLG_CONTENT_PODCASTMANAGER_ERROR_INVALID_PLAYER', $playerType), JLog::INFO);

						// Remove the {podcast marker
						$article->text = JString::str_ireplace($matches[0][$i], '', $article->text);
					}
				}
				else
				{
					JLog::add(JText::_('PLG_CONTENT_PODCASTMANAGER_ERROR_NO_FILEPATH'), JLog::INFO);

					// Remove the {podcast marker
					$article->text = JString::str_ireplace($matches[0][$i], '', $article->text);
				}

				$i++;
			}
		}

		return true;
	}
}
