<?php 
/**
* Podcast Manager for Joomla!
*
* @version		$Id$
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Editor Podcast buton
 */
class plgButtonPodcastManager extends JPlugin
{
	/**
	 * Constructor
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Display the button
	 *
	 * @return array A single element array of (podcast title)
	 */
	function onDisplay($name)
	{
		/*
		 * Javascript to insert the link
		 * View element calls PodcastManagerSelectPodcast when a podcast is clicked
		 * PodcastManagerSelectPodcast creates the plugin syntax, sends it to the editor,
		 * and closes the select frame.
		 */
		$js = "
		function PodcastManagerSelectPodcast(title, object) {
			var tag = '{podcast '+title+'}';
			jInsertEditorText(tag, '".$name."');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		JHTML::_('behavior.modal');

		/*
		 * Use the built-in element view to select the podcast.
		 * Currently uses blank class.
		 */
		$link = 'index.php?option=com_podcastmanager&amp;view=podcasts&amp;layout=modal&amp;tmpl=component';

		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_EDITORS-XTD_PODCASTMANAGER_BUTTON'));
		$button->set('name', 'blank');
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");

		return $button;
	}
}
