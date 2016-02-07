<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Feed selection class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.7
 */
class JFormFieldFeedName extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.7
	 */
	protected $type = 'FeedName';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  $options  The field options.
	 *
	 * @since   1.7
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = [];

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName(['a.id', 'a.name'], ['value', 'text']))
			->from($db->quoteName('#__podcastmanager_feeds', 'a'))
			->where($db->quoteName('a.name') . ' != ' . $db->quote(''))
			->where($db->quoteName('a.published') . ' != -2')
			->group('a.id, a.name, a.published')
			->order('a.id ASC');

		try
		{
			$options = $db->setQuery($query)->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		// Merge any additional options in the XML definition.
		return array_merge($options, parent::getOptions());
	}
}
