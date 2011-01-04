<?php defined( '_JEXEC' ) or die( 'Restricted access' ); 

JToolBarHelper::title( JText::_( 'Error' ), 'addedit.png' );
JToolBarHelper::back();

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base() . '/components/com_podcast/alerts.css');

?>
<div class="alert">
	<p><?php echo JText::_('ALERT SELECTED FILE HAS SPACES'); ?></p>
</div>