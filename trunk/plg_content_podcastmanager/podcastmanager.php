<?php 
/**
* Podcast Manager for Joomla!
*
* @version		$Id: podcastmanager.php 7 2011-01-05 16:46:53Z mbabker $
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

$app	= JFactory::getApplication();

$app->registerEvent( 'onPrepareContent', 'plgContentPodcastManager' );

function plgContentPodcastManager( &$row, &$params, $page=0 )
{	
	// Performance check: don't go any farther if we don't have an {enclose ...} tag
	if ( JString::strpos( $row->text, 'enclose' ) === false && JString::strpos( $row->text, 'player' ) === false) {
		return true;
	}
	
	jimport('joomla.filesystem.file');
	
	preg_match_all( '/\{(enclose|player) (.*)\}/i' , $row->text, $matches );
	
	$podManParams =& JComponentHelper::getParams('com_podcastmanager');
	
	foreach ($matches[2] as $id => $podcast) {

		/*
		 * $podcast contents:
		 * $podcast[0] filename (required)
		 * $podcast[1] file length in bytes
		 * $podcast[2] file mime type
		 * 
		 * We're only interested in $podcast[0] here
		 */
		$enclose = explode(' ', $podcast);

		$player = new PodcastManagerPlayer($podManParams, $enclose, $row->title);
				
		$row->text = JString::str_ireplace($matches[0][$id], $player->generate(), $row->text);
	}
	
	return true;
}

class PodcastManagerPlayer
{
	private $playerType = null;
	private $enclose = null;
	private $fileURL = null;
	private $title = null;
	private $podManParams = null;
	private $validTypes = array('links', 'player', 'html', 'QTplayer');
	private $fileTypes = array (
		'asf' => 'video/asf',
		'asx' => 'video/asf',
		'avi' => 'video/avi',
		'm4a' => 'audio/x-m4a',
		'm4v' => 'video/x-m4v',
		'mov' => 'video/quicktime',
		'mp3' => 'audio/mpeg',
		'mpe' => 'video/mpeg',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'ogg' => 'audio/ogg',
		'qt' => 'video/quicktime',
		'ra' => 'audio/x-realaudio',
		'ram' => 'audio/x-realaudio',
		'wav' => 'audio/wav',
		'wax' => 'video/asf',
		'wma' => 'audio/wma',
		'wmv' => 'video/wmv',
		'wmx' => 'video/asf',
	);
	
	function __construct(&$podManParams, $enclose, $title)
	{	
		$this->podManParams =& $podManParams;
		$playerType = $this->podManParams->get('linkhandling', 'player');
		
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
		$mediapath = $this->podManParams->get('mediapath', 'components/com_podcastmanager/media');
		
		// If we have a full URL, stop.
		// Otherwise, see if the file is the normal mediapath and build URL
		// Else, just assume Joomla! root
		if (!preg_match('/^https?:\/\//', $filename)) {

			$fullPath = JPATH_BASE . DS . $mediapath . DS . $filename;

			if (JFile::exists($fullPath)) {
				$filename = JURI::base() . $mediapath . '/' . $filename;
			} else {
				$filename = JURI::base() . $filename;
			}

		} 
		
		return $filename;
	}
	
	private function links()
	{
		return '<a href="' . $this->fileURL . '">' . htmlspecialchars($this->podManParams->get('linktitle', JText::_('Listen Now!'))) . '</a>';
	}
	
	private function player()
	{
		$width = $this->podManParams->get( 'playerwidth', 400);
		$height = $this->podManParams->get( 'playerheight', 15);

		$playerURL = JURI::base() . 'plugins/content/podcast/xspf_player_slim.swf';

		return '<object type="application/x-shockwave-flash" width="' . $width . '" height="' . $height . '" data="' . $playerURL . '?song_url=' . $this->fileURL . '&song_title=' . $this->title . '&player_title=' . $this->title . '"><param name="movie" value="' . $playerURL . '?song_url=' . $this->fileURL . '&song_title=' . $this->title . '&player_title=' . $this->title . '" /></object>';
	}
	
	private function html()
	{
		$linkcode = $this->podManParams->get('linkcode', '');
		return preg_replace('/\{filename\}/', $this->fileURL, $linkcode);
	}
	
	private function QTplayer()
	{
		$ext = substr($this->enclose[0], strlen($this->enclose[0]) - 3);

		$width = $this->podManParams->get( 'playerwidth', 320);
		$height = $this->podManParams->get( 'playerheight', 240);
		
		$player = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="' . $width . '" height="' . $height . '" codebase="http://www.apple.com/qtactivex/qtplugin.cab">'
		. '<param name="src" value="' . $this->fileURL . '" />'
		. '<param name="href" value="' . $this->fileURL . '" />'
		. '<param name="scale" value="aspect" />'
		. '<param name="controller" value="true" />'
		. '<param name="autoplay" value="false" />'
		. '<param name="bgcolor" value="000000" />'
		. '<param name="pluginspage" value="http://www.apple.com/quicktime/download/" />'
		. '<embed src="' . $this->fileURL . '" width="' . $width . '" height="' . $height . '" scale="aspect" cache="true" bgcolor="000000" autoplay="false" controller="true" src="' . $this->fileURL .'" type="' . $this->fileTypes[$ext] . '" pluginspage="http://www.apple.com/quicktime/download/"></embed>'
		. '</object>';
		
		return $player;
	}
}
