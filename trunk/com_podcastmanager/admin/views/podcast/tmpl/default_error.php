<?php
/**
* Podcast Manager for Joomla!
*
* @version		$Id: default_error.php 9 2011-01-05 17:24:41Z mbabker $
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

JToolBarHelper::title( JText::_( 'Error' ), 'addedit.png' );
JToolBarHelper::back();

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base() . '/components/com_podcastmanager/alerts.css');

?>
<div class="alert">
	<p><?php echo JText::_('ALERT SELECTED FILE HAS SPACES'); ?></p>
</div>