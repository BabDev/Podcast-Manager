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

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$document =& JFactory::getDocument();
$document->addScript(JURI::base() . 'components/com_podcastmanager/views/podcast/tmpl/default.js');

?>
<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&layout=edit&id='.(int) $this->item->podcast_id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PODCASTMANAGER_VIEW_PODCAST_FIELDSET_METADATA'); ?></legend>
			<?php // if (!$this->item->podcast_id) {
				//echo $this->loadTemplate('shownotes');
			//} ?>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('filename'); ?>
				<?php echo $this->form->getInput('filename'); ?></li>

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
			</ul>
		</fieldset>
	</div>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo JRequest::getCmd('return');?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
