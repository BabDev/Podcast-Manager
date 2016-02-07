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
	protected $_extensionName = 'pkg_podcastmanager';
	protected $_extensionTitle = 'Podcast Manager';
	protected $_requiresAuthorization = false;
	protected $_updateURL = 'https://www.babdev.com/index.php?option=com_ars&view=update&format=ini&id=3';
	protected $_versionStrategy = 'different';
	protected $_xmlFilename = 'pkg_podcastmanager.xml';

	public function __construct()
	{
		$this->_minStability = JComponentHelper::getParams('com_podcastmanager')->get('minstability', 'alpha');
		parent::__construct();
	}
}
