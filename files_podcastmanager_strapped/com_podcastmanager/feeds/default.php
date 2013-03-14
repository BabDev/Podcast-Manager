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

// Load the tooltip behavior.
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

// Get info about the site to build a proper front end URL to display for the RSS links
$uri      = JUri::getInstance();
$protocol = $uri->getScheme();
$domain   = $uri->getHost();
$base     = $protocol . '://' . $domain;

$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById('sortTable');
		direction = document.getElementById('directionTable');
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}

		Joomla.tableOrdering(order, dirn, '');
	}

	Joomla.submitbutton = function(pressbutton) {
		if (pressbutton == 'feeds.delete') {
			if (confirm(Joomla.JText._('COM_PODCASTMANAGER_CONFIRM_FEED_DELETE'))) {
				Joomla.submitform(pressbutton);
			} else {
				return false;
			}
		}

		Joomla.submitform(pressbutton);
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=feeds');?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<div id="filter-bar" class="btn-toolbar">
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>
		<div class="clearfix"> </div>

		<table class="table table-striped">
			<thead>
				<tr>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="5%" style="min-width: 55px" class="center">
						<?php echo JText::_('JSTATUS'); ?>
					</th>
					<th>
						<?php echo JText::_('JGLOBAL_TITLE'); ?>
					</th>
					<th width="10%" class="hidden-phone">
						<?php echo JText::_('COM_PODCASTMANAGER_HEADING_PUBLISHED_ITEMS'); ?>
					</th>
					<th width="10%" class="hidden-phone">
						<?php echo JText::_('COM_PODCASTMANAGER_HEADING_UNPUBLISHED_ITEMS'); ?>
					</th>
					<th width="10%" class="hidden-phone">
						<?php echo JText::_('COM_PODCASTMANAGER_HEADING_TRASHED_ITEMS'); ?>
					</th>
					<th width="5%" class="hidden-phone">
						<?php echo JText::_('JGRID_HEADING_LANGUAGE'); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<?php echo JText::_('JGRID_HEADING_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php if (count($this->items) == 0) : ?>
				<tr class="row0">
					<td class="center" colspan="8">
						<?php echo JText::_('COM_PODCASTMANAGER_NO_RECORDS_FOUND'); ?>
					</td>
				</tr>
			<?php else : ?>
			<?php foreach ($this->items as $i => $item) :
				$canCreate	= $user->authorise('core.create',     'com_podcastmanager.feed.' . $item->id);
				$canEdit	= $user->authorise('core.edit',       'com_podcastmanager.feed.' . $item->id);
				$canCheckin	= $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
				$canEditOwn	= $user->authorise('core.edit.own',   'com_podcastmanager.feed.' . $item->id) && $item->created_by == $userId;
				$canChange	= $user->authorise('core.edit.state', 'com_podcastmanager.feed.' . $item->id) && $canCheckin;
				$rssRoute   = PodcastManagerHelperRoute::getFeedRssRoute($item->id);
			?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'feeds.', $canChange); ?>
					</td>
					<td>
						<?php if ($item->checked_out) :
							echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'feeds.', $canCheckin);
						endif;
						if ($canEdit || $canEditOwn) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_podcastmanager&task=feed.edit&id=' . $item->id); ?>">
								<?php echo $this->escape($item->name); ?>
							</a>
						<?php else : ?>
							<span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->name); ?></span>
						<?php endif; ?>
						<p class="small">
							<span><?php echo JText::_('COM_PODCASTMANAGER_RSS_FEED_URL') ?></span>
							<a href="<?php echo $base . PodcastManagerHelper::getFeedRoute($rssRoute); ?>" target="_blank">
								<?php echo $base . PodcastManagerHelper::getFeedRoute($rssRoute); ?>
							</a>
						</p>
					</td>
					<td class="center btns hidden-phone">
						<a class="badge badge-success" href="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts&feedname=' . $item->id . '&filter_published=1');?>">
							<?php echo $item->count_published; ?>
						</a>
					</td>
					<td class="center btns hidden-phone">
						<a class="badge" href="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts&feedname=' . $item->id . '&filter_published=0');?>">
							<?php echo $item->count_unpublished; ?>
						</a>
					</td>
					<td class="center btns hidden-phone">
						<a class="badge badge-error" href="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts&feedname=' . $item->id . '&filter_published=-2');?>">
							<?php echo $item->count_trashed; ?>
						</a>
					</td>
					<td class="center hidden-phone">
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
				<?php endforeach;
			endif; ?>
			</tbody>
		</table>
		<?php echo $this->pagination->getListFooter(); ?>

		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
</form>
