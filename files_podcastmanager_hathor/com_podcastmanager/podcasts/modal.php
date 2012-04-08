<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  files_podcastmanager_hathor
 *
 * @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Check for a token if accessing from front end
if (JFactory::getApplication()->isSite())
{
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
}

JHtml::_('behavior.tooltip');

$input = JFactory::getApplication()->input;
$function	= $input->get('function', 'PodcastManagerSelectPodcast', 'cmd');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts&layout=modal&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
	<legend class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></legend>
		<div class="filter-search">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
			<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="filter-select">
			<label class="selectlabel" for="filter_published"><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></label>
			<select name="filter_published" class="inputbox" id="filter_published">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', $this->states), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>

			<label class="selectlabel" for="filter_feedname"><?php echo JText::_('COM_PODCASTMANAGER_SELECT_FEEDNAME'); ?></label>
			<select name="filter_feedname" id="filter_feedname" class="inputbox">
				<option value=""><?php echo JText::_('COM_PODCASTMANAGER_SELECT_FEEDNAME');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('podcast.feeds'), 'value', 'text', $this->state->get('filter.feedname'), true);?>
			</select>

			<label class="selectlabel" for="filter_language"><?php echo JText::_('JOPTION_SELECT_LANGUAGE'); ?></label>
			<select name="filter_language" class="inputbox" id="filter_language">
				<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>

			<button type="button" id="filter-go" onclick="this.form.submit();">
				<?php echo JText::_('JSUBMIT'); ?></button>
		</div>
	</fieldset>

	<table class="adminlist modal">
		<thead>
			<tr>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_PODCASTMANAGER_HEADING_FEEDNAME', 'a.feedname', $listDirn, $listOrder); ?>
				</th>
				<th class="title language-col">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap id-col">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>

		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<th>
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo (int) $item->id; ?>');">
						<?php echo $this->escape($item->title); ?></a>
				</th>
				<td class="center">
					<?php echo $item->feed_name ? $this->escape($item->feed_name) : JText::_('JNONE'); ?>
				</td>
				<td class="center">
					<?php if ($item->language == '*'):?>
						<?php echo JText::alt('JALL', 'language'); ?>
					<?php else:?>
						<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
					<?php endif;?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php echo $this->pagination->getListFooter(); ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
