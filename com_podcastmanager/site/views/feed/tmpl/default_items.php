<?php
/**
 * @version		$Id: default_items.php 13471 2009-11-12 00:38:49Z eddieajau
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
// Code to support edit links for weblinks
// Create a shortcut for params.
$params = &$this->item->params;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::core();

// Get the user object.
$user = JFactory::getUser();
// Check if user is allowed to add/edit based on weblinks permissinos.
$canEdit = $user->authorise('core.edit', 'com_podcastmanager');
$canCreate = $user->authorise('core.create', 'com_podcastmanager');
$canEditState = $user->authorise('core.edit.state', 'com_podcastmanager');

$n = count($this->items);
//$listOrder	= $this->escape($this->state->get('list.ordering'));
//$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<?php if (empty($this->items)) : ?>
	<p> <?php echo JText::_('COM_PODCASTMANAGER_NO_ITEMS'); ?></p>
<?php else : ?>

<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php //if ($this->params->get('show_pagination_limit')) : ?>
<!-- 	<fieldset class="filters">
		<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
		<div class="display-limit">
			<?php //echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php //echo $this->pagination->getLimitBox(); ?>
		</div>
		<input type="hidden" name="filter_order" value="<?php //echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php //echo $listDirn; ?>" />
		</fieldset>  -->
	<?php //endif; ?>

	<table class="category">
		<?php //if ($this->params->get('show_headings')==1) : ?>

		<thead><tr>

			<th class="title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
			</th>
			<th class="title">
					<?php echo JHtml::_('grid.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
			</th>
		</tr>
	</thead>
	<?php //endif; ?>
	<tbody>
	<?php foreach ($this->items as $i => $item) : ?>
		<?php if ($this->items[$i]->state == 0) : ?>
			<tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
		<?php else: ?>
			<tr class="cat-list-row<?php echo $i % 2; ?>" >
		<?php endif; ?>

			<td class="title">
			<p>
				<?php //if ($this->params->get('icons') == 0) : ?>
					 <?php //echo JText::_('COM_WEBLINKS_LINK'); ?>
				<?php //elseif ($this->params->get('icons') == 1) : ?>
					<?php //if (!$this->params->get('link_icons')) : ?>
						<?php //echo JHtml::_('image','system/'.$this->params->get('link_icons', 'weblink.png'), JText::_('COM_WEBLINKS_LINK'), NULL, true); ?>
					<?php //else: ?>
						<?php //echo '<img src="'.$this->params->get('link_icons').'" alt="'.JText::_('COM_WEBLINKS_LINK').'" />'; ?>
					<?php //endif; ?>
				<?php //endif; ?>
				<?php
					// Compute the correct link
					$menuclass = 'podcast'.$this->pageclass_sfx;
					$link = JURI::base().$item->filename;
					echo '<a href="'.$link.'" class="'.$menuclass.'" rel="nofollow">'.
					$this->escape($item->title).'</a><br />'.$this->escape($item->itSummary);
				?>
				<?php // Code to add the edit link for the podcast. ?>

						<?php //if ($canEdit) : ?>
							<ul class="actions">
								<li class="edit-icon">
									<?php //echo JHtml::_('icon.edit', $item, $params); ?>
								</li>
							</ul>
						<?php //endif; ?>
			</p>

			<?php //if (($this->params->get('show_link_description')) AND ($item->description !='')): ?>
				<p>
				<?php //echo nl2br($item->description); ?>
				</p>
			<?php //endif; ?>
		</td>
		<td class="hits">
			<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
		</td>
	</tr>
	<?php endforeach; ?>
</tbody>
</table>

	<?php // Code to add a link to submit a weblink. ?>
	<?php /* if ($canCreate) : // TODO This is not working due to some problem in the router, I think. Ref issue #23685 ?>
		<?php echo JHtml::_('icon.create', $item, $item->params); ?>
 	<?php  endif; */ ?>
		<?php //if ($this->params->get('show_pagination')) : ?>
		 <div class="pagination">
			<?php //if ($this->params->def('show_pagination_results', 1)) : ?>
				<p class="counter">
					<?php //echo $this->pagination->getPagesCounter(); ?>
				</p>
			<?php //endif; ?>
				<?php //echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php //endif; ?>
	</form>
<?php endif; ?>