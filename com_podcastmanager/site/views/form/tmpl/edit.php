<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Create shortcut to parameters.
$params = $this->state->get('params');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'form.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<div class="edit<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=form&feedname=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<fieldset>
		<legend><?php echo JText::_('COM_PODCASTMANAGER_FEED_DATA'); ?></legend>
		<div class="formelm">
			<?php echo $this->form->getLabel('name'); ?>
			<?php echo $this->form->getInput('name'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('subtitle'); ?>
			<?php echo $this->form->getInput('subtitle'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('feedname'); ?>
			<?php echo $this->form->getInput('feedname'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('description'); ?>
			<?php echo $this->form->getInput('description'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('copyright'); ?>
			<?php echo $this->form->getInput('copyright'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('author'); ?>
			<?php echo $this->form->getInput('author'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('image'); ?>
			<?php echo $this->form->getInput('image'); ?>
		</div>
		<div class="formelm-buttons">
			<button type="button" onclick="Joomla.submitbutton('form.save')">
				<?php echo JText::_('JSAVE') ?>
			</button>
			<button type="button" onclick="Joomla.submitbutton('form.cancel')">
				<?php echo JText::_('JCANCEL') ?>
			</button>
		</div>
	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('COM_PODCASTMANAGER_FIELDSET_PUBLISHING'); ?></legend>
		<?php if ($this->user->authorise('core.edit.state', 'com_podcastmanager')) : ?>
		<div class="formelm">
			<?php echo $this->form->getLabel('created'); ?>
			<?php echo $this->form->getInput('created'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('created_by'); ?>
			<?php echo $this->form->getInput('created_by'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('published'); ?>
			<?php echo $this->form->getInput('published'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('publish_up'); ?>
			<?php echo $this->form->getInput('publish_up'); ?>
		</div>
		<?php if ($this->item->modified_by) : ?>
		<div class="formelm">
			<?php echo $this->form->getLabel('modified_by'); ?>
			<?php echo $this->form->getInput('modified_by'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('modified'); ?>
			<?php echo $this->form->getInput('modified'); ?>
		</div>
		<?php endif; ?>
		<?php endif; ?>
	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('COM_PODCASTMANAGER_FIELDSET_ITUNES_OPTIONS'); ?></legend>
		<div class="formelm">
			<?php echo $this->form->getLabel('block'); ?>
			<?php echo $this->form->getInput('block'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('explicit'); ?>
			<?php echo $this->form->getInput('explicit'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('category1'); ?>
			<?php echo $this->form->getInput('category1'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('category2'); ?>
			<?php echo $this->form->getInput('category2'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('category3'); ?>
			<?php echo $this->form->getInput('category3'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('ownername'); ?>
			<?php echo $this->form->getInput('ownername'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('owneremail'); ?>
			<?php echo $this->form->getInput('owneremail'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('keywords'); ?>
			<?php echo $this->form->getInput('keywords'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('newFeed'); ?>
			<?php echo $this->form->getInput('newFeed'); ?>
		</div>
	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?></legend>
		<div class="formelm-area">
		<?php echo $this->form->getLabel('language'); ?>
		<?php echo $this->form->getInput('language'); ?>
		</div>
	</fieldset>
		<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
