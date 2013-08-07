<?php

/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */
defined('_JEXEC') or die();

/**
 * Live Update Component Storage Class
 * Allows to store the update data to a component's parameters. This is the most reliable method.
 * Its configuration options are:
 * component	string	The name of the component which will store our data. If not specified the extension name will be used.
 * key			string	The name of the component parameter where the serialized data will be stored. If not specified "liveupdate" will be used.
 */
class LiveUpdateStorageComponent extends LiveUpdateStorage
{
	private $component = null;

	private $key = null;

	public function __construct()
	{
		$this->keyPrefix = '';
	}

	public function load($config)
	{
		if (!array_key_exists('component', $config))
		{
			$this->component = $config['extensionName'];
		}
		else
		{
			$this->component = $config['component'];
		}

		if (!array_key_exists('key', $config))
		{
			$this->key = 'liveupdate';
		}
		else
		{
			$this->key = $config['key'];
		}

		// Not using JComponentHelper to avoid conflicts ;)
		$db			 = JFactory::getDbo();
		$sql		 = $db->getQuery(true)
			->select($db->qn('params'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type') . ' = ' . $db->q('component'))
			->where($db->qn('element') . ' = ' . $db->q($this->component));
		$db->setQuery($sql);
		$rawparams	 = $db->loadResult();
		$params		 = new JRegistry();
		$params->loadString($rawparams, 'JSON');

		$data = $params->get($this->key, '');

		JLoader::import('joomla.registry.registry');
		$this->registry = new JRegistry('update');

		$this->registry->loadString($data, 'INI');
	}

	public function save()
	{
		$data = $this->registry->toString('INI');

		$db = JFactory::getDBO();

		// An interesting discovery: if your component is manually updating its
		// component parameters before Live Update is called, then calling Live
		// Update will reset the modified component parameters because
		// JComponentHelper::getComponent() returns the old, cached version of
		// them. So, we have to forget the following code and shoot ourselves in
		// the feet. Dammit!!!
		$sql = $db->getQuery(true)
			->select($db->qn('params'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type') . ' = ' . $db->q('component'))
			->where($db->qn('element') . ' = ' . $db->q($this->component));
		$db->setQuery($sql);
		$rawparams	 = $db->loadResult();
		$params		 = new JRegistry();
		$params->loadString($rawparams, 'JSON');

		$params->set($this->key, $data);

		$data	 = $params->toString('JSON');
		$sql	 = $db->getQuery(true)
			->update($db->qn('#__extensions'))
			->set($db->qn('params') . ' = ' . $db->q($data))
			->where($db->qn('type') . ' = ' . $db->q('component'))
			->where($db->qn('element') . ' = ' . $db->q($this->component));

		$db->setQuery($sql);
		$db->execute();
	}

}
