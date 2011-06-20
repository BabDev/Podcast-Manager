<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;
?>
<div class="podcastmanager-feed<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<?php if ($this->params->get('show_feed_title', 1)) : ?>
<h2>
	<?php echo JHtml::_('content.prepare', $this->feed->name); ?>
</h2>
<?php endif; ?>
<?php if ($this->params->get('show_feed_description', 1) || $this->params->get('show_feed_image', 1)) : ?>
<div class="category-desc">
	<?php if ($this->params->get('show_feed_image') && $this->feed->image) : ?>
		<img src="<?php echo $this->feed->image; ?>"/>
	<?php endif; ?>
	<?php if ($this->params->get('show_feed_description') && $this->feed->description) : ?>
		<?php echo JHtml::_('content.prepare', $this->feed->description); ?>
	<?php endif; ?>
	<div class="clr"></div>
	</div>
<?php endif; ?>
<?php echo $this->loadTemplate('items'); ?>
</div>
