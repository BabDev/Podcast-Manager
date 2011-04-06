<?php
/**
 * Podcast Manager for Joomla!
 *
 * @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 */

// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('script','system/multiselect.js',false,true);

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form
	action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcasts'); ?>"
	method="post" name="adminForm" id="adminForm">
<fieldset id="filter-bar"><legend class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></legend>
<div class="filter-search"><label class="filter-search-lbl"
	for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
<input type="text" name="filter_search" id="filter_search"
	value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
	title="<?php echo JText::_('COM_WEBLINKS_SEARCH_IN_TITLE'); ?>" />
<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
<button type="button"
	onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
</div>
<div class="filter-select"><label class="selectlabel"
	for="filter_published"> <?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?>
</label> <select name="filter_published" id="filter_published"
	class="inputbox">
	<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
	<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
</select> <label class="selectlabel" for="filter_language"> <?php echo JText::_('JOPTION_SELECT_LANGUAGE'); ?>
</label> <select name="filter_language" id="filter_language"
	class="inputbox">
	<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
	<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
</select>

<button type="button" id="filter-go" onclick="this.form.submit();"><?php echo JText::_('JSUBMIT'); ?></button>

</div>
</fieldset>
<div class="clr"></div>

<table class="adminlist">
	<thead>
		<tr>
			<th class="checkmark-col"><input type="checkbox"
				name="checkall-toggle" value=""
				title="<?php echo JText::_('TPL_HATHOR_CHECKMARK_ALL'); ?>"
				onclick="checkAll(this)" /></th>
			<th class="title"><?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
			</th>
			<th class="nowrap state-col"><?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
			</th>
			<th class="width-5"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
			</th>
			<th class="nowrap id-col"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
			</th>
		</tr>
	</thead>

	<tbody>
	<?php
	$n = count($this->items);
	foreach ($this->items as $i => $item) :
	$canCreate	= $user->authorise('core.create',		'com_podcastmanager');
	$canEdit	= $user->authorise('core.edit',			'com_podcastmanager');
	$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
	$canChange	= $user->authorise('core.edit.state',	'com_podcastmanager') && $canCheckin;
	?>
		<tr class="row<?php echo $i % 2; ?>">
			<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
			<td><?php if ($item->checked_out) : ?> <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'podcasts.', $canCheckin); ?>
			<?php endif; ?> <?php if ($canEdit) : ?> <a
				href="<?php echo JRoute::_('index.php?option=com_podcastmanager&task=podcast.edit&id='.(int) $item->id); ?>">
				<?php echo $this->escape($item->title); ?></a> <?php else : ?> <?php echo $this->escape($item->title); ?>
				<?php endif; ?></td>
			<td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'podcasts.', $canChange); ?>
			</td>
			<td class="center"><?php if ($item->language=='*'):?> <?php echo JText::alt('JALL','language'); ?>
			<?php else:?> <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
			<?php endif;?></td>
			<td class="center"><?php echo $item->id; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

		<?php echo $this->pagination->getListFooter(); ?> <input type="hidden"
	name="task" value="" /> <input type="hidden" name="boxchecked"
	value="0" /> <input type="hidden" name="filter_order"
	value="<?php echo $listOrder; ?>" /> <input type="hidden"
	name="filter_order_Dir" value="<?php echo $listDirn; ?>" /> <?php echo JHtml::_('form.token'); ?>
</form>
