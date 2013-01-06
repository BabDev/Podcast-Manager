<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  files_podcastmanager_strapped
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Site addresses to be processed outside JText
$babdev		= '<a href="http://www.babdev.com/extensions/podcast-manager" target="_blank">http://www.babdev.com/extensions/podcast-manager</a>';
$getid3		= '<b><a href="http://www.getid3.org" target="_blank">getID3</a></b>';
$liveupdate	= '<b><a href="https://www.akeebabackup.com/software/akeeba-live-update.html" target="_blank">Akeeba Live Update</a></b>';
$mejs		= '<b><a href="http://mediaelementjs.com" target="_blank">MediaElement.JS</a></b>';
$transifex	= '<a href="https://opentranslators.transifex.net/projects/p/podcast-manager" target="_blank">https://opentranslators.transifex.net/projects/p/podcast-manager</a>';

$buttons = $this->getButtons();

JHtml::_('behavior.framework');
?>
<div class="row-fluid">
	<!-- Begin Sidebar -->
	<div id="sidebar" class="span3">
		<div class="sidebar-nav">
			<div class="well">
				<div class="row-striped">
					<?php echo JHtml::_('icons.buttons', $buttons);
					echo LiveUpdate::getIcon(); ?>
				</div>
			</div>
		</div>
	</div>
	<!-- End Sidebar -->
	<!-- Begin Content -->
	<div class="span9">
		<h5><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_THANK_YOU_FOR_INSTALLING', $babdev);?></h5>

		<ul class="nav nav-tabs">
			<li class="active"><a href="#howitworks" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS');?></a></li>
			<li><a href="#allowedfiles" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_INFO_ALLOWED_FILE_TYPES');?></a></li>
			<li><a href="#whattoexpect" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT');?></a></li>
			<li><a href="#translations" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_INFO_TRANSLATIONS');?></a></li>
			<li><a href="#credits" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_INFO_CREDITS');?></a></li>
		</ul>
		<div class="tab-content">
			<!-- Begin Tabs -->
			<div class="tab-pane active" id="howitworks">
				<div class="well">
					<h6><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS'); ?></h6>
					<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_INTRO')?></p>
					<ul>
						<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_COMPONENT_MANAGER')?></li>
						<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_COMPONENT_MEDIA')?></li>
						<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_GETID3')?></li>
						<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_MODULE_FEED')?></li>
						<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_MODULE_LINK')?></li>
						<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_PLUGIN_CONTENT')?></li>
						<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_PLUGIN_EDITOR')?></li>
						<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_PLUGIN_PODCASTMEDIA')?></li>
						<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_PLUGIN_SMARTSEARCH_FEEDS')?></li>
						<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_PLUGIN_SMARTSEARCH_PODCASTS')?></li>
					</ul>
					<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_HOW_PODCAST_MANAGER_WORKS_LAYOUTS')?></p>
				</div>
			</div>
			<div class="tab-pane" id="allowedfiles">
				<div class="well">
					<h6><?php echo JText::_('COM_PODCASTMANAGER_INFO_ALLOWED_FILE_TYPES'); ?></h6>
					<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_ALLOWED_FILE_TYPES_INTRO')?></p>
					<ul>
						<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_ALLOWED_FILE_TYPES_AUDIO')?></li>
						<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_ALLOWED_FILE_TYPES_VIDEO')?></li>
					</ul>
				</div>
			</div>
			<div class="tab-pane" id="whattoexpect">
				<div class="well">
					<h6><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT'); ?></h6>
					<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_1')?></p>
					<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_2')?></p>
					<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_3')?></p>
					<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_4')?></p>
					<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_WHAT_TO_EXPECT_PARA_5')?></p>
				</div>
			</div>
			<div class="tab-pane" id="translations">
				<div class="well">
					<h6><?php echo JText::_('COM_PODCASTMANAGER_INFO_TRANSLATIONS'); ?></h6>
					<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_TRANSLATIONS_INTRO')?></p>
					<ul>
						<li><?php echo JText::_('COM_PODCASTMANAGER_INFO_TRANSLATIONS_PTBR')?></li>
					</ul>
					<p><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_TRANSLATIONS_CONTRIBUTE', $transifex);?></p>
				</div>
			</div>
			<div class="tab-pane" id="credits">
				<div class="well">
					<h6><?php echo JText::_('COM_PODCASTMANAGER_INFO_CREDITS'); ?></h6>
					<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_CREDITS_INTRO')?></p>
					<ul>
						<li><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_CREDITS_LIVEUPDATE', $liveupdate);?></li>
						<li><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_CREDITS_GETID3', $getid3);?></li>
						<li><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_CREDITS_MEDIAELEMENT', $mejs);?></li>
					</ul>
				</div>
			</div>
			<!-- End Tabs -->
		</div>
	</div>
	<!-- End Content -->
</div>
