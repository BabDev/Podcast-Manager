<?php
/**
 * Podcast Manager for Joomla!
 *
 * @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Podcast Media component
 *
 * @package		Podcast Manager
 * @subpackage	com_podcastmedia
 * @since		1.6
 */
class PodcastMediaViewMediaList extends JView
{
	function display($tpl = null)
	{
		// Do not allow cache
		JResponse::allowCache(false);

		$app	= JFactory::getApplication();
		$style = $app->getUserStateFromRequest('podcastmedia.list.layout', 'layout', 'thumbs', 'word');

		$lang	= JFactory::getLanguage();

		JHtml::_('behavior.framework', true);

		$document = JFactory::getDocument();
		$document->addStyleSheet('../media/media/css/medialist-'.$style.'.css');
		if ($lang->isRTL()) :
		$document->addStyleSheet('../media/media/css/medialist-'.$style.'_rtl.css');
		endif;

		$document->addScriptDeclaration("
		window.addEvent('domready', function() {
			window.parent.document.updateUploader();
			$$('a.img-preview').each(function(el) {
				el.addEvent('click', function(e) {
					new Event(e).stop();
					window.top.document.preview.fromElement(el);
				});
			});
		});");

		$audio		= $this->get('audio');
		$folders	= $this->get('folders');
		$state		= $this->get('state');

		$this->assign('baseURL', JURI::root());
		$this->assignRef('audio', $audio);
		$this->assignRef('folders', $folders);
		$this->assignRef('state', $state);

		parent::display($tpl);
	}

	function setFolder($index = 0)
	{
		if (isset($this->folders[$index])) {
			$this->_tmp_folder = &$this->folders[$index];
		} else {
			$this->_tmp_folder = new JObject;
		}
	}

	function setAudio($index = 0)
	{
		if (isset($this->audio[$index])) {
			$this->_tmp_audio = &$this->audio[$index];
		} else {
			$this->_tmp_audio = new JObject;
		}
	}
}
