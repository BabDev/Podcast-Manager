<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* @package		PodcastManager
* @subpackage	mod_podcastmanagerfeed
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;
?>
<ul class="podmanfeed<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) : ?>
	<li>
		<a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
		<?php if ($params->get('description') == 1) : ?>
		<br /><?php echo $item->itSummary; ?>
		<?php endif; ?>
	</li>
<?php endforeach; ?>
</ul>
