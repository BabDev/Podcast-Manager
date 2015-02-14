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

// Load the tooltip behavior.
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->id;
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task) {
		if (task != 'podcasts.unpublish' || confirm('" . JText::_('COM_PODCASTMANAGER_CONFIRM_PODCAST_UNPUBLISH', true) . "')) {
			Joomla.submitform(task);
		}
	};
");
?>
<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts');?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php // Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="podcastList">
				<thead>
					<tr>
						<th width="1%" class="center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" style="min-width:55px" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_PODCASTMANAGER_HEADING_FEEDNAME', 'a.feedname', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_CREATED_DATE', 'a.created', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_PUBLISHED_DATE', 'a.publish_up', $listDirn, $listOrder); ?>
						</th>
						<th width="5%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$canCreate	= $user->authorise('core.create',     'com_podcastmanager.feed.' . $item->feedname);
					$canEdit	= $user->authorise('core.edit',       'com_podcastmanager.podcast.' . $item->id);
					$canCheckin	= $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canEditOwn	= $user->authorise('core.edit.own',   'com_podcastmanager.podcast.' . $item->id) && $item->created_by == $userId;
					$canChange	= $user->authorise('core.edit.state', 'com_podcastmanager.podcast.' . $item->id) && $canCheckin;
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'podcasts.', $canChange); ?>
						</td>
						<td>
							<?php if ($item->checked_out) :
								echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'podcasts.', $canCheckin);
							endif;
							if ($item->language == '*') :
								$language = JText::alt('JALL', 'language');
							else :
								$language = $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED');
							endif;
							if ($canEdit || $canEditOwn) : ?>
								<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_podcastmanager&task=podcast.edit&id=' . (int) $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
									<?php echo $this->escape($item->title); ?>
								</a>
							<?php else : ?>
								<span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->title); ?></span>
							<?php endif; ?>
							<span class="small break-word">
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
							</span>
						</td>
						<td class="center hidden-phone">
							<?php echo $item->feed_name ? $this->escape($item->feed_name) : JText::_('JNONE'); ?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo JHtml::_('date', $item->publish_up, JText::_('DATE_FORMAT_LC4')); ?>
						</td>
						<td class="small hidden-phone">
							<?php if ($item->language == '*') :
								echo JText::alt('JALL', 'language');
							else :
								echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED');
							endif; ?>
						</td>
						<td class="center hidden-phone">
							<?php echo $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php echo $this->pagination->getListFooter(); ?>
		<?php // Load the batch processing form.
		echo $this->loadTemplate('batch'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
