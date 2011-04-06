<?php
/**
 * Podcast Manager for Joomla!
 *
 * @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 */

// Restricted access
defined('_JEXEC') or die();
?>

<div><?php echo $params->get('text'); ?></div>
<div><a href="<?php echo $link; ?>"><?php echo $img; ?></a></div>
<div><a href="<?php echo $plainlink; ?>"><?php echo JText::_('MOD_PODCASTMANAGER_FULLFEED');?></a></div>
