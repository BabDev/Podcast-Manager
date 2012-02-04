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
 * Podcast Manager player builder.
 *
 * @package     PodcastManager
 * @subpackage  plg_content_podcastmanager
 * @since       1.6
 */
class PodcastManagerPlayer
{
	/**
	 * The type of player being rendered
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
