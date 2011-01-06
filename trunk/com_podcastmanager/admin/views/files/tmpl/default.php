<?php 
/**
* Podcast Manager for Joomla!
*
* @version		$Id: default.php 12 2011-01-05 22:57:02Z mbabker $
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

JToolBarHelper::title( JText::_( 'Podcast Files Manager' ), 'addedit.png' );
JToolBarHelper::editList();
JToolBarHelper::addNew();
JToolBarHelper::preferences('com_podcastmanager', '550');

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base() . '/components/com_podcastmanager/alerts.css');

$document->addScript(JURI::base() . '/components/com_podcastmanager/views/files/tmpl/default.js');

if($this->params->get('hidehelps', 0) != 1) 
{
	?>
	<div class="info">
		<?php echo JText::_('INFO ADD A FILE'); ?><br /><br />
		<?php JText::printf('INFO FILES IN DIRECTORY', $this->folder); ?>
	</div>
	<?php
	
	if ($this->hasSpaces) {
		?>
		<div class="alert">
		<strong><?php echo JText::_('Alert'); ?></strong>
		<p><?php echo JText::_('ALERT SPACES IN FILENAME'); ?></p>
		</div>
		<?php
	}
}

?>
<form action="index.php" method="post" name="adminForm">
	<table>
		<tr>
			<td align="left">
				<strong><?php echo JText::_('Filter'); ?></strong>:
			</td>
			<td nowrap="nowrap">
				<?php
					echo $this->filter['published'];
					echo $this->filter['metadata'];
				?>
			</td>
		</tr>
	</table>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->data); ?>);" /></th>
				<th class="title"><?php echo JText::_('Filename'); ?></th>
				<th class="title"><?php echo JText::_('Published'); ?></th>
				<th class="title"><?php echo JText::_('Metadata'); ?></th>
			</tr>
		</thead>
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
			$link = JRoute::_("index.php?option=$option&task=edit&{$editKeyName}[]=" . urlencode($editKeyValue));
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
		<tfoot>
			<td colspan="4"><?php echo $this->pagination->getListFooter(); ?></td>
		</tfoot>
	</table>
	
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
