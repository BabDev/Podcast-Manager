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

$user      = JFactory::getUser();
$userId    = $user->id;
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

// Get info about the site to build a proper front end URL to display for the RSS links
$uri      = JUri::getInstance();
$protocol = $uri->getScheme();
$domain   = $uri->getHost();
$base     = $protocol . '://' . $domain;

$js = <<< JS
Joomla.submitbutton = function(task) {
	if (task != 'feeds.delete' || confirm(Joomla.JText._('COM_PODCASTMANAGER_CONFIRM_FEED_DELETE'))) {
		Joomla.submitform(task);
	}
};
JS;

JFactory::getDocument()->addScriptDeclaration($js);
?>
<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=feeds'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_PODCASTMANAGER_FILTER_FEEDS_SEARCH_DESCRIPTION');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_PODCASTMANAGER_FILTER_FEEDS_SEARCH_DESCRIPTION'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::_('tooltipText', 'COM_PODCASTMANAGER_FILTER_FEEDS_SEARCH_DESCRIPTION'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::_('tooltipText', 'JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::_('tooltipText', 'JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		</div>
		<div class="clearfix"> </div>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="1%">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="5%" style="min-width: 55px" class="center">
							<?php echo JText::_('JSTATUS'); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap center hidden-phone">
							<?php echo JText::_('COM_PODCASTMANAGER_HEADING_PUBLISHED_ITEMS'); ?>
						</th>
						<th width="10%" class="nowrap center hidden-phone">
							<?php echo JText::_('COM_PODCASTMANAGER_HEADING_UNPUBLISHED_ITEMS'); ?>
						</th>
						<th width="10%" class="nowrap center hidden-phone">
							<?php echo JText::_('COM_PODCASTMANAGER_HEADING_TRASHED_ITEMS'); ?>
						</th>
						<th width="5%" class="nowrap center hidden-phone">
							<?php echo JText::_('JGRID_HEADING_LANGUAGE'); ?>
						</th>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="8">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$canCreate	= $user->authorise('core.create',     'com_podcastmanager.feed.' . $item->id);
					$canEdit	= $user->authorise('core.edit',       'com_podcastmanager.feed.' . $item->id);
					$canCheckin	= $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canEditOwn	= $user->authorise('core.edit.own',   'com_podcastmanager.feed.' . $item->id) && $item->created_by == $userId;
					$canChange	= $user->authorise('core.edit.state', 'com_podcastmanager.feed.' . $item->id) && $canCheckin;
					$rssRoute   = PodcastManagerHelperRoute::getFeedRssRoute($item->id);
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'feeds.', $canChange); ?>
						</td>
						<td>
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'feeds.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit || $canEditOwn) : ?>
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
							<?php if ($item->language == '*') : ?>
								<?php echo JText::alt('JALL', 'language'); ?>
							<?php else : ?>
								<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
							<?php endif; ?>
						</td>
						<td class="center">
							<?php echo $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif;?>

		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
</form>
