<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Ensure JavaScript dependencies are loaded for the metadata parser
JHtml::_('jquery.framework');
JHtml::_('behavior.core');

// Add the component media
JHtml::_('script', 'podcastmanager/podcast.js', false, true);

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$js = <<< JS
Joomla.submitbutton = function(task) {
	if (task == 'podcast.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
		Joomla.submitform(task);
	} else {
		alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED'));
	}
};
JS;

JFactory::getDocument()->addScriptDeclaration($js);
?>

<div class="edit<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->def('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcast&p_id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
		<div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('podcast.save')">
					<span class="icon-ok"></span><?php echo JText::_('JSAVE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn" onclick="Joomla.submitbutton('podcast.cancel')">
					<span class="icon-cancel"></span><?php echo JText::_('JCANCEL') ?>
				</button>
			</div>
			<?php if ($this->params->get('save_history', 0) && $this->item->id) : ?>
				<div class="btn-group">
					<?php echo $this->form->getInput('contenthistory'); ?>
				</div>
			<?php endif; ?>
		</div>

		<fieldset>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_METADATA') ?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_FIELDSET_PUBLISHING') ?></a></li>
				<li><a href="#language" data-toggle="tab"><?php echo JText::_('JFIELD_LANGUAGE_LABEL') ?></a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="general">
					<?php echo $this->form->renderField('filename'); ?>
					<?php echo $this->form->renderField('title'); ?>
					<?php echo $this->form->renderField('feedname'); ?>
					<?php echo $this->form->renderField('mime'); ?>
					<?php echo $this->form->renderField('itSummary'); ?>
					<?php echo $this->form->renderField('itImage'); ?>
					<?php echo $this->form->renderField('itAuthor'); ?>
					<?php echo $this->form->renderField('itBlock'); ?>
					<?php echo $this->form->renderField('itDuration'); ?>
					<?php echo $this->form->renderField('itExplicit'); ?>
					<?php echo $this->form->renderField('itKeywords'); ?>
					<?php echo $this->form->renderField('itSubtitle'); ?>
				</div>
				<div class="tab-pane" id="publishing">
					<?php echo $this->form->renderField('tags'); ?>
					<?php if ($this->params->get('save_history', 0)) : ?>
						<?php echo $this->form->renderField('version_note'); ?>
					<?php endif; ?>
					<?php if ($this->user->authorise('core.edit.state', 'com_podcastmanager')) : ?>
						<?php echo $this->form->renderField('created'); ?>
						<?php echo $this->form->renderField('created_by'); ?>
						<?php echo $this->form->renderField('published'); ?>
						<?php echo $this->form->renderField('publish_up'); ?>
						<?php if ($this->item->modified_by) : ?>
							<?php echo $this->form->renderField('modified_by'); ?>
							<?php echo $this->form->renderField('modified'); ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<div class="tab-pane" id="language">
					<?php echo $this->form->renderField('language'); ?>
				</div>
			</div>
		</fieldset>
		<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
