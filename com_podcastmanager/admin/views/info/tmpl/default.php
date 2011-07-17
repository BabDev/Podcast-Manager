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

// Styles specific to the info view
JHtml::stylesheet('administrator/components/com_podcastmanager/media/css/info.css', false, false, false);

// Site addresses to be processed outside JText
$flbab		= '<a href="http://www.flbab.com/extensions/podcast-manager" target="_blank">http://www.flbab.com/extensions/podcast-manager</a>';
$getid3		= '<b><a href="http://www.getid3.org" target="_blank">getID3</a></b>';
$sm2		= '<b><a href="http://www.schillmania.com/projects/soundmanager2/" target="_blank">SoundManager2</a></b>';
$transifex	= '<a href="https://www.transifex.net/projects/p/podcast-manager" target="_blank">https://www.transifex.net/projects/p/podcast-manager</a>';
$xspf		= '<b><a href="http://musicplayer.sourceforge.net" target="_blank">XSPF Player Lite</a></b>';
?>
<div class="podMan-welcome"><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_THANK_YOU_FOR_INSTALLING', $flbab);?></div>

<div class="podMan-header"><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS')?></div>
<div class="divider"></div>
<div>
	<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_INTRO')?></p>
	<ul>
		<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_COMPONENT_MANAGER')?></li>
		<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_COMPONENT_MEDIA')?></li>
		<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_GETID3')?></li>
		<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_MODULE_FEED')?></li>
		<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_MODULE_LINK')?></li>
		<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_PLUGIN_CONTENT')?></li>
		<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_PLUGIN_EDITOR')?></li>
	</ul>
</div>

<div class="podMan-header"><?php echo JText::_('COM_PODCASTMANAGER_INFO_ALLOWED_FILE_TYPES')?></div>
<div class="divider"></div>
<div>
	<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_ALLOWED_FILE_TYPES_INTRO')?></p>
	<ul>
		<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_ALLOWED_FILE_TYPES_AUDIO')?></li>
		<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_ALLOWED_FILE_TYPES_VIDEO')?></li>
	</ul>
</div>

<div class="podMan-header"><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT')?></div>
<div class="divider"></div>
<div>
	<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_1')?></p>
	<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_2')?></p>
	<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_3')?></p>
	<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_4')?></p>
	<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_5')?></p>
</div>

<div class="podMan-header"><?php echo JText::_('COM_PODCASTMANAGER_INFO_TRANSLATIONS')?></div>
<div class="divider"></div>
<div>
	<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_TRANSLATIONS_INTRO')?></p>
	<ul>
		<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_TRANSLATIONS_PTBR')?></li>
	</ul>
	<p><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_TRANSLATIONS_CONTRIBUTE', $transifex);?></p>
</div>

<div class="podMan-header"><?php echo JText::_('COM_PODCASTMANAGER_INFO_CREDITS')?></div>
<div class="divider"></div>
<div>
	<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_CREDITS_INTRO')?></p>
	<ul>
		<li><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_CREDITS_GETID3', $getid3);?></li>
		<li><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_CREDITS_SM2', $sm2);?></li>
		<li><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_CREDITS_XSPF', $xspf);?></li>
	</ul>
</div>
