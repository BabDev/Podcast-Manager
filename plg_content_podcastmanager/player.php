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
	protected $validTypes = array('custom', 'link', 'player');

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
	 * @param   string     $playerType     The type of player to use
	 *
	 * @since   1.6
	 */
	public function __construct(&$podmanparams, $podfilepath, $podtitle, $playerType)
	{
		$this->podmanparams = $podmanparams;
		$this->podfilepath = $podfilepath;

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
	 * Function to generate a link player
	 *
	 * @return  object  A HTML link to the podcast
	 *
	 * @since   1.6
	 */
	protected function link()
	{
		return '<a href="' . $this->fileURL . '">' . htmlspecialchars($this->podmanparams->get('linktitle', 'Listen Now!')) . '</a>';
	}

	/**
	 * Function to generate a media player
	 *
	 * @return  object  A media player containing the podcast episode
	 *
	 * @since   1.6
	 */
	protected function player()
	{
		$width = $this->podmanparams->get('playerwidth', 400);
		$height = $this->podmanparams->get('playerheight', 15);

		$player	= '';

		return $player;
	}
}
