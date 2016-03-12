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

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$js = <<< JS
Joomla.submitbutton = function(task) {
	if (task == 'podcast.cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {
		Joomla.submitform(task, document.getElementById('item-form'));
	}
};
JS;

JFactory::getDocument()->addScriptDeclaration($js);
?>

<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcast&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_PODCASTMANAGER_VIEW_PODCAST_FIELDSET_METADATA', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<fieldset class="adminform">
					<?php echo $this->form->renderField('filename'); ?>
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
				</fieldset>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
