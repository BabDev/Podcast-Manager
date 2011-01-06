<?php 
/**
* Podcast Manager for Joomla!
*
* @version		$Id: default.php 9 2011-01-05 17:24:41Z mbabker $
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

if ($this->podcast->podcast_id) {
	JToolBarHelper::title( JText::_('Edit Podcast Metadata'), 'addedit.png' );
	JToolBarHelper::save();
} else {
	JToolBarHelper::title( JText::_('Add Podcast Metadata'), 'addedit.png' );
	JToolBarHelper::publish('publish', JText::_('SAVE AND PUBLISH IN ARTICLE'));
}

JToolBarHelper::cancel();

$document =& JFactory::getDocument();
$document->addScript(JURI::base() . 'components/com_podcastmanager/views/podcast/tmpl/default.js');

?>
<form action="index.php" method="post" name="adminForm">
<div class="col width-45">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Details' ); ?></legend>
	<table class="admintable">
		<?php 
		
		if (!$this->podcast->podcast_id) {
			echo $this->loadTemplate('shownotes');
		}
		
		?>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'Filename or URL' ); ?>:
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" name="filename" id="filename" size="60" value="<?php echo $this->podcast->filename; ?>" />
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'iTunes Author' ); ?>:
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" name="itAuthor" id="itAuthor" size="60" value="<?php echo $this->podcast->itAuthor; ?>" />
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'iTunes Block' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->block ?>
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'iTunes Category' ); ?>:
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" name="itCategory" id="itCategory" size="60" value="<?php echo $this->podcast->itCategory; ?>" />
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'iTunes Duration' ); ?>:
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" name="itDuration" id="itDuration" size="60" value="<?php echo $this->podcast->itDuration; ?>" />
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'iTunes Explicit' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->explicit ?>
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'iTunes Keywords' ); ?>:
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" name="itKeywords" id="itKeywords" size="60" value="<?php echo $this->podcast->itKeywords; ?>" />
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'iTunes Subtitle' ); ?>:
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" name="itSubtitle" id="itSubtitle" size="60" value="<?php echo $this->podcast->itSubtitle; ?>" />
			</td>
		</tr>
	</table>
	</fieldset>
</div>
<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_podcastmanager" />
	<input type="hidden" name="podcast_id" value="<?php echo $this->podcast->podcast_id; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>