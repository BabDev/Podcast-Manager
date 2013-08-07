<?php

/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */
defined('_JEXEC') or die();

/**
 * Abstract class for the update parameters storage
 * @author nicholas
 *
 */
abstract class LiveUpdateStorage
{
	/**
	 * @var  JRegistry  The update data registry
	 */
	protected $registry = null;

	/**
	 * @var  string  The key prefix for the registry data
	 */
	protected $keyPrefix = 'update.';

	/**
	 * Singleton implementation
	 *
	 * @param   string  $type    Storage tyme (file, component)
	 * @param   array   $config  Configuration array
	 *
	 * @return  LiveUpdateStorage
	 */
	public static function getInstance($type, $config)
	{
		static $instances = array();

		$sig = md5($type, serialize($config));
		if (!array_key_exists($sig, $instances))
		{
			$className = 'LiveUpdateStorage' . ucfirst($type);

			if (!class_exists($className))
			{
				require_once __DIR__ . '/' . strtolower($type) . '.php';
			}

			$object	= new $className($config);
			$object->load($config);

			$instances[$sig] = $object;
		}

		return $instances[$sig];
	}

	/**
	 * Set a value to the storage registry. Automatically encodes updatedata.
	 *
	 * @param   string  $key    The key to set
	 * @param   mixed   $value  The value of the key to set
	 *
	 * @return  void
	 */
	public final function set($key, $value)
	{
		if ($key == 'updatedata')
		{
			if (function_exists('base64_encode') && function_exists('base64_decode'))
			{
				$value = base64_encode(serialize($value));
			}
			else
			{
				$value = serialize($value);
			}
		}

		$this->registry->set($this->keyPrefix . $key, $value);
	}

	/**
	 * Read a value from the storage registry
	 *
	 * @param   string  $key      The key to read
	 * @param   mixed   $default  The default value of the key, if the key is not present
	 *
	 * @return  mixed  The value of the key
	 */
	public final function get($key, $default)
	{
		$value = $this->registry->get($this->keyPrefix . $key, $default);

		if ($key == 'updatedata')
		{
			if (function_exists('base64_encode') && function_exists('base64_decode'))
			{
				$value = unserialize(base64_decode($value));
			}
			else
			{
				$value = unserialize($value);
			}
		}

		return $value;
	}

	/**
	 * Save the contents of the registry to the appropriate storage
	 *
	 * @return  void
	 */
	abstract public function save();

	/**
	 * Load data from the storage
	 *
	 * @param   array  The configuration options
	 *
	 * @return  void
	 */
	abstract public function load($config);
}
