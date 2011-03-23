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
		$regex		= '/\{(podcast) (.*)\}/i';
		
		// Find all instances of plugin and put in $matches
		preg_match_all($regex, $article->text, $matches);

	$podmanparams = JComponentHelper::getParams('com_podcastmanager');
	
	foreach ($matches as $id => $podcast) {

		/*
		 * $podcast contents:
		 * $podcast[0] filename (required)
		 * $podcast[1] file length in bytes
		 * $podcast[2] file mime type
		 * 
		 * We're only interested in $podcast[0] here
		 */
		 
		$enclose = explode(' ', $podcast);

		// Retreive the title string from the $matches array and convert it to an object
		$podtitle	= (object) $matches[2];
		
		// Query the DB for the title string, returning the filename
		$db = JFactory::getDBO();
		$db->setQuery(
			'SELECT `filename`' .
			' FROM `#__podcastmanager`' .
			' WHERE `title` = "'.$podtitle.'"'
		);
		$podfilepath = $db->loadObject();
		
		// Get the player
		$player = new PodcastManagerPlayer($podmanparams, $enclose, $article->title);
		
		// Replace the {podcast marker with the player
		$article->text = JString::str_ireplace($matches[0][$id], $player->generate(), $article->text);
	}
	
	return true;
	}
}

class PodcastManagerPlayer
{
	private $playerType = null;
	private $enclose = null;
	private $fileURL = null;
	private $title = null;
	private $podmanparams = null;
	private $validTypes = array('links', 'player', 'html', 'QTplayer');
	private $fileTypes = array (
		'm4a' => 'audio/x-m4a',
		'm4v' => 'video/x-m4v',
		'mov' => 'video/quicktime',
		'mp3' => 'audio/mpeg',
		'mp4' => 'video/mp4',
	);
	
	function __construct(&$podmanparams, $enclose, $title)
	{	
		$this->podmanparams = $podmanparams;
		$playerType = $this->podmanparams->get('linkhandling', 'player');
		
		if (in_array($playerType, $this->validTypes)) {
			$this->playerType = $playerType;
		} else {
			$this->playerType = 'player';
		}
		
		$this->fileURL = $this->determineURL($enclose[0]);
		$this->title = $title;
		$this->enclose = $enclose;
	}
	
	public function generate()
	{
		$func = $this->playerType;
		
		return $this->$func();
	}
	
	private function determineURL($filename)
	{
		// If we have a full URL, stop.
		// Otherwise, see if the file is the normal mediapath and build URL
		// Else, just assume Joomla! root
		if (!preg_match('/^https?:\/\//', $filename)) {

			$fullPath = JPATH_BASE.'media/com_podcastmanager/'.$filename;

			if (JFile::exists($fullPath)) {
				$filename = JURI::base().'media/com_podcastmanager/'.$filename;
			} else {
				$filename = JURI::base().'/'.$filename;
			}
		} 
		return $filename;
	}
	
	private function links()
	{
		return '<a href="'.$this->fileURL.'">'.htmlspecialchars($this->podmanparams->get('linktitle', JText::_('Listen Now!'))).'</a>';
	}
	
	private function player()
	{
		$width = $this->podmanparams->get('playerwidth', 400);
		$height = $this->podmanparams->get('playerheight', 15);

		$playerURL = JURI::base().'plugins/content/podcastmanager/xspf_player_slim.swf';

		return '<object type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" data="'.$playerURL.'?song_url='.$this->fileURL.'&song_title='.$this->title.'&player_title='.$this->title.'"><param name="movie" value="'.$playerURL.'?song_url='.$this->fileURL.'&song_title='.$this->title.'&player_title='.$this->title.'" /></object>';
	}
	
	private function html()
	{
		$linkcode = $this->podmanparams->get('linkcode', '');
		return preg_replace('/\{filename\}/', $this->fileURL, $linkcode);
	}
	
	private function QTplayer()
	{
		$ext = substr($this->enclose[0], strlen($this->enclose[0]) - 3);

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
