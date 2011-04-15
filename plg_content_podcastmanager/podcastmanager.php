<?php 
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

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
			// We only want to process ID 0
			if ($id > 0) {
				return;
			}

			// Retrieve the title and convert it to a useable string
			// 9 offset for {podcast marker
			// -1 offset for closing }
			$podtitle	= substr(implode(' ', $podcast), 9, -1);

			// Query the DB for the title string, returning the filename
			$db = JFactory::getDBO();
			$db->setQuery(
				'SELECT `filename`' .
				' FROM `#__podcastmanager`' .
				' WHERE `title` = "'.$podtitle.'"'
			);
			$podfilepath = $db->loadObject();

			// Get the player
			$player = new PodcastManagerPlayer($podmanparams, $podfilepath, $podtitle, $article->title);

			// Replace the {podcast marker with the player
			$article->text = JString::str_ireplace($matches[0][$id], $player->generate(), $article->text);
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
	private $validTypes = array('link', 'player', 'QTplayer');
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
	function __construct(&$podmanparams, $podfilepath, $podtitle, $title)
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
	 * @param	object	The filename of the podcast file relative to the site root.
	 * 
	 * @return	object	The URL to the file
	 */
	private function determineURL($podfilepath)
	{
		// Convert the file path to a string
		$tempfile	= get_object_vars($podfilepath);
		$filepath	= substr(implode('', $tempfile), 0);
		
		// Set the file path based on the server
		$fullPath = JPATH_BASE.'/'.$filepath;
		
		// Check if the file exists
		if (JFile::exists($fullPath)) {
			$filename = JURI::base().$filepath;
		}
		
		return $filename;
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
