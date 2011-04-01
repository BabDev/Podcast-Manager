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
JHtml::_('behavior.formvalidation');
?>
<div class="podcast-edit">

<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="podcast-form" class="form-validate">
<div class="col main-section">
	<fieldset class="adminform">
		<legend><?php echo empty($this->item->id) ? JText::_('COM_PODCASTMANAGER_VIEW_PODCAST_ADD_PODCAST') : JText::sprintf('COM_PODCASTMANAGER_VIEW_PODCAST_EDIT_PODCAST', $this->item->id); ?></legend>
		<ul class="adminformlist">
			<li><?php echo $this->form->getLabel('filename'); ?>
			<?php echo $this->form->getInput('filename'); ?></li>

			<li><?php echo $this->form->getLabel('title'); ?>
			<?php echo $this->form->getInput('title'); ?></li>

			<li><?php echo $this->form->getLabel('published'); ?>
			<?php echo $this->form->getInput('published'); ?></li>

			<li><?php echo $this->form->getLabel('itSummary'); ?>
			<?php echo $this->form->getInput('itSummary'); ?></li>

			<li><?php echo $this->form->getLabel('itAuthor'); ?>
			<?php echo $this->form->getInput('itAuthor'); ?></li>

			<li><?php echo $this->form->getLabel('itBlock'); ?>
			<?php echo $this->form->getInput('itBlock'); ?></li>

			<li><?php echo $this->form->getLabel('itCategory'); ?>
			<?php echo $this->form->getInput('itCategory'); ?></li>

			<li><?php echo $this->form->getLabel('itDuration'); ?>
			<?php echo $this->form->getInput('itDuration'); ?></li>

			<li><?php echo $this->form->getLabel('itExplicit'); ?>
			<?php echo $this->form->getInput('itExplicit'); ?></li>

			<li><?php echo $this->form->getLabel('itKeywords'); ?>
			<?php echo $this->form->getInput('itKeywords'); ?></li>

			<li><?php echo $this->form->getLabel('itSubtitle'); ?>
			<?php echo $this->form->getInput('itSubtitle'); ?></li>

			<li><?php echo $this->form->getLabel('language'); ?>
			<?php echo $this->form->getInput('language'); ?></li>

			<li><?php echo $this->form->getLabel('id'); ?>
			<?php echo $this->form->getInput('id'); ?></li>
		</ul>
	</fieldset>
</div>

<div class="col options-section">
	<?php echo JHtml::_('sliders.start','podcastmanager-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
		<?php echo JHtml::_('sliders.panel',JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>
		<fieldset class="panelform">
		<legend class="element-invisible"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></legend>
		<ul class="adminformlist">

			<li><?php echo $this->form->getLabel('created'); ?>
			<?php echo $this->form->getInput('created'); ?></li>

			<li><?php echo $this->form->getLabel('publish_up'); ?>
			<?php echo $this->form->getInput('publish_up'); ?></li>

			<?php if ($this->item->modified_by) : ?>
			<li><?php echo $this->form->getLabel('modified_by'); ?>
			<?php echo $this->form->getInput('modified_by'); ?></li>

			<li><?php echo $this->form->getLabel('modified'); ?>
			<?php echo $this->form->getInput('modified'); ?></li>
			<?php endif; ?>

		</ul>

		</fieldset>

	<?php echo JHtml::_('sliders.end'); ?>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
</div>

