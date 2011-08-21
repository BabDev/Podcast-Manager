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
</ul>
<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcast&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
    <div id="item-basic">
    <div class="width-70 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_PODCASTMANAGER_VIEW_PODCAST_FIELDSET_METADATA'); ?></legend>
            <ol class="adminformlist">
				<li><?php echo $this->form->getLabel('filename'); ?>
				<?php echo $this->form->getInput('filename'); ?></li>

                <li class="item-title">
                    <?php echo $this->form->getLabel('title'); ?>
                    <?php echo $this->form->getInput('title'); ?>
                </li>

				<li><?php echo $this->form->getLabel('feedname'); ?>
				<?php echo $this->form->getInput('feedname'); ?></li>

				<li><?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?></li>

				<li><?php echo $this->form->getLabel('itSummary'); ?>
				<?php echo $this->form->getInput('itSummary'); ?></li>

				<li><?php echo $this->form->getLabel('itAuthor'); ?>
				<?php echo $this->form->getInput('itAuthor'); ?></li>

				<li><?php echo $this->form->getLabel('itBlock'); ?>
				<?php echo $this->form->getInput('itBlock'); ?></li>

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
            </ol>
        </fieldset>
	</div>
	<div class="width-30 fltrt item-info">
        <fieldset class="adminform">
            <legend><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></legend>
            <ol class="adminformlist">
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
            </ol>
        </fieldset>
	</div>
    </div><!-- #item-basic -->

    <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
</form>
