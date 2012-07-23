<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  files_podcastmanager_strapped
 *
 * @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
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
<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts');?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<!-- Begin Sidebar -->
		<div id="sidebar" class="span2">
			<div class="sidebar-nav">
				<?php
					// Display the submenu position modules
					$this->modules = JModuleHelper::getModules('submenu');
					foreach ($this->modules as $module)
					{
						$output = JModuleHelper::renderModule($module);
						$params = new JRegistry;
						$params->loadString($module->params);
						echo $output;
					}
				?>
				<hr />
				<div class="filter-select">
					<h4 class="page-header"><?php echo JText::_('JSEARCH_FILTER_LABEL');?></h4>
					<select name="filter_published" class="span12 small" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', $this->states), 'value', 'text', $this->state->get('filter.published'), true);?>
					</select>
					<hr class="hr-condensed" />
					<select name="filter_feedname" class="span12 small" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('COM_PODCASTMANAGER_SELECT_FEEDNAME');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('podcast.feeds'), 'value', 'text', $this->state->get('filter.feedname'));?>
					</select>
					<hr class="hr-condensed" />
					<select name="filter_language" class="span12 small" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
					</select>
				</div>
			</div>
		</div>
		<!-- End Sidebar -->
		<!-- Begin Content -->
		<div class="span10">
			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<input type="text" name="filter_search" placeholder="<?php echo JText::_('COM_PODCASTMANAGER_FILTER_SEARCH_DESCRIPTION'); ?>" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
				</div>
				<div class="btn-group pull-left">
					<button class="btn tip" type="submit" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
					<button class="btn tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
				</div>
			</div>
			<div class="clearfix"> </div>

			<table class="table table-striped">
				<thead>
					<tr>
						<th width="1%">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'COM_PODCASTMANAGER_HEADING_FEEDNAME', 'a.feedname', $listDirn, $listOrder); ?>
						</th>
						<th width="5%">
							<?php echo JHtml::_('grid.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
						</th>
						<th width="5%">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
						</th>
						<th width="1%">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php if (count($this->items) == 0)
				{ ?>
					<tr class="row0">
						<td class="center" colspan="6">
							<?php echo JText::_('COM_PODCASTMANAGER_NO_RECORDS_FOUND'); ?>
						</td>
					</tr>
				<?php }
				else
				{ ?>
				<?php foreach ($this->items as $i => $item)
				{
					$canCreate	= $user->authorise('core.create',		'com_podcastmanager.feed.' . $item->feedname);
					$canEdit	= $user->authorise('core.edit',			'com_podcastmanager.podcast.' . $item->id);
					$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
					$canEditOwn	= $user->authorise('core.edit.own',		'com_podcastmanager.podcast.' . $item->id) && $item->created_by == $userId;
					$canChange	= $user->authorise('core.edit.state',	'com_podcastmanager.podcast.' . $item->id) && $canCheckin;
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td>
							<?php if ($item->checked_out)
							{
								echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'podcasts.', $canCheckin);
							}
							echo JHtml::_('jgrid.published', $item->published, $i, 'podcasts.', $canChange);
							if ($canEdit || $canEditOwn)
							{ ?>
								<a href="<?php echo JRoute::_('index.php?option=com_podcastmanager&task=podcast.edit&id=' . (int) $item->id); ?>">
									<?php echo $this->escape($item->title); ?>
								</a>
							<?php }
							else
							{
								echo $this->escape($item->title);
							} ?>
						</td>
						<td class="center">
							<?php echo $item->feed_name ? $this->escape($item->feed_name) : JText::_('JNONE'); ?>
						</td>
						<td class="center btns">
							<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
						</td>
						<td class="center">
							<?php if ($item->language == '*')
							{
								echo JText::alt('JALL', 'language');
							}
							else
							{
								echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED');
							} ?>
						</td>
						<td class="center">
							<?php echo $item->id; ?>
						</td>
					</tr>
					<?php }
				} ?>
				</tbody>
			</table>
			<?php // Load the batch processing form.
			echo $this->loadTemplate('batch'); ?>

			<div>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
		<!-- End Content -->
	</div>
</form>
