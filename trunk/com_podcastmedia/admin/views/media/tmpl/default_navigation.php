<?php
/**
* Podcast Manager for Joomla!
*
* @version		$Id$
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// No direct access
defined('_JEXEC') or die;
$app	= JFactory::getApplication();
$style = $app->getUserStateFromRequest('podcastmedia.list.layout', 'layout', 'thumbs', 'word');
?>
<div id="submenu-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">
		<div class="submenu-box">
			<div class="submenu-pad">
				<ul id="submenu" class="media">
					<li><a id="thumbs" onclick="MediaManager.setViewType('thumbs')" class="<?php echo ($style == "thumbs") ? 'active' : '';?>">
					<?php echo JText::_('COM_PODCASTMEDIA_THUMBNAIL_VIEW'); ?></a></li>
					<li><a id="details" onclick="MediaManager.setViewType('details')" class="<?php echo ($style == "details") ? 'active' : '';?>">
					<?php echo JText::_('COM_PODCASTMEDIA_DETAIL_VIEW'); ?></a></li>
				</ul>
				<div class="clr"></div>
			</div>
		</div>
		<div class="clr"></div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>