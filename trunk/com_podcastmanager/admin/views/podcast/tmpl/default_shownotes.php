<?php defined( '_JEXEC' ) or die( 'Restricted access' );
$editor =& JFactory::getEditor();
?>
<tr>
	<td width="110" class="key">
		<label for="title">
			<?php echo JText::_( 'Title' ); ?>:
		</label>
	</td>
	<td>
		<input type="text" name="title" value="<?php echo $this->title; ?>" />
	</td>			
</tr>
<tr>
	<td width="110" class="key">
		<label for="title">
			<?php echo JText::_( 'Show Notes' ); ?>:
		</label>
	</td>
	<td>
		<?php echo $editor->display( 'text',  $this->text , '100%', '250', '75', '20' ); ?>
	</td>			
</tr>
