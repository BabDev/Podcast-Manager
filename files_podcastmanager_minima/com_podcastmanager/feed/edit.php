<?php
/**
* Podcast Manager for Joomla!
*
* @package     PodcastManager
* @subpackage  files_podcastmanager_minima
*
* @copyright   Copyright (C) 2011 Michael Babker. All rights reserved.
* @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<ul id="submenu" class="out">
	<li class="item-content"><a href="#" class="active"><?php echo JText::_('TPL_MINIMA_CONTENT_LABEL_CONTENT'); ?></a></li>
	<li class="item-parameters"><a href="#"><?php echo JText::_('TPL_MINIMA_CONTENT_LABEL_PARAMETERS'); ?></a></li>
</ul>
<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=feed&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div id="item-basic">
		<div class="width-70 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_PODCASTMANAGER_VIEW_FEED_FIELDSET_FEED');?></legend>
				<ol class="adminformlist">
					<li>
						<?php echo $this->form->getLabel('name'); ?>
						<?php echo $this->form->getInput('name'); ?>
					</li>
					<li>
						<?php echo $this->form->getLabel('subtitle'); ?>
						<?php echo $this->form->getInput('subtitle'); ?>
					</li>
					<li class="item-text">
						<?php echo $this->form->getLabel('description'); ?>
						<?php echo $this->form->getInput('description'); ?>
					</li>
				</ol>
			</fieldset>
		</div>
		<div class="width-30 fltrt item-info">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_PODCASTMANAGER_VIEW_FEED_FIELDSET_FEED'); ?></legend>
				<ol class="adminformlist">
					<li><?php echo $this->form->getLabel('published'); ?>
					<?php echo $this->form->getInput('published'); ?></li>

					<li><?php echo $this->form->getLabel('copyright'); ?>
					<?php echo $this->form->getInput('copyright'); ?></li>

					<li><?php echo $this->form->getLabel('author'); ?>
					<?php echo $this->form->getInput('author'); ?></li>

					<li><?php echo $this->form->getLabel('image'); ?>
					<?php echo $this->form->getInput('image'); ?></li>

					<li><?php echo $this->form->getLabel('language'); ?>
					<?php echo $this->form->getInput('language'); ?></li>

					<li><?php echo $this->form->getLabel('id'); ?>
					<?php echo $this->form->getInput('id'); ?></li>
				</ol>
			</fieldset>
		</div>
	</div><!-- #item-basic -->

	<div id="item-advanced">
		<ul id="vertical-tabs">
			<li class="publishing"><a href="#" class="active"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></a></li>
			<li class="details"><a href="#"><?php echo JText::_('COM_PODCASTMANAGER_FIELDSET_ITUNES_OPTIONS'); ?></a></li>
		</ul>
		<div id="tabs">
			<fieldset id="publishing" class="panelform">
				<ol class="adminformlist">
					<li><?php echo $this->form->getLabel('created'); ?>
					<?php echo $this->form->getInput('created'); ?></li>

					<li><?php echo $this->form->getLabel('created_by'); ?>
					<?php echo $this->form->getInput('created_by'); ?></li>

					<li><?php echo $this->form->getLabel('publish_up'); ?>
					<?php echo $this->form->getInput('publish_up'); ?></li>

					<?php if ($this->item->modified_by) : ?>
					<li><?php echo $this->form->getLabel('modified_by'); ?>
					<?php echo $this->form->getInput('modified_by'); ?></li>

					<li><?php echo $this->form->getLabel('modified'); ?>
					<?php echo $this->form->getInput('modified'); ?></li>
					<?php endif; ?>
				</ol>
			</fieldset>
			<fieldset id="details" class="panelform">
				<ol class="adminformlist">
					<li><?php echo $this->form->getLabel('block'); ?>
					<?php echo $this->form->getInput('block'); ?></li>

					<li><?php echo $this->form->getLabel('explicit'); ?>
					<?php echo $this->form->getInput('explicit'); ?></li>

					<li><?php echo $this->form->getLabel('category1'); ?>
					<?php echo $this->form->getInput('category1'); ?></li>

					<li><?php echo $this->form->getLabel('category2'); ?>
					<?php echo $this->form->getInput('category2'); ?></li>

					<li><?php echo $this->form->getLabel('category3'); ?>
					<?php echo $this->form->getInput('category3'); ?></li>

					<li><?php echo $this->form->getLabel('ownername'); ?>
					<?php echo $this->form->getInput('ownername'); ?></li>

					<li><?php echo $this->form->getLabel('owneremail'); ?>
					<?php echo $this->form->getInput('owneremail'); ?></li>

					<li><?php echo $this->form->getLabel('keywords'); ?>
					<?php echo $this->form->getInput('keywords'); ?></li>

					<li><?php echo $this->form->getLabel('newFeed'); ?>
					<?php echo $this->form->getInput('newFeed'); ?></li>
				</ol>
			</fieldset>
		</div><!-- /#tabs -->
	</div><!-- /#item-advanced -->

	<div id="item-permissions">
	<?php if ($this->canDo->get('core.admin')): ?>
		<div class="width-100 fltlft">
			<fieldset class="panelform">
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>
		</div>
	<?php endif; ?>
	</div><!-- /#item-permissions -->

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
