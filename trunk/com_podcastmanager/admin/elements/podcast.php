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
defined('_JEXEC') or die;

class PodcastManagerElementPodcast extends JElement
{
	/**
	 * Element name
	 *
	 * @var		string
	 */
	var	$_name = 'Podcast';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$app		= JFactory::getApplication();
		$db			= JFactory::getDbo();
		$doc		= JFactory::getDocument();
		$template	= $app->getTemplate();
		$fieldName	= $control_name.'['.$name.']';
		$podcast = JTable::getInstance('podcast');
		if ($value) {
			$podcast->load($value);
		} else {
			$podcast->title = JText::_('COM_PODCASTMANAGER_SELECT_A_PODCAST');
		}

		$js = "
		function PodcastManagerSelectPodcast_".$name."(id, title, filename, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			SqueezeBox.close();
		}";
		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_podcastmanager&amp;task=element&amp;tmpl=component&amp;function=PodcastManagerSelectPodcast_'.$name;

		JHtml::_('behavior.modal', 'a.modal');
		$html = "\n".'<div class="fltlft"><input type="text" id="'.$name.'_name" value="'.htmlspecialchars($podcast->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
//		$html .= "\n &#160; <input class=\"inputbox modal-button\" type=\"button\" value=\"".JText::_('JSELECT')."\" />";
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('COM_PODCASTMANAGER_SELECT_A_PODCAST').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('JSELECT').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}
}