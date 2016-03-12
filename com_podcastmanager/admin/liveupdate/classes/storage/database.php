<?php
/**
 * @package   LiveUpdate
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license   GNU GPLv3 or later <https://www.gnu.org/licenses/gpl.html>
 */

defined('_JEXEC') or die();

/**
 * Live Update Component Storage Class
 * Allows to store the update data to a component's parameters. This is the most reliable method.
 * Its configuration options are:
 * table         string    The database table name to use for storing the data
 * key_field     string    The name of the key field in the database table
 * value_field   string    The name of the value field in the database table
 * key_name      string    The key used in the key field to handle live update information
 */
class LiveUpdateStorageDatabase extends LiveUpdateStorage
{
	private $tableName = null;

	private $keyField = null;

	private $valueField = null;

	private $keyValue = null;

	public function __construct()
	{
		$this->keyPrefix = '';
	}

	public function load($config)
	{
		$this->tableName = $config['tableName'];

		if (!array_key_exists('keyField', $config))
		{
			$this->keyField = 'key';
		}
		else
		{
			$this->keyField = $config['keyField'];
		}

		if (!array_key_exists('valueField', $config))
		{
			$this->valueField = 'value';
		}
		else
		{
			$this->valueField = $config['valueField'];
		}

		if (!array_key_exists('key', $config))
		{
			$this->keyValue = 'liveupdate';
		}
		else
		{
			$this->keyValue = $config['key'];
		}

		$db = JFactory::getDbo();
		$sql = $db->getQuery(true)
			->select($db->qn($this->valueField))
			->from($db->qn($this->tableName))
			->where($db->qn($this->keyField) . ' = ' . $db->q($this->keyValue));
		$db->setQuery($sql);
		$rawData = $db->loadResult();

		JLoader::import('joomla.registry.registry');
		$this->registry = new JRegistry('update');

		$this->registry->loadString($rawData, 'INI');
	}

	public function save()
	{
		$data = (object)array(
			$this->keyField   => $this->keyValue,
			$this->valueField => $this->registry->toString('INI')
		);

		$db = JFactory::getDBO();

		$result = false;
		try
		{
			$result = $db->insertObject($this->tableName, $data, $this->keyField);
		}
		catch (Exception $e)
		{
			$result = false;
		}

		if ($result == false)
		{
			$db->updateObject($this->tableName, $data, $this->keyField);
		}
	}
}
