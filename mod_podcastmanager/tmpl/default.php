<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* @package		PodcastManager
* @subpackage	mod_podcastmanager
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;
?>

<div><?php echo $params->get('text'); ?></div>
<div><a href="<?php echo $link; ?>"><?php echo $img; ?></a></div>
<?php if ($params->get('plainlink') == 1) { ?>
<div><a href="<?php echo $plainlink; ?>"><?php echo JText::_('MOD_PODCASTMANAGER_FULLFEED');?></a></div>
<?php }
