<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * iTunes Category selection class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.7
 */
class JFormFieldItunesCategory extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.7
	 */
	protected $type = 'ItunesCategory';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  $options  The field options.
	 *
	 * @since   1.7
	 */
	public function getOptions()
	{
		// Set the options
		$options = array(
			JHtml::_('select.option', '', JText::_('JNONE')),
			JHtml::_('select.option', 'Arts > Design', 'Arts &gt; Design'),
			JHtml::_('select.option', 'Arts > Fashion & Beauty', 'Arts &gt; Fashion &amp; Beauty'),
			JHtml::_('select.option', 'Arts > Food', 'Arts &gt; Food'),
			JHtml::_('select.option', 'Arts > Literature', 'Arts &gt; Literature'),
			JHtml::_('select.option', 'Arts > Performing Arts', 'Arts &gt; Performing Arts'),
			JHtml::_('select.option', 'Arts > Visual Arts', 'Arts &gt; Visual Arts'),
			JHtml::_('select.option', 'Business', 'Business'),
			JHtml::_('select.option', 'Business > Business News', 'Business &gt; Business News'),
			JHtml::_('select.option', 'Business > Careers', 'Business &gt; Careers'),
			JHtml::_('select.option', 'Business > Investing', 'Business &gt; Investing'),
			JHtml::_('select.option', 'Business > Management & Marketing', 'Business &gt; Management &amp; Marketing'),
			JHtml::_('select.option', 'Business > Shopping', 'Business &gt; Shopping'),
			JHtml::_('select.option', 'Comedy', 'Comedy'),
			JHtml::_('select.option', 'Education', 'Education'),
			JHtml::_('select.option', 'Education > Education Technology', 'Education &gt; Education Technology'),
			JHtml::_('select.option', 'Education > Higher Education', 'Education &gt; Higher Education'),
			JHtml::_('select.option', 'Education > K-12', 'Education &gt; K-12'),
			JHtml::_('select.option', 'Education > Language Courses', 'Education &gt; Language Courses'),
			JHtml::_('select.option', 'Education > Training', 'Education &gt; Training'),
			JHtml::_('select.option', 'Games & Hobbies', 'Games &amp; Hobbies'),
			JHtml::_('select.option', 'Games & Hobbies > Automotive', 'Games &amp; Hobbies &gt; Automotive'),
			JHtml::_('select.option', 'Games & Hobbies > Aviation', 'Games &amp; Hobbies &gt; Aviation'),
			JHtml::_('select.option', 'Games & Hobbies > Hobbies', 'Games &amp; Hobbies &gt; Hobbies'),
			JHtml::_('select.option', 'Games & Hobbies > Other Games', 'Games &amp; Hobbies &gt; Other Games'),
			JHtml::_('select.option', 'Games & Hobbies > Video Games', 'Games &amp; Hobbies &gt; Video Games'),
			JHtml::_('select.option', 'Government & Organizations', 'Government &amp; Organizations'),
			JHtml::_('select.option', 'Government & Organizations > Local', 'Government &amp; Organizations &gt; Local'),
			JHtml::_('select.option', 'Government & Organizations > National', 'Government &amp; Organizations &gt; National'),
			JHtml::_('select.option', 'Government & Organizations > Non-Profit', 'Government &amp; Organizations &gt; Non-Profit'),
			JHtml::_('select.option', 'Government & Organizations > Regional', 'Government &amp; Organizations &gt; Regional'),
			JHtml::_('select.option', 'Health', 'Health'),
			JHtml::_('select.option', 'Health > Alternative Health', 'Health &gt; Alternative Health'),
			JHtml::_('select.option', 'Health > Fitness & Nutrition', 'Health &gt; Fitness &amp; Nutrition'),
			JHtml::_('select.option', 'Health > Self-Help', 'Health &gt; Self-Help'),
			JHtml::_('select.option', 'Health > Sexuality', 'Health &gt; Sexuality'),
			JHtml::_('select.option', 'Kids & Family', 'Kids &amp; Family'),
			JHtml::_('select.option', 'Music', 'Music'),
			JHtml::_('select.option', 'News & Politics', 'News &amp; Politics'),
			JHtml::_('select.option', 'Religion & Spirituality', 'Religion &amp; Spirituality'),
			JHtml::_('select.option', 'Religion & Spirituality > Buddhism', 'Religion &amp; Spirituality &gt; Buddhism'),
			JHtml::_('select.option', 'Religion & Spirituality > Christianity', 'Religion &amp; Spirituality &gt; Christianity'),
			JHtml::_('select.option', 'Religion & Spirituality > Hinduism', 'Religion &amp; Spirituality &gt; Hinduism'),
			JHtml::_('select.option', 'Religion & Spirituality > Islam', 'Religion &amp; Spirituality &gt; Islam'),
			JHtml::_('select.option', 'Religion & Spirituality > Judaism', 'Religion &amp; Spirituality &gt; Judaism'),
			JHtml::_('select.option', 'Religion & Spirituality > Other', 'Religion &amp; Spirituality &gt; Other'),
			JHtml::_('select.option', 'Religion & Spirituality > Spirituality', 'Religion &amp; Spirituality &gt; Spirituality'),
			JHtml::_('select.option', 'Science & Medicine', 'Science &amp; Medicine'),
			JHtml::_('select.option', 'Science & Medicine > Medicine', 'Science &amp; Medicine &gt; Medicine'),
			JHtml::_('select.option', 'Science & Medicine > Natural Sciences', 'Science &amp; Medicine &gt; Natural Sciences'),
			JHtml::_('select.option', 'Science & Medicine > Social Sciences', 'Science &amp; Medicine &gt; Social Sciences'),
			JHtml::_('select.option', 'Society & Culture', 'Society &amp; Culture'),
			JHtml::_('select.option', 'Society & Culture > History', 'Society &amp; Culture &gt; History'),
			JHtml::_('select.option', 'Society & Culture > Personal Journals', 'Society &amp; Culture &gt; Personal Journals'),
			JHtml::_('select.option', 'Society & Culture > Philosophy', 'Society &amp; Culture &gt; Philosophy'),
			JHtml::_('select.option', 'Society & Culture > Places & Travel', 'Society &amp; Culture &gt; Places &amp; Travel'),
			JHtml::_('select.option', 'Sports & Recreation', 'Sports &amp; Recreation'),
			JHtml::_('select.option', 'Sports & Recreation > Amateur', 'Sports &amp; Recreation &gt; Amateur'),
			JHtml::_('select.option', 'Sports & Recreation > College & High School', 'Sports &amp; Recreation &gt; College &amp; High School'),
			JHtml::_('select.option', 'Sports & Recreation > Outdoor', 'Sports &amp; Recreation &gt; Outdoor'),
			JHtml::_('select.option', 'Sports & Recreation > Professional', 'Sports &amp; Recreation &gt; Professional'),
			JHtml::_('select.option', 'Technology', 'Technology'),
			JHtml::_('select.option', 'Technology > Gadgets', 'Technology &gt; Gadgets'),
			JHtml::_('select.option', 'Technology > Tech News', 'Technology &gt; Tech News'),
			JHtml::_('select.option', 'Technology > Podcasting', 'Technology &gt; Podcasting'),
			JHtml::_('select.option', 'Technology > Software How-To', 'Technology &gt; Software How-To'),
			JHtml::_('select.option', 'TV & Film', 'TV &amp; Film')
		);

		return $options;
	}
}
