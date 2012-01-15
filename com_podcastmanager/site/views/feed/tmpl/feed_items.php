<?php
/**
* Podcast Manager for Joomla!
*
* @package     PodcastManager
* @subpackage  com_podcastmanager
*
* @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
* @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

defined('_JEXEC') or die;

$params = &$this->item->params;

// Add external behaviors
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::core();

// Get the user object.
$user = JFactory::getUser();

// Check if user is allowed to add/edit based on component permissinos.
$canEdit = $user->authorise('core.edit', 'com_podcastmanager');
$canCreate = $user->authorise('core.create', 'com_podcastmanager');
$canEditState = $user->authorise('core.edit.state', 'com_podcastmanager');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->params->get('show_headings') || $this->params->get('filter_field') != 'hide' || $this->params->get('show_pagination_limit')) :?>
 	<fieldset class="filters">
		<?php if ($this->params->get('filter_field') != 'hide') :?>
		<legend class="hidelabeltxt">
			<?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?>
		</legend>

		<div class="filter-search">
			<label class="filter-search-lbl" for="filter-search"><?php echo JText::_('COM_PODCASTMANAGER_FILTER_SEARCH_LABEL') . '&#160;'; ?></label>
			<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_PODCASTMANAGER_FILTER_SEARCH_DESCRIPTION'); ?>" />
		</div>
		<?php endif; ?>

		<?php if ($this->params->get('show_pagination_limit')) : ?>
		<div class="display-limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<?php endif; ?>

		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="limitstart" value="" />
	</fieldset>
	<?php endif; ?>

	<?php if (empty($this->items)) : ?>
	<p><?php echo JText::_('COM_PODCASTMANAGER_NO_ITEMS'); ?></p>
	<?php else : ?>

	<table class="category">
		<?php if ($this->params->get('show_headings')) : ?>
		<thead>
			<tr>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JDATE', 'a.publish_up', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<?php endif; ?>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
		if ($this->items[$i]->published == 0) : ?>
			<tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
		<?php else : ?>
			<tr class="cat-list-row<?php echo $i % 2; ?>" >
		<?php endif; ?>
				<td class="title">
					<p>
					<?php // Compute the correct link
					if ((JPluginHelper::isEnabled('content', 'podcastmanager')) && $this->params->get('show_item_player')) {
						echo $item->text;
					} else {
						$menuclass = 'podcast'.$this->pageclass_sfx;
						$link = JURI::base().$item->filename; ?>
						<a href="<?php echo $link; ?>" class="<?php echo $menuclass; ?>" rel="nofollow">
						<?php echo $this->escape($item->title); ?></a>
					<?php } ?></p>
					<?php if ($canEdit) : ?>
					<ul class="actions">
						<li class="edit-icon">
							<?php echo JHtml::_('icon.podcastedit', $item, $params); ?>
						</li>
					</ul>
					<?php endif; ?>

					<?php if (($this->params->get('show_item_description')) AND ($item->itSummary)): ?>
					<p><?php echo nl2br($item->itSummary); ?></p>
					<?php endif; ?>
				</td>
				<td>
					<?php echo JHtml::_('date', $item->publish_up, JText::_('DATE_FORMAT_LC4')); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php
		if ($this->params->get('show_pagination')) : ?>
		 <div class="pagination">
			<?php if ($this->params->def('show_pagination_results', 1)) : ?>
				<p class="counter">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</p>
			<?php endif;
				echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif; ?>
	</form>
<?php endif; ?>
