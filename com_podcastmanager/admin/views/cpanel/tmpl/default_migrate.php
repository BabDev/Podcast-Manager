<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  files_podcastmanager_strapped
 *
 * @copyright   Copyright (C) 2011-2014 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Reset the title
JToolBarHelper::title(JText::_('COM_PODCASTMANAGER_VIEW_MIGRATION_TITLE'), 'podcastmanager.png');

// Site addresses to be processed outside JText
$babdev = '<a href="http://www.babdev.com/extensions/podcast-manager" target="_blank">http://www.babdev.com/extensions/podcast-manager</a>';

// Initialize the errors array
$errors = array();
?>
<div class="row-fluid">
	<!-- Begin Content -->
	<div class="span12">
		<h5><?php echo JText::sprintf('COM_PODCASTMANAGER_INFO_THANK_YOU_FOR_INSTALLING', $babdev); ?></h5>

		<div class="well">
			<h2><?php echo JText::_('COM_PODCASTMANAGER_INFO_MIGRATION_TITLE'); ?></h2>
			<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_MIGRATION_INTRO'); ?></p>

			<?php if (count($this->migrationErrors)) : ?>
				<h4><?php echo JText::_('COM_PODCASTMANAGER_INFO_MIGRATION_ERRORS_TITLE'); ?></h4>
				<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_MIGRATION_ERRORS_PARAGRAPH'); ?></p>
				<ul>
					<?php foreach ($this->migrationErrors as $key => $message) : ?>
						<?php $errors[$key] = true; ?>
						<li><?php echo $message; ?></li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<?php $errors['noLayouts'] = true; ?>
				<h4><?php echo JText::_('COM_PODCASTMANAGER_INFO_MIGRATION_NO_ERRORS_TITLE'); ?></h4>
				<p><?php echo JText::_('COM_PODCASTMANAGER_INFO_MIGRATION_NO_ERRORS_PARAGRAPH'); ?></p>
			<?php endif; ?>

			<a href="<?php echo JRoute::_('index.php?option=com_podcastmanager&task=migrate&migrationTasks=' . base64_encode(json_encode($errors))); ?>" type="button" class="btn btn-large btn-primary">
				<?php echo JText::_('COM_PODCASTMANAGER_MIGRATION_BUTTON'); ?>
			</a>
		</div>
	</div>
	<!-- End Content -->
</div>
