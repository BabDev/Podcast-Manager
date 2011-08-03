<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* @package		PodcastManager
* @subpackage	com_podcastmanager
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;

// Add external behaviors
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Get the user object.
$user = JFactory::getUser();

// Check if user is allowed to add/edit based on component permissinos.
$canEdit = $user->authorise('core.edit', 'com_podcastmanager');
?>
<div class="podcastmanager-feed<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif;
if ($this->params->get('show_feed_title', 1)) : ?>
	<h2><?php echo JHtml::_('content.prepare', $this->feed->name); ?></h2>
<?php endif;
if ($this->params->get('show_feed_description', 1) || $this->params->get('show_feed_image', 1)) : ?>
	<div class="category-desc">
	<?php if ($this->params->get('show_feed_image') && $this->feed->image) : ?>
		<img src="<?php echo $this->feed->image; ?>"/>
	<?php endif;
	if ($this->params->get('show_feed_description') && $this->feed->description) : ?>
		<?php echo JHtml::_('content.prepare', $this->feed->description);
	endif; ?>
		<div class="clr"></div>
	</div>
<?php endif;
if ($canEdit) : ?>
	<?php echo JHtml::_('icon.feededit', $this->feed, $this->params); ?>
<?php endif;
echo $this->loadTemplate('items'); ?>
</div>
