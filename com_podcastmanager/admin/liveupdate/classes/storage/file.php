<?php

/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */
defined('_JEXEC') or die();

/**
 * Live Update File Storage Class
 * Allows to store the update data to files on disk. Its configuration options are:
 * path			string	The absolute path to the directory where the update data will be stored as INI files
 *
 */
class LiveUpdateStorageFile extends LiveUpdateStorage
{
	private $filename = null;
	private $extname = null;

	public function __construct()
	{
	}

	public function load($config)
	{
		JLoader::import('joomla.registry.registry');
		JLoader::import('joomla.filesystem.file');

		if (array_key_exists('path', $config))
		{
			$path	= $config['path'];
		}
		else
		{
			$path	= JPATH_CACHE;
		}
		$extname	= $config['extensionName'];
		$filename	= "$path/$extname.updates.php";

		// Kill old files
		$filenameKill = "$path/$extname.updates.ini";
		if (JFile::exists($filenameKill))
		{
			JFile::delete($filenameKill);
		}

		$this->filename	 = $filename;
		$this->extname	 = $extname;

		$this->registry = new JRegistry('update');

		if (JFile::exists($this->filename))
		{
			// Workaround for broken JRegistryFormatPHP API...
			@include_once $this->filename;

			$className = 'LiveUpdate' . ucwords($extname) . 'Cache';

			if (class_exists($className))
			{
				$object = new $className;
				$this->registry->loadObject($object);
			}
		}
	}

	public function save()
	{
		JLoader::import('joomla.registry.registry');
		JLoader::import('joomla.filesystem.file');

		$options = array(
			'class' => 'LiveUpdate' . ucwords($this->extname) . 'Cache'
		);
		$data	 = $this->registry->toString('PHP', $options);
		JFile::write($this->filename, $data);
	}

}