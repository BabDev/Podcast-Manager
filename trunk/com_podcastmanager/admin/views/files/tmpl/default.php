<?php 
/**
* Podcast Manager for Joomla!
*
* @version		$Id$
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base() . '/components/com_podcastmanager/alerts.css');

$document->addScript(JURI::base() . '/components/com_podcastmanager/views/files/tmpl/default.js');

//if($this->params->get('hidehelps', 0) != 1) 
//{
	?>
	<div class="info">
		<?php echo JText::_('COM_PODCASTMANAGER_VIEW_FILES_INFO_ADD_FILE'); ?><br /><br />
		<?php JText::printf('COM_PODCASTMANAGER_VIEW_FILES_INFO_DIRECTORY', $this->folder); ?>
	</div>
	<?php if ($this->hasSpaces) {
		JError::raiseWarning(500, JText::_('COM_PODCASTMANAGER_WARNING_FILENAME_SPACE'));
	}
// }

?>
<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=files');?>" method="post" name="adminForm" id="adminForm">
			<!-- 1.5 Filter Bars <td nowrap="nowrap">
				<?php
					echo $this->filter['published'];
					echo $this->filter['metadata'];
				?>
			</td>  -->
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_PODCASTMANAGER_FILTER_SEARCH_DESCRIPTION'); ?>" />

			<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->data); ?>);" /></th>
				<th class="title"><?php echo JText::_('Filename'); ?></th>
				<th class="title"><?php echo JText::_('Published'); ?></th>
				<th class="title"><?php echo JText::_('Metadata'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach($this->data as $file) 
		{
			$id = $file->id;

			if(!$id) {
				$editKeyName = 'filename';
				$editKeyValue = $file->filename;
			} else {
				$editKeyName = 'cid';
				$editKeyValue = $id;
			}

			$checked = JHTML::_('grid.id', $i, htmlentities($editKeyValue), false, $editKeyName);
			if($file->published) {
				$viewLink = JRoute::_("../index.php?option=com_content&view=article&id={$file->articleId}");
				$editLink = JRoute::_("index.php?option=com_content&task=edit&cid[]={$file->articleId}");

				$published = JText::_('Yes') . " <span style=\"font-size: 85%;\">(<a href=\"$viewLink\">" . JText::_('view')  . "</a>/<a href=\"$editLink\">" . JText::_('edit')  . "</a> " . JText::_('article')  . ")";
			} else {
				$published = JText::_('No') ;
			}
			$link = JRoute::_("index.php?option=com_podcastmanager&task=edit&{$editKeyName}[]=" . urlencode($editKeyValue));
			?>
			<tr class="<?php echo $file->hasSpaces ? 'filespace' : "row$k"; ?>"> 
				<td> 
					<?php echo $checked; ?> 
				</td>
				<td>
					<a href="<?php echo $link; ?>"><?php echo $file->filename; ?></a>
				</td>
				<td align="center">
					<?php echo $published; ?>
				</td>
				<td align="center">
					<?php echo "<a href=\"$link\">" . ($file->hasMetadata ? JText::_('Yes') : JText::_('No')) . "</a>"; ?>
				</td>
			</tr> 
			<?php 
			$k = 1 - $k;
			$i++;
		}
		?>
		</tbody>
	</table>
	
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
