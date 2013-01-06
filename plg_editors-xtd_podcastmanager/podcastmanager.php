<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  plg_editors-xtd_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

/**
 * Podcast Manager button plugin.
 *
 * @package     PodcastManager
 * @subpackage  plg_editors-xtd_podcastmanager
 * @since       1.6
 */
class PlgButtonPodcastManager extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since	1.6
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Display the button
	 *
	 * @param   string  $name  The name of the editor
	 *
	 * @return  array  Markup to display the button
	 *
	 * @since   1.6
	 */
	public function onDisplay($name)
	{
		/*
		 * Javascript to insert the link
		 * Modal calls PodcastManagerSelectPodcast when a podcast is clicked
		 * PodcastManagerSelectPodcast creates the plugin syntax, sends it to the editor,
		 * and closes the modal.
		 */
		$js = "
		function PodcastManagerSelectPodcast(id, object) {
			var tag = '{podcast id='+id+'}';
			jInsertEditorText(tag, '" . $name . "');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		JHtml::_('behavior.modal');

		/*
		 * Use the modal view to select the podcast.
		 * Currently uses broadcast class for the image.
		 */
		$link = 'index.php?option=com_podcastmanager&amp;view=podcasts&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';

		$button = new JObject;
		$button->modal = true;
		$button->link = $link;
		$button->text = JText::_('PLG_EDITORS-XTD_PODCASTMANAGER_BUTTON');
		$button->name = 'broadcast';
		$button->title = JText::_('PLG_EDITORS-XTD_PODCASTMANAGER_BUTTON_TOOLTIP');
		$button->options = "{handler: 'iframe', size: {x: 770, y: 400}}";

		return $button;
	}
}
