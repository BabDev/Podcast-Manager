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

// Add external behaviors
JHtml::_('bootstrap.tooltip');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<?php if ($this->params->get('filter_field') != 'hide' || $this->params->get('show_pagination_limit', 1)) : ?>
		<fieldset class="filters alert alert-info">
			<?php if ($this->params->get('filter_field') != 'hide') : ?>
				<div class="btn-group">
					<label class="filter-search-lbl element-invisible" for="filter-search">
						<?php echo JText::_('COM_PODCASTMANAGER_FILTER_SEARCH_LABEL') . '&#160;'; ?>
					</label>
					<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_PODCASTMANAGER_FILTER_SEARCH_DESCRIPTION'); ?>" />
				</div>
			<?php endif; ?>

			<?php if ($this->params->get('show_pagination_limit', 1)) : ?>
				<div class="btn-group pull-right">
					<label for="limit" class="element-invisible">
						<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
					</label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			<?php endif; ?>
		</fieldset>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="limitstart" value="" />
		<input type="hidden" name="task" value="" />
	<?php endif; ?>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-info"><?php echo JText::_('COM_PODCASTMANAGER_NO_ITEMS'); ?></div>
	<?php else : ?>
		<table class="table table-striped table-bordered table-hover">
			<?php
			$headerTitle  = '';
			$headerDate   = '';
			$headerAuthor = '';
			?>
			<?php if ($this->params->get('show_headings')) : ?>
				<?php
				$headerTitle  = 'headers="feedlist_header_title"';
				$headerDate   = 'headers="feedlist_header_date"';
				$headerAuthor = 'headers="feedlist_header_author"';
				?>
				<thead>
					<tr>
						<th id="feedlist_header_title">
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th id="feedlist_header_date">
							<?php echo JHtml::_('grid.sort', 'JDATE', 'a.publish_up', $listDirn, $listOrder); ?>
						</th>
						<?php if ($this->params->get('show_item_author')) : ?>
							<th id="feedlist_header_author">
								<?php echo JHtml::_('grid.sort', 'JAUTHOR', 'a.itAuthor', $listDirn, $listOrder); ?>
							</th>
						<?php endif; ?>
					</tr>
				</thead>
			<?php endif; ?>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<?php $canEdit = $this->user->authorise('core.edit', 'com_podcastmanager.podcast.' . $item->id); ?>
					<tr class="<?php if ($item->published == 0) : ?>system-unpublished <?php endif; ?>feed-list-row<?php echo $i % 2; ?>" >
						<td <?php echo $headerTitle; ?> class="list-title">
							<p>
								<?php // Compute the correct link
								if ((JPluginHelper::isEnabled('content', 'podcastmanager')) && $this->params->get('show_item_player')) : ?>
									<?php echo $this->escape($item->title) . '<br />' . $item->text; ?>
								<?php else : ?>
									<?php $menuclass = 'podcast' . $this->pageclass_sfx;

									// Check if the file is from off site
									if (preg_match('/^http/', $item->filename)) :
										// The file is off site
										$link = $item->filename;
									else :
										// The file is stored on site
										$link = JUri::base() . $item->filename;
									endif;

									// Process the URL through the helper to get the stat tracking details if applicable
									$link = PodcastManagerHelper::getMediaUrl($link);?>
									<a href="<?php echo $link; ?>" class="<?php echo $menuclass; ?>" rel="nofollow">
										<?php echo $this->escape($item->title); ?>
									</a>
								<?php endif; ?>
							</p>
							<?php if ($canEdit) : ?>
								<ul class="actions">
									<li class="edit-icon">
										<?php echo JHtml::_('icon.podcastedit', $item, $this->params); ?>
									</li>
								</ul>
							<?php endif; ?>

							<?php if (($this->params->get('show_item_description')) && ($item->itSummary)) : ?>
								<p><?php echo nl2br($item->itSummary); ?></p>
							<?php endif; ?>

							<?php if (($this->params->get('show_item_image')) && ($item->itImage)) : ?>
								<p><?php echo JHtml::_('image', $item->itImage, $item->title); ?></p>
							<?php endif; ?>
						</td>
						<td <?php echo $headerDate; ?> class="list-date small">
							<?php echo JHtml::_('date', $item->publish_up, JText::_('DATE_FORMAT_LC4')); ?>
						</td>
						<?php if ($this->params->get('show_item_author')) : ?>
							<td <?php echo $headerAuthor; ?> class="list-author">
								<?php echo $item->itAuthor; ?>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pagesTotal') > 1)) : ?>
			<div class="pagination">
				<?php if ($this->params->def('show_pagination_results', 1)) : ?>
					<p class="counter pull-right">
						<?php echo $this->pagination->getPagesCounter(); ?>
					</p>
				<?php endif; ?>

				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif;?>
	<?php endif; ?>
</form>
