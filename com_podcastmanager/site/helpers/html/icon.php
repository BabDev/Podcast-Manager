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

jimport('joomla.application.component.helper');
class JHtmlIcon
{
	/* static function create($weblink, $params)
	{
		$uri = JFactory::getURI();

		$url = JRoute::_(WeblinksHelperRoute::getFormRoute(0, base64_encode($uri)));
		$text = JHtml::_('image','system/new.png', JText::_('JNEW'), NULL, true);
		$button = JHtml::_('link',$url, $text);
		$output = '<span class="hasTip" title="'.JText::_('COM_WEBLINKS_FORM_CREATE_WEBLINK').'">'.$button.'</span>';
		return $output;
	} */

	static function edit($podcast, $params, $attribs = array())
	{
		$user = JFactory::getUser();
		$uri = JFactory::getURI();

		if ($params && $params->get('popup')) {
			return;
		}

		if ($podcast->published < 0) {
			return;
		}

		JHtml::_('behavior.tooltip');
		$url	= PodcastManagerHelperRoute::getFormRoute($podcast->id, base64_encode($uri));
		$icon	= $podcast->published ? 'edit.png' : 'edit_unpublished.png';
		$text	= JHtml::_('image','system/'.$icon, JText::_('JGLOBAL_EDIT'), NULL, true);

		if ($podcast->published == 0) {
			$overlib = JText::_('JUNPUBLISHED');
		}
		else {
			$overlib = JText::_('JPUBLISHED');
		}

		$date = JHtml::_('date', $podcast->created);
		$author = $podcast->itAuthor;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= htmlspecialchars($author, ENT_COMPAT, 'UTF-8');

		$button = JHtml::_('link', JRoute::_($url), $text);

		$output = '<span class="hasTip" title="'.JText::_('JGLOBAL_EDIT').' :: '.$overlib.'">'.$button.'</span>';

		return $output;
	}
}
