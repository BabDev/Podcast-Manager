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

		if ($context == 'com_podcastmanager.feed' && $params->get('show_item_player') == 1)
		{
			$article->text = $article->player;
			$feedView = 'com_podcastmanager.feed';
		}

		// Simple performance check to determine whether plugin should process further
		if (strpos($article->text, 'podcast') === false)
		{
			return true;
		}

		// Expression to search for
		$regex = '/\{(podcast)\s+(.*?)}/i';

		// Find all instances of the podcast marker and put in $matches
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

			// If using SM2 player, define the scripts only on the first iteration
			if (($podmanparams->get('linkhandling') == 'html5') && ($i == 0))
			{
				// Initialize variables
				$document = JFactory::getDocument();

				// Define non debug settings
				$file = 'soundmanager2-nodebug-jsmin.js';
				$debugMode = 'false';
				$player = 'player.js';

				// If site debug enabled, enable SoundManager debug
				if (JDEBUG)
				{
					$file = 'soundmanager2.js';
					$debugMode = 'true';
					$player = 'player-uncompressed.js';
				}

				// Declare the stylesheets
				JHtml::stylesheet('plugins/content/podcastmanager/soundmanager/css/player.css', false, false, false);
				JHtml::stylesheet('plugins/content/podcastmanager/soundmanager/css/flashblock.css', false, false, false);

				// Declare the scripts
				JHtml::script('plugins/content/podcastmanager/soundmanager/script/' . $file, false, false);
				// Check if the custom tags are already defined first; if not, add them
				if (!in_array('<script type="text/javascript">soundManager.debugMode = ' . $debugMode . ';</script>', $document->_custom))
				{
					$document->addCustomTag('<script type="text/javascript">soundManager.debugMode = ' . $debugMode . ';</script>');
				}
				if (!in_array('<script type="text/javascript">soundManager.url = "' . JURI::base() . 'plugins/content/podcastmanager/soundmanager/swf/"</script>', $document->_custom))
				{
					$document->addCustomTag('<script type="text/javascript">soundManager.url = "' . JURI::base() . 'plugins/content/podcastmanager/soundmanager/swf/"</script>');
				}
				JHtml::script('plugins/content/podcastmanager/soundmanager/script/' . $player, false, false);
			}

			foreach ($podcast as $episode)
			{
				// Check if we're in the Podcast Manager Feed view; if so, extract data from the object
				if ((isset($feedView)) && ($feedView == $context))
				{
					$podtitle = $article->title;
					$podfilepath = $article->filename;
				}
				else
				{
					// Retrieve the title from the object and prepare it for a DB query
					// 9 offset for {podcast marker, -1 offset for closing }
					$podtitle = substr($episode, 9, -1);

					// Fix for K2 Item when {podcast marker is last text in an item with no readmore
					// -17 offset removes '}</p>{K2Splitter'
					if ($context == 'com_k2.item' && strpos($episode, '{K2Splitter'))
					{
						$podtitle = substr($episode, 9, -17);
					}

					// Check if we've received an ID
					if (strpos($podtitle, 'id') === 0)
					{
						// Remove the id= portion and cast as an integer
						$podtitle = (int) substr($podtitle, 3);
					}

					// Query the DB for the title string, returning the filename
					$db = JFactory::getDBO();
					$query = $db->getQuery(true);
					// If the title is a string, use the "classic" lookup method
					if (is_string($podtitle))
					{
						$query->select($db->quoteName('filename'));
						$query->from($db->quoteName('#__podcastmanager'));
						$query->where($db->quoteName('title') . ' = ' . $db->quote($podtitle));
					}
					elseif (is_int($podtitle))
					{
						$query->select($db->quoteName('filename') . ', ' . $db->quoteName('title'));
						$query->from($db->quoteName('#__podcastmanager'));
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
					// Get the player
					$player = new PodcastManagerPlayer($podmanparams, $podfilepath, $podtitle);

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

/**
 * Podcast Manager player building class.
 *
 * @package     PodcastManager
 * @subpackage  plg_content_podcastmanager
 * @since       1.6
 */
class PodcastManagerPlayer
{
	/**
	 * Type	The type of player being rendered
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $playerType = 'player';

	/**
	 * The title of the podcast being processed
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $podtitle = null;

	/**
	 * The URL of the file being processed
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $fileURL = null;

	/**
	 * Podcast Manager component parameters
	 *
	 * @var    JRegistry
	 * @since  1.6
	 */
	protected $podmanparams = null;

	/**
	 * The server file path to the file being processed
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $podfilepath = null;

	/**
	 * An array of valid player types
	 *
	 * @var    array
	 * @since  1.6
	 */
	protected $validTypes = array('custom', 'html5', 'link', 'player', 'QTplayer');

	/**
	 * An array of valid file types
	 *
	 * @var    array
	 * @since  1.6
	 */
	protected $fileTypes = array(
		'm4a' => 'audio/x-m4a',
		'm4v' => 'video/x-m4v',
		'mov' => 'video/quicktime',
		'mp3' => 'audio/mpeg',
		'mp4' => 'video/mp4'
	);

	/**
	 * The class constructor
	 *
	 * @param   JRegistry  &$podmanparams  The Podcast Manager parameters
	 * @param   string     $podfilepath    The path to the file being processed
	 * @param   string     $podtitle       The title of the podcast being processed
	 *
	 * @since   1.6
	 */
	public function __construct(&$podmanparams, $podfilepath, $podtitle)
	{
		$this->podmanparams = $podmanparams;
		$this->podfilepath = $podfilepath;
		$playerType = $this->podmanparams->get('linkhandling', 'player');

		if (in_array($playerType, $this->validTypes))
		{
			$this->playerType = $playerType;
		}

		$this->fileURL = $this->determineURL($podfilepath);
		$this->podtitle = $podtitle;
	}

	/**
	 * Function to generate the player
	 *
	 * @return  object  The player for the article
	 *
	 * @since   1.6
	 */
	public function generate()
	{
		$func = $this->playerType;

		return $this->$func();
	}

	/**
	 * Function to create the URL for a podcast episode file
	 *
	 * @param   object  $podfilepath  The filename of the podcast file.
	 *
	 * @return  string  The URL to the file
	 *
	 * @since   1.6
	 */
	protected function determineURL($podfilepath)
	{
		// Convert the file path to a string
		$tempfile = $podfilepath;

		if (isset($tempfile->filename))
		{
			$filepath = $tempfile->filename;
		}
		else
		{
			$filepath = $tempfile;
		}

		$filename = $filepath;

		// Check if the file is from off site
		if (!preg_match('/^http/', $filename))
		{
			// The file is stored on site, check if it exists
			$filepath = JPATH_ROOT . '/' . $filename;

			// Check if the file exists
			if (JFile::exists($filepath))
			{
				$filename = JURI::base() . $filename;
			}
		}

		return $filename;
	}

	/**
	 * Function to generate a custom player
	 *
	 * @return  object  A link to the podcast as defined by the user
	 *
	 * @since   1.7
	 */
	protected function custom()
	{
		$linkcode = $this->podmanparams->get('customcode', '');
		return preg_replace('/\{podcast\}/', $this->fileURL, $linkcode);
	}

	/**
	 * Function to generate a HTML5 player that will fall back to Flash if necessary
	 *
	 * @return  object  A HTML5 or Flash player for the podcast
	 *
	 * @since   1.8
	 */
	protected function html5()
	{
		$player = '<div id="sm2-container">'
		. '<div class="sm2-player">'
		. '<a class="sm2_link" href="' . $this->fileURL . '">' . htmlspecialchars($this->podtitle) . '</a>'
		. '</div>'
		. '</div>';
		return $player;
	}

	/**
	 * Function to generate a link player
	 *
	 * @return  object  A HTML link to the podcast
	 *
	 * @since   1.6
	 */
	protected function link()
	{
		return '<a href="' . $this->fileURL . '">' . htmlspecialchars($this->podmanparams->get('linktitle', JText::_('Listen Now!'))) . '</a>';
	}

	/**
	 * Function to generate a flash player
	 *
	 * @return  object  A flash player containing the podcast episode
	 *
	 * @since   1.6
	 */
	protected function player()
	{
		$width = $this->podmanparams->get('playerwidth', 400);
		$height = $this->podmanparams->get('playerheight', 15);

		$playerURL = JURI::base() . 'plugins/content/podcastmanager/podcast/xspf_player_slim.swf';

		$player	= '<object type="application/x-shockwave-flash" width="' . $width . '" height="' . $height . '" data="' . $playerURL . '?song_url=' . $this->fileURL . '&song_title=' . $this->podtitle . '&player_title=' . $this->podtitle . '">'
		. '<param name="movie" value="' . $playerURL . '?song_url=' . $this->fileURL . '&song_title=' . $this->podtitle . '&player_title=' . $this->podtitle . '" />'
		. '</object>';

		return $player;
	}

	/**
	 * Function to generate a QuickTime player
	 *
	 * @return  object  A QuickTime player containing the podcast episode
	 *
	 * @since   1.6
	 */
	protected function QTplayer()
	{
		$tempfile = get_object_vars($this->podfilepath);
		$filepath = substr(implode('', $tempfile), 0);
		$ext = substr($filepath, strlen($filepath) - 3);

		$width = $this->podmanparams->get('playerwidth', 320);
		$height = $this->podmanparams->get('playerheight', 240);

		$player = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="' . $width . '" height="' . $height . '" codebase="http://www.apple.com/qtactivex/qtplugin.cab">'
		. '<param name="src" value="' . $this->fileURL . '" />'
		. '<param name="href" value="' . $this->fileURL . '" />'
		. '<param name="scale" value="aspect" />'
		. '<param name="controller" value="true" />'
		. '<param name="autoplay" value="false" />'
		. '<param name="bgcolor" value="000000" />'
		. '<param name="pluginspage" value="http://www.apple.com/quicktime/download/" />'
		. '<embed src="' . $this->fileURL . '" width="' . $width . '" height="' . $height . '" scale="aspect" cache="true" bgcolor="000000" autoplay="false" controller="true" src="' . $this->fileURL . '" type="' . $this->fileTypes[$ext] . '" pluginspage="http://www.apple.com/quicktime/download/"></embed>'
		. '</object>';

		return $player;
	}
}
