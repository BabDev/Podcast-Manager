<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  files_podcastmanager_hathor
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Styles specific to the podcasts view
JHtml::stylesheet('administrator/templates/hathor/html/com_podcastmanager/podcasts/podcasts.css', false, false, false);

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		if (pressbutton == 'podcasts.unpublish') {
			if (confirm(Joomla.JText._('COM_PODCASTMANAGER_CONFIRM_PODCAST_UNPUBLISH'))) {
				Joomla.submitform(pressbutton);
			} else {
				return false;
			}
		}

		Joomla.submitform(pressbutton);
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
	<legend class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></legend>
		<div class="filter-search">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_WEBLINKS_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select">
			<label class="selectlabel" for="filter_published">
				<?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?>
			</label>
			<select name="filter_published" id="filter_published" class="inputbox">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', $this->states), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>

			<label class="selectlabel" for="filter_feedname">
				<?php echo JText::_('COM_PODCASTMANAGER_SELECT_FEEDNAME'); ?>
			</label>
			<select name="filter_feedname" id="filter_feedname" class="inputbox">
				<option value=""><?php echo JText::_('COM_PODCASTMANAGER_SELECT_FEEDNAME');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('podcast.feeds'), 'value', 'text', $this->state->get('filter.feedname'), true);?>
			</select>

			<label class="selectlabel" for="filter_language">
				<?php echo JText::_('JOPTION_SELECT_LANGUAGE'); ?>
			</label>
			<select name="filter_language" id="filter_language" class="inputbox">
				<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>

			<button type="button" id="filter-go" onclick="this.form.submit();">
				<?php echo JText::_('JSUBMIT'); ?>
			</button>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th class="checkmark-col">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap state-col">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th class="width-10">
					<?php echo JHtml::_('grid.sort', 'COM_PODCASTMANAGER_HEADING_FEEDNAME', 'a.feedname', $listDirn, $listOrder); ?>
				</th>
				<th class="width-10">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_CREATED_DATE', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<th class="width-10">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_PUBLISHED_DATE', 'a.publish_up', $listDirn, $listOrder); ?>
				</th>
				<th class="width-5">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap id-col">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>

		<tbody>
		<?php if (count($this->items) == 0) : ?>
			<tr class="row0">
				<td align="center" colspan="7">
					<?php echo JText::_('COM_PODCASTMANAGER_NO_RECORDS_FOUND'); ?>
				</td>
			</tr>
		<?php else : ?>
		<?php foreach ($this->items as $i => $item) :
			$canCreate	= $user->authorise('core.create',     'com_podcastmanager.feed.' . $item->feedname);
			$canEdit	= $user->authorise('core.edit',       'com_podcastmanager.podcast.' . $item->id);
			$canCheckin	= $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
			$canEditOwn	= $user->authorise('core.edit.own',   'com_podcastmanager.podcast.' . $item->id) && $item->created_by == $userId;
			$canChange	= $user->authorise('core.edit.state', 'com_podcastmanager.podcast.' . $item->id) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) :
						echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'podcasts.', $canCheckin);
					endif;
					if ($canEdit || $canEditOwn) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_podcastmanager&task=podcast.edit&id=' . (int) $item->id); ?>">
							<?php echo $this->escape($item->title); ?>
						</a>
					<?php else :
						echo $this->escape($item->title);
					endif; ?>
					<p class="smallsub">
						<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
					</p>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'podcasts.', $canChange); ?>
				</td>
				<td class="center">
					<?php echo $item->feed_name ? $this->escape($item->feed_name) : JText::_('JNONE'); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('date', $item->publish_up, JText::_('DATE_FORMAT_LC4')); ?>
				</td>
				<td class="center">
					<?php if ($item->language == '*') :
						echo JText::alt('JALL', 'language');
					else :
						echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED');
					endif; ?>
				</td>
				<td class="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach;
		endif; ?>
		</tbody>
	</table>
	<?php // Load the batch processing form.
	echo $this->loadTemplate('batch');
	echo $this->pagination->getListFooter(); ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
