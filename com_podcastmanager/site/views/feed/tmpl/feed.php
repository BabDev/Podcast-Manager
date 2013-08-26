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

// Check if user is allowed to add/edit based on component permissions.
$canEdit = $this->feed->id && $this->user->authorise('core.edit', 'com_podcastmanager.feed.' . $this->feed->id);
?>
<div class="podcastmanager-feed<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif;
if ($this->feed->name && $this->params->get('show_feed_title', 1)) : ?>
	<h2><?php echo JHtml::_('content.prepare', $this->feed->name); ?></h2>
<?php endif;
if ($this->params->get('show_feed_description', 1) || $this->params->get('show_feed_image', 1)) : ?>
	<div class="feed-desc">
	<?php if ($this->params->get('show_feed_image') && $this->feed->image) : ?>
		<img src="<?php echo $this->feed->image; ?>"/>
	<?php endif;
	if ($this->params->get('show_feed_description') && $this->feed->description) :
		echo JHtml::_('content.prepare', $this->feed->description);
	endif; ?>
		<div class="clr"></div>
	</div>
<?php endif;
if ($canEdit) :
	echo JHtml::_('icon.feededit', $this->feed, $this->params);
endif;
echo $this->loadTemplate('items'); ?>
</div>
