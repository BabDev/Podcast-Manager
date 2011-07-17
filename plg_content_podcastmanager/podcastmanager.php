<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgContentPodcastManager extends JPlugin
{
	/**
	 * Plugin that loads a podcast player within content
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		if ($context == 'com_podcastmanager.feed' && $params->get('show_item_player') == 1) {
			$article->text = $article->player;
			$feedView	= 'com_podcastmanager.feed';
		}

		// Simple performance check to determine whether plugin should process further
		if (strpos($article->text, 'podcast') === false) {
			return true;
		}

		// Expression to search for
		$regex	= '/\{(podcast) (.*)\}/i';

		// Find all instances of plugin and put in $matches
		preg_match_all($regex, $article->text, $matches);

		$podmanparams = JComponentHelper::getParams('com_podcastmanager');

		foreach ($matches as $id => $podcast) {
			// Set $i for multiple {podcast instances
			$i	= 0;

			// We only want to process ID 0
			if ($id > 0) {
				return;
			}

			// If using SM2 player, define the scripts only on the first iteration
			if (($podmanparams->get('linkhandling') == 'html5') && ($i == 0)) {
				// Initialize variables
				$document	= JFactory::getDocument();

				// Define non debug settings
				$file		= 'soundmanager2-nodebug-jsmin.js';
				$debugMode	= 'false';

				// If site debug enabled, enable SoundManager debug
				if (JDEBUG) {
					$file		= 'soundmanager2.js';
					$debugMode	= 'true';
				}

				// Declare the stylesheets
				JHTML::stylesheet('plugins/content/podcastmanager/soundmanager/css/player.css', false, false, false);
				JHTML::stylesheet('plugins/content/podcastmanager/soundmanager/css/flashblock.css', false, false, false);

				// Declare the scripts
				JHTML::script('plugins/content/podcastmanager/soundmanager/script/'.$file, false, false);
				// Check if the custom tags are already defined first; if not, add them
				if (!in_array('<script type="text/javascript">soundManager.debugMode = '.$debugMode.';</script>', $document->_custom)) {
					$document->addCustomTag('<script type="text/javascript">soundManager.debugMode = '.$debugMode.';</script>');
				}
				if (!in_array('<script type="text/javascript">soundManager.url = "'.JURI::base().'plugins/content/podcastmanager/soundmanager/swf/"</script>', $document->_custom)) {
					$document->addCustomTag('<script type="text/javascript">soundManager.url = "'.JURI::base().'plugins/content/podcastmanager/soundmanager/swf/"</script>');
				}
				JHTML::script('plugins/content/podcastmanager/soundmanager/script/player.js', false, false);
			}

			foreach ($podcast as $episode) {
				// Check if we're in the Podcast Manager Feed view; if so, extract data from the object
				if ((isset($feedView)) && ($feedView == $context)) {
					$podtitle		= $article->title;
					$podfilepath	= $article->filename;
				} else {
					// Retrieve the title from the object and prepare it for a DB query
					// 9 offset for {podcast marker, -1 offset for closing }
					$podtitle	= substr($episode, 9, -1);

					// Query the DB for the title string, returning the filename
					$db = JFactory::getDBO();
					$db->setQuery(
						'SELECT `filename`' .
						' FROM `#__podcastmanager`' .
						' WHERE `title` = "'.$podtitle.'"'
					);
					$podfilepath = $db->loadObject();
				}

				// Get the player
				$player = new PodcastManagerPlayer($podmanparams, $podfilepath, $podtitle);

				// Replace the {podcast marker with the player
				$article->text = JString::str_ireplace($matches[0][$i], $player->generate(), $article->text);

				$i++;
			}
		}

	return true;
	}
}

class PodcastManagerPlayer
{
	private $playerType = null;
	private $podtitle = null;
	private $fileURL = null;
	private $podmanparams = null;
	private $podfilepath = null;
	private $validTypes = array('custom', 'html5', 'link', 'player', 'QTplayer');
	private $fileTypes = array (
		'm4a' => 'audio/x-m4a',
		'm4v' => 'video/x-m4v',
		'mov' => 'video/quicktime',
		'mp3' => 'audio/mpeg',
		'mp4' => 'video/mp4',
	);

	/**
	 * The class constructor
	 */
	function __construct(&$podmanparams, $podfilepath, $podtitle)
	{
		$this->podmanparams = $podmanparams;
		$this->podfilepath	= $podfilepath;
		$playerType			= $this->podmanparams->get('linkhandling', 'player');

		if (in_array($playerType, $this->validTypes)) {
			$this->playerType = $playerType;
		} else {
			$this->playerType = 'player';
		}

		$this->fileURL		= $this->determineURL($podfilepath);
		$this->podtitle		= $podtitle;
	}

	/**
	 * Function to generate the player
	 *
	 * @return	object	The player for the article
	 */
	public function generate()
	{
		$func = $this->playerType;

		return $this->$func();
	}

	/**
	 * Function to create the URL for a podcast episode file
	 *
	 * @param	object	The filename of the podcast file.
	 *
	 * @return	object	The URL to the file
	 */
	private function determineURL($podfilepath)
	{
		// Convert the file path to a string
		$tempfile	= $podfilepath;

		if (isset($tempfile->filename)) {
			$filepath	= $tempfile->filename;
		} else {
			$filepath	= $tempfile;
		}

		$filename = $filepath;

		// Check if the file is from off site
		if (!preg_match('/^http/', $filename)) {
			// The file is stored on site, check if it exists
			$filepath	= JPATH_ROOT.'/'.$item->filename;

			// Check if the file exists
			if (JFile::exists($filepath)) {
				$filename = JURI::base().$item->filename;
			}

			// Set the file path based on the server
			$fullPath = JPATH_BASE.'/'.$filepath;

			// Check if the file exists
			if (JFile::exists($fullPath)) {
				$filename = JURI::base().$filepath;
			}
		}

		return $filename;
	}

	/**
	 * Function to generate a custom player
	 *
	 * @return	object	A link to the podcast as defined by the user
	 */
	private function custom()
	{
		$linkcode = $this->podmanparams->get('customcode', '');
		return preg_replace('/\{podcast\}/', $this->fileURL, $linkcode);
	}

	/**
	 * Function to generate a HTML5 player that will fall back to Flash if necessary
	 *
	 * @return	object	A HTML5 or Flash player for the podcast
	 */
	private function html5()
	{
		return '<div id="sm2-container"><div class="sm2-player"><a class="sm2_link" href="'.$this->fileURL.'">'.htmlspecialchars($this->podtitle).'</a></div></div>';
	}

	/**
	 * Function to generate a link player
	 *
	 * @return	object	A HTML link to the podcast
	 */
	private function link()
	{
		return '<a href="'.$this->fileURL.'">'.htmlspecialchars($this->podmanparams->get('linktitle', JText::_('Listen Now!'))).'</a>';
	}

	/**
	 * Function to generate a flash player
	 *
	 * @return	object	A flash player containing the podcast episode
	 */
	private function player()
	{
		$width = $this->podmanparams->get('playerwidth', 400);
		$height = $this->podmanparams->get('playerheight', 15);

		$playerURL = JURI::base().'plugins/content/podcastmanager/podcast/xspf_player_slim.swf';

		return '<object type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" data="'.$playerURL.'?song_url='.$this->fileURL.'&song_title='.$this->podtitle.'&player_title='.$this->podtitle.'"><param name="movie" value="'.$playerURL.'?song_url='.$this->fileURL.'&song_title='.$this->podtitle.'&player_title='.$this->podtitle.'" /></object>';
	}

	/**
	 * Function to generate a QuickTime player
	 *
	 * @return	object	A QuickTime player containing the podcast episode
	 */
	private function QTplayer()
	{
		$tempfile	= get_object_vars($this->podfilepath);
		$filepath	= substr(implode('', $tempfile), 0);
		$ext = substr($filepath, strlen($filepath) - 3);

		$width = $this->podmanparams->get('playerwidth', 320);
		$height = $this->podmanparams->get('playerheight', 240);

		$player = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="'.$width.'" height="'.$height.'" codebase="http://www.apple.com/qtactivex/qtplugin.cab">'
		. '<param name="src" value="'.$this->fileURL.'" />'
		. '<param name="href" value="'.$this->fileURL.'" />'
		. '<param name="scale" value="aspect" />'
		. '<param name="controller" value="true" />'
		. '<param name="autoplay" value="false" />'
		. '<param name="bgcolor" value="000000" />'
		. '<param name="pluginspage" value="http://www.apple.com/quicktime/download/" />'
		. '<embed src="'.$this->fileURL.'" width="'.$width.'" height="'.$height.'" scale="aspect" cache="true" bgcolor="000000" autoplay="false" controller="true" src="'.$this->fileURL.'" type="'.$this->fileTypes[$ext].'" pluginspage="http://www.apple.com/quicktime/download/"></embed>'
		. '</object>';

		return $player;
	}
}
