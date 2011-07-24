<?php
/**
 * @package LiveUpdate
 * @copyright Copyright ©2011 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
	var $_extensionName			= 'pkg_podcastmanager';
	var $_extensionTitle		= 'Podcast Manager';
	var $_minStability 			= 'beta';
	var $_requiresAuthorization	= false;
	var $_updateURL				= 'http://www.flbab.com/index.php?option=com_ars&view=update&format=ini&id=3';
	var $_versionStrategy		= 'different';
	var $_xmlFilename			= 'pkg_podcastmanager.xml';
}