<?php
/**
 * @version		$Id: default_batch.php 21663 2011-06-23 13:51:35Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Add the HTML Helper
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$options = array(
	JHtml::_('select.option', 'c', JText::_('JLIB_HTML_BATCH_COPY')),
	JHtml::_('select.option', 'm', JText::_('JLIB_HTML_BATCH_MOVE'))
);
$published	= $this->state->get('filter.published');
$extension	= $this->escape($this->state->get('filter.extension'));
?>
<fieldset class="batch">
	<legend><?php echo JText::_('COM_CATEGORIES_BATCH_OPTIONS');?></legend>
	<?php if ($published >= 0) : ?>
	<label id="batch-choose-action-lbl" for="batch-feed-id">
		<?php echo JText::_('COM_PODCASTMANAGER_BATCH_FEED_LABEL'); ?>
	</label>
	<select name="batch[feed_id]" class="inputbox" id="batch-feed-id">
		<option value=""><?php echo JText::_('JSELECT') ?></option>
		<?php echo JHtml::_('select.options', JHtml::_('podcast.feeds', $extension, array('published' => $published)));?>
	</select>
	<?php echo JHtml::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
	<?php endif; ?>

	<button type="submit" onclick="submitbutton('podcast.batch');">
		<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.id('batch-feed-id').value='';document.id('batch-access').value=''">
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>