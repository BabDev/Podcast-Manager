<?php
/**
* Podcast Manager for Joomla!
*
* @version		$Id: default_shownotes.php 9 2011-01-05 17:24:41Z mbabker $
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

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
