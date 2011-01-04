<?php
// based on mod_syndicate
defined( '_JEXEC' ) or die( 'Restricted access' );

$params->def('text', '');
$params->def('urischeme', 'http');
$params->def('plainlink', 1);

$plainlink = $params->get('otherlink', '');
$img = $params->get('otherimage', '');

if(!$plainlink)
	$plainlink = JRoute::_(JURI::root(false) . 'index.php?option=com_podcast&view=feed&format=raw');

if($img)
	$img = JHTML::_('image', $img, 'Podcast Feed');
else
	$img = JHTML::_('image', 'modules/mod_podcast/podcast-mini2.png', 'Podcast Feed');

if($params->get('urischeme') == 'http')
	$link = $plainlink;
else
	$link = str_replace(array('http:', 'https:'), $params->get('urischeme') . ':', $plainlink);

require(JModuleHelper::getLayoutPath('mod_podcast'));
?>

