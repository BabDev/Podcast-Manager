<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2014 Michael Babker. All rights reserved.
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
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('a.id', 'a.name'), array('value', 'text')));
		$query->from($db->quoteName('#__podcastmanager_feeds', 'a'));
		$query->where($db->quoteName('a.name') . ' != ' . $db->quote(''));
		$query->where($db->quoteName('a.published') . ' != -2');
		$query->group('a.id, a.name, a.published');
		$query->order('a.id ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		// Merge any additional options in the XML definition.
		$options = array_merge($options, parent::getOptions());

		return $options;
	}
}
