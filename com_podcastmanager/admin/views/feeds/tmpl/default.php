<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
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

// Get info about the URL to build a proper one to display for the RSS links
$uri = JURI::getInstance();
$protocol = $uri->getScheme();
$domain = $uri->getHost();
$base = $protocol . '://' . $domain;
?>
<script type="text/javascript">
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
	<fieldset id="filter-bar">
		<div class="filter-select fltrt">
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', $this->states), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%" rowspan="2">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th rowspan="2">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th width="30%" colspan="3">
					<?php echo JText::_('COM_PODCASTMANAGER_HEADING_NUMBER_ITEMS'); ?>
				</th>
				<th width="5%" rowspan="2">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" rowspan="2">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap" rowspan="2">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
			<tr>
				<th width="10%">
					<?php echo JText::_('COM_PODCASTMANAGER_HEADING_PUBLISHED_ITEMS'); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('COM_PODCASTMANAGER_HEADING_UNPUBLISHED_ITEMS'); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('COM_PODCASTMANAGER_HEADING_TRASHED_ITEMS'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php if (count($this->items) == 0)
		{ ?>
			<tr class="row0">
				<td align="center" colspan="8">
					<?php echo JText::_('COM_PODCASTMANAGER_NO_RECORDS_FOUND'); ?>
				</td>
			</tr>
		<?php }
		else
		{ ?>
		<?php foreach ($this->items as $i => $item)
		{
			$canCreate	= $user->authorise('core.create',		'com_podcastmanager.feed.' . $item->id);
			$canEdit	= $user->authorise('core.edit',			'com_podcastmanager.feed.' . $item->id);
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
			$canEditOwn	= $user->authorise('core.edit.own',		'com_podcastmanager.feed.' . $item->id) && $item->created_by == $userId;
			$canChange	= $user->authorise('core.edit.state',	'com_podcastmanager.feed.' . $item->id) && $canCheckin;
			$rssRoute = PodcastManagerHelperRoute::getFeedRssRoute($item->id);
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out)
					{
						echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'feeds.', $canCheckin);
					}
					if ($canEdit || $canEditOwn)
					{ ?>
						<a href="<?php echo JRoute::_('index.php?option=com_podcastmanager&task=feed.edit&id=' . $item->id); ?>">
							<?php echo $this->escape($item->name); ?>
						</a>
					<?php }
					else
					{
						echo $this->escape($item->name);
					} ?>
					<p class="smallsub">
						<span><?php echo JText::_('COM_PODCASTMANAGER_RSS_FEED_URL') ?></span>
						<a href="<?php echo $base . PodcastManagerHelper::getFeedRoute($rssRoute); ?>" target="_blank">
							<?php echo $base . PodcastManagerHelper::getFeedRoute($rssRoute); ?>
						</a>
					</p>
				</td>
				<td class="center btns">
					<a href="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts&feedname=' . $item->id . '&filter_published=1');?>">
						<?php echo $item->count_published; ?>
					</a>
				</td>
				<td class="center btns">
					<a href="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts&feedname=' . $item->id . '&filter_published=0');?>">
						<?php echo $item->count_unpublished; ?>
					</a>
				</td>
				<td class="center btns">
					<a href="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts&feedname=' . $item->id . '&filter_published=-2');?>">
						<?php echo $item->count_trashed; ?>
					</a>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'feeds.', $canChange); ?>
				</td>
				<td class="center nowrap">
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

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
