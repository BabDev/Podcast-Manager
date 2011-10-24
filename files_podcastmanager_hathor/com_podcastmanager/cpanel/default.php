<?php
/**
* Podcast Manager for Joomla!
*
* @package     PodcastManager
* @subpackage  files_podcastmanager_hathor
*
* @copyright   Copyright (C) 2011 Michael Babker. All rights reserved.
* @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

defined('_JEXEC') or die;

// Styles specific to the cpanel
JHtml::stylesheet('administrator/templates/hathor/html/com_podcastmanager/cpanel/cpanel.css', false, false, false);

// Site addresses to be processed outside JText
$babdev		= '<a href="http://www.babdev.com/extensions/podcast-manager" target="_blank">http://www.babdev.com/extensions/podcast-manager</a>';
$getid3		= '<b><a href="http://www.getid3.org" target="_blank">getID3</a></b>';
$liveupdate	= '<b><a href=https://www.akeebabackup.com/software/akeeba-live-update.html" target="_blank">Akeeba Live Update</a></b>';
$sm2		= '<b><a href="http://www.schillmania.com/projects/soundmanager2/" target="_blank">SoundManager2</a></b>';
$transifex	= '<a href="https://www.transifex.net/projects/p/podcast-manager" target="_blank">https://www.transifex.net/projects/p/podcast-manager</a>';
$xspf		= '<b><a href="http://musicplayer.sourceforge.net" target="_blank">XSPF Player Lite</a></b>';

// Icons folder
$icons = JURI::base().'components/com_podcastmanager/media/images/icons';

jimport('joomla.html.pane');
JHtml::_('behavior.framework');
$pane =& JPane::getInstance('Sliders');
?>

<p class="intro"><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_THANK_YOU_FOR_INSTALLING', $babdev);?></p>
<div class="cpanel-icons">
	<div id="cpanel">
		<div class="icon-wrapper">
			<div class="icon">
				<a href="index.php?option=com_podcastmanager&amp;view=feeds">
					<img src="<?php echo $icons; ?>/feeds.png" />
					<span><?php echo JText::_('COM_PODCASTMANAGER_SUBMENU_FEEDS')?></span>
				</a>
			</div>
		</div>
		<div class="icon-wrapper">
			<div class="icon">
				<a href="index.php?option=com_podcastmanager&amp;view=podcasts">
					<img src="<?php echo $icons; ?>/podcasts.png" />
					<span><?php echo JText::_('COM_PODCASTMANAGER_SUBMENU_PODCASTS')?></span>
				</a>
			</div>
		</div>
		<div class="icon-wrapper">
			<div class="icon">
				<a href="index.php?option=com_podcastmedia&view=media">
					<img src="<?php echo $icons; ?>/files.png" />
					<span><?php echo JText::_('COM_PODCASTMANAGER_SUBMENU_FILES')?></span>
				</a>
			</div>
		</div>
		<?php echo LiveUpdate::getIcon(); ?>
	</div>
</div>

<div class="cpanel-component">
<?php echo $pane->startPane('panel-sliders')."\n"; ?>
	<?php echo $pane->startPanel(JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS'), 'howitworks')."\n"; ?>
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
	<?php echo $pane->endPanel()."\n"; ?>
	<?php echo $pane->startPanel(JText::_('COM_PODCASTMANAGER_INFO_ALLOWED_FILE_TYPES'), 'filetypes')."\n"; ?>
	<div>
		<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_ALLOWED_FILE_TYPES_INTRO')?></p>
		<ul>
			<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_ALLOWED_FILE_TYPES_AUDIO')?></li>
			<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_ALLOWED_FILE_TYPES_VIDEO')?></li>
		</ul>
	</div>
	<?php echo $pane->endPanel()."\n"; ?>
	<?php echo $pane->startPanel(JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT'), 'whattoexpect')."\n"; ?>
	<div>
		<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_1')?></p>
		<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_2')?></p>
		<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_3')?></p>
		<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_4')?></p>
		<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_5')?></p>
	</div>
	<?php echo $pane->endPanel()."\n"; ?>
	<?php echo $pane->startPanel(JText::_('COM_PODCASTMANAGER_INFO_TRANSLATIONS'), 'translations')."\n"; ?>
	<div>
		<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_TRANSLATIONS_INTRO')?></p>
		<ul>
			<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_TRANSLATIONS_PTBR')?></li>
		</ul>
		<p><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_TRANSLATIONS_CONTRIBUTE', $transifex);?></p>
	</div>
	<?php echo $pane->endPanel()."\n"; ?>
	<?php echo $pane->startPanel(JText::_('COM_PODCASTMANAGER_INFO_CREDITS'), 'credits')."\n"; ?>
	<div>
		<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_CREDITS_INTRO')?></p>
		<ul>
			<li><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_CREDITS_LIVEUPDATE', $liveupdate);?></li>
			<li><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_CREDITS_GETID3', $getid3);?></li>
			<li><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_CREDITS_SM2', $sm2);?></li>
			<li><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_CREDITS_XSPF', $xspf);?></li>
		</ul>
	</div>
	<?php echo $pane->endPanel()."\n"; ?>
<?php echo $pane->endPane()."\n"; ?>
</div>
