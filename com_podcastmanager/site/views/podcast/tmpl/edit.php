<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
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
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'podcast.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
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
<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcast&p_id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<fieldset>
		<legend><?php echo JText::_('COM_PODCASTMANAGER_METADATA'); ?></legend>
		<div class="formelm">
			<?php echo $this->form->getLabel('filename'); ?>
			<?php echo $this->form->getInput('filename'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('title'); ?>
			<?php echo $this->form->getInput('title'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('feedname'); ?>
			<?php echo $this->form->getInput('feedname'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('mime'); ?>
			<?php echo $this->form->getInput('mime'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('itSummary'); ?>
			<?php echo $this->form->getInput('itSummary'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('itImage'); ?>
			<?php echo $this->form->getInput('itImage'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('itAuthor'); ?>
			<?php echo $this->form->getInput('itAuthor'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('itBlock'); ?>
			<?php echo $this->form->getInput('itBlock'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('itDuration'); ?>
			<?php echo $this->form->getInput('itDuration'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('itExplicit'); ?>
			<?php echo $this->form->getInput('itExplicit'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('itKeywords'); ?>
			<?php echo $this->form->getInput('itKeywords'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('itSubtitle'); ?>
			<?php echo $this->form->getInput('itSubtitle'); ?>
		</div>
		<div class="formelm-buttons">
			<button type="button" onclick="Joomla.submitbutton('podcast.save')">
				<?php echo JText::_('JSAVE') ?>
			</button>
			<button type="button" onclick="Joomla.submitbutton('podcast.cancel')">
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
