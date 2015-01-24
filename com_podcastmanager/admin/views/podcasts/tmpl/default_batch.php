<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
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
<fieldset class="batch">
	<legend><?php echo JText::_('COM_PODCASTMANAGER_BATCH_OPTIONS');?></legend>
	<p><?php echo JText::_('COM_PODCASTMANAGER_BATCH_TIP'); ?></p>
	<?php echo JHtml::_('batch.language'); ?>

	<?php if (version_compare(JVERSION, '3.1', 'ge')) : ?>
	<?php echo JHtml::_('batch.tag'); ?>
	<?php endif; ?>

	<?php if ($published >= 0) : ?>
	<label id="batch-choose-action-lbl" for="batch-choose-action">
		<?php echo JText::_('COM_PODCASTMANAGER_BATCH_FEED_LABEL'); ?>
	</label>
	<fieldset id="batch-choose-action" class="combo">
		<select name="batch[feed_id]" class="inputbox" id="batch-feed-id">
			<option value=""><?php echo JText::_('JSELECT') ?></option>
			<?php echo JHtml::_('select.options', JHtml::_('podcast.feeds'));?>
		</select>
		<?php echo JHtml::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
	</fieldset>
	<?php endif; ?>

	<button type="submit" onclick="submitbutton('podcast.batch');">
		<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.id('batch-feed-id').value='';document.id('batch-language').value='';document.id('batch-tag').value=''">
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>
