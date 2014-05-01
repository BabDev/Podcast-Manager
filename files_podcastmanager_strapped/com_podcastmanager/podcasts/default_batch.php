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

$options = array(
	JHtml::_('select.option', 'c', JText::_('JLIB_HTML_BATCH_COPY')),
	JHtml::_('select.option', 'm', JText::_('JLIB_HTML_BATCH_MOVE'))
);
$published	= $this->state->get('filter.published');
?>
<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3><?php echo JText::_('COM_PODCASTMANAGER_BATCH_OPTIONS');?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo JText::_('COM_PODCASTMANAGER_BATCH_TIP'); ?></p>
		<div class="control-group">
			<div class="controls">
				<?php echo JHtml::_('batch.language'); ?>
			</div>
		</div>
		<?php if (version_compare(JVERSION, '3.1', 'ge')) : ?>
		<div class="control-group">
			<div class="controls">
				<?php echo JHtml::_('batch.tag');?>
			</div>
		</div>
		<?php endif; ?>
		<?php if ($published >= 0) : ?>
		<div class="control-group">
			<label id="batch-choose-action-lbl" for="batch-feed-id" class="control-label">
				<?php echo JText::_('COM_PODCASTMANAGER_BATCH_FEED_LABEL'); ?>
			</label>
			<div id="batch-choose-action" class="combo controls">
				<select name="batch[feed_id]" class="inputbox" id="batch-feed-id">
					<option value=""><?php echo JText::_('JSELECT') ?></option>
					<?php echo JHtml::_('select.options', JHtml::_('podcast.feeds'));?>
				</select>
			</div>
		</div>
		<div class="control-group radio">
			<?php echo JHtml::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
		</div>
		<?php endif; ?>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch-feed-id').value='';document.id('batch-language-id').value='';document.id('batch-tag-id').value=''" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('podcast.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>
