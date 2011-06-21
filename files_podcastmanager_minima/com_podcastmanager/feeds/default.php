<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task != 'feeds.delete' || confirm('<?php echo JText::_('COM_PODCASTMANAGER_CONFIRM_DELETE', true);?>')) {
			Joomla.submitform(task);
		}
	}
</script>
<?php if( $this->items ): ?>
<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=feeds');?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%" rowspan="2">
					<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
				</th>
				<th rowspan="2%">
					<?php echo JHtml::_('grid.sort',  'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th width="30%" colspan="3">
					<?php echo JText::_('COM_PODCASTMANAGER_HEADING_NUMBER_ITEMS'); ?>
				</th>
				<th width="5%" rowspan="2">
					<?php echo JHtml::_('grid.sort',  'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" rowspan="2">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap" rowspan="2">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
		<?php if( $this->pagination->total >= 10 ): ?>
		<tfoot>
			<tr>
				<th width="1%" rowspan="2">
					<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
				</th>
				<th rowspan="2%">
					<?php echo JHtml::_('grid.sort',  'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th width="30%" colspan="3">
					<?php echo JText::_('COM_PODCASTMANAGER_HEADING_NUMBER_ITEMS'); ?>
				</th>
				<th width="5%" rowspan="2">
					<?php echo JHtml::_('grid.sort',  'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" rowspan="2">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap" rowspan="2">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
		</tfoot>
		<?php endif; ?>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$canCreate	= $user->authorise('core.create',		'com_podcastmanager');
			$canEdit	= $user->authorise('core.edit',			'com_podcastmanager');
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
			$canChange	= $user->authorise('core.edit.state',	'com_podcastmanager') && $canCheckin;
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) {
						echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'feeds.', $canCheckin);
					} ?>
					<?php if ($canEdit) { ?>
						<a href="<?php echo JRoute::_('index.php?option=com_podcastmanager&task=feed.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->name); ?></a>
					<?php } else { 
							echo $this->escape($item->name);
					} ?>
				</td>
				<td class="center btns">
					<a href="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts&feedname='.$item->id.'&filter_published=1');?>">
						<?php echo $item->count_published; ?></a>
				</td>
				<td class="center btns">
					<a href="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts&feedname='.$item->id.'&filter_published=0');?>">
						<?php echo $item->count_unpublished; ?></a>
				</td>
				<td class="center btns">
					<a href="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts&feedname='.$item->id.'&filter_published=-2');?>">
						<?php echo $item->count_trashed; ?></a>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'feeds.', $canChange); ?>
				</td>
				<td class="center nowrap">
					<?php if ($item->language=='*') {
						echo JText::alt('JALL','language');
					} else {
						echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED');
					} ?>
				</td>
				<td class="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php if ($this->pagination->total > 0): ?>
	<div id="pagination-bottom">
		<?php echo $this->pagination->getListFooter(); ?>
	</div>
	<?php endif; ?>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php else: ?>
    <div class="noresults"><p><?php echo JText::_('No items'); ?></p></div>
<?php endif; ?>