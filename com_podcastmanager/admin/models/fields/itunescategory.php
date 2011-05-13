<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// No direct access
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldItunesCategory extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.7
	 */
	protected $type = 'ItunesCategory';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.7
	 */
	public function getOptions()
	{
		// Set the options
		$options = array(
			JHtml::_('select.option', '', JText::_('JNONE')),
			JHtml::_('select.option', 'Arts &gt; Design', 'Arts &gt; Design'),
			JHtml::_('select.option', 'Arts &gt; Fashion &amp; Beauty', 'Arts &gt; Fashion &amp; Beauty'),
			JHtml::_('select.option', 'Arts &gt; Food', 'Arts &gt; Food'),
			JHtml::_('select.option', 'Arts &gt; Literature', 'Arts &gt; Literature'),
			JHtml::_('select.option', 'Arts &gt; Performing Arts', 'Arts &gt; Performing Arts'),
			JHtml::_('select.option', 'Arts &gt; Visual Arts', 'Arts &gt; Visual Arts'),
			JHtml::_('select.option', 'Business', 'Business'),
			JHtml::_('select.option', 'Business &gt; Business News', 'Business &gt; Business News'),
			JHtml::_('select.option', 'Business &gt; Careers', 'Business &gt; Careers'),
			JHtml::_('select.option', 'Business &gt; Investing', 'Business &gt; Investing'),
			JHtml::_('select.option', 'Business &gt; Management &amp; Marketing', 'Business &gt; Management &amp; Marketing'),
			JHtml::_('select.option', 'Business &gt; Shopping', 'Business &gt; Shopping'),
			JHtml::_('select.option', 'Comedy', 'Comedy'),
			JHtml::_('select.option', 'Education', 'Education'),
			JHtml::_('select.option', 'Education &gt; Education Technology', 'Education &gt; Education Technology'),
			JHtml::_('select.option', 'Education &gt; Higher Education', 'Education &gt; Higher Education'),
			JHtml::_('select.option', 'Education &gt; K-12', 'Education &gt; K-12'),
			JHtml::_('select.option', 'Education &gt; Language Courses', 'Education &gt; Language Courses'),
			JHtml::_('select.option', 'Education &gt; Training', 'Education &gt; Training'),
			JHtml::_('select.option', 'Games &amp; Hobbies', 'Games &amp; Hobbies'),
			JHtml::_('select.option', 'Games &amp; Hobbies &gt; Automotive', 'Games &amp; Hobbies &gt; Automotive'),
			JHtml::_('select.option', 'Games &amp; Hobbies &gt; Aviation', 'Games &amp; Hobbies &gt; Aviation'),
			JHtml::_('select.option', 'Games &amp; Hobbies &gt; Hobbies', 'Games &amp; Hobbies &gt; Hobbies'),
			JHtml::_('select.option', 'Games &amp; Hobbies &gt; Other Games', 'Games &amp; Hobbies &gt; Other Games'),
			JHtml::_('select.option', 'Games &amp; Hobbies &gt; Video Games', 'Games &amp; Hobbies &gt; Video Games'),
			JHtml::_('select.option', 'Government &amp; Organizations', 'Government &amp; Organizations'),
			JHtml::_('select.option', 'Government &amp; Organizations &gt; Local', 'Government &amp; Organizations &gt; Local'),
			JHtml::_('select.option', 'Government &amp; Organizations &gt; National', 'Government &amp; Organizations &gt; National'),
			JHtml::_('select.option', 'Government &amp; Organizations &gt; Non-Profit', 'Government &amp; Organizations &gt; Non-Profit'),
			JHtml::_('select.option', 'Government &amp; Organizations &gt; Regional', 'Government &amp; Organizations &gt; Regional'),
			JHtml::_('select.option', 'Health', 'Health'),
			JHtml::_('select.option', 'Health &gt; Alternative Health', 'Health &gt; Alternative Health'),
			JHtml::_('select.option', 'Health &gt; Fitness &amp; Nutrition', 'Health &gt; Fitness &amp; Nutrition'),
			JHtml::_('select.option', 'Health &gt; Self-Help', 'Health &gt; Self-Help'),
			JHtml::_('select.option', 'Health &gt; Sexuality', 'Health &gt; Sexuality'),
			JHtml::_('select.option', 'Kids &amp; Family', 'Kids &amp; Family'),
			JHtml::_('select.option', 'Music', 'Music'),
			JHtml::_('select.option', 'News &amp; Politics', 'News &amp; Politics'),
			JHtml::_('select.option', 'Religion &amp; Spirituality', 'Religion &amp; Spirituality'),
			JHtml::_('select.option', 'Religion &amp; Spirituality &gt; Buddhism', 'Religion &amp; Spirituality &gt; Buddhism'),
			JHtml::_('select.option', 'Religion &amp; Spirituality &gt; Christianity', 'Religion &amp; Spirituality &gt; Christianity'),
			JHtml::_('select.option', 'Religion &amp; Spirituality &gt; Hinduism', 'Religion &amp; Spirituality &gt; Hinduism'),
			JHtml::_('select.option', 'Religion &amp; Spirituality &gt; Islam', 'Religion &amp; Spirituality &gt; Islam'),
			JHtml::_('select.option', 'Religion &amp; Spirituality &gt; Judaism', 'Religion &amp; Spirituality &gt; Judaism'),
			JHtml::_('select.option', 'Religion &amp; Spirituality &gt; Other', 'Religion &amp; Spirituality &gt; Other'),
			JHtml::_('select.option', 'Religion &amp; Spirituality &gt; Spirituality', 'Religion &amp; Spirituality &gt; Spirituality'),
			JHtml::_('select.option', 'Science &amp; Medicine', 'Science &amp; Medicine'),
			JHtml::_('select.option', 'Science &amp; Medicine &gt; Medicine', 'Science &amp; Medicine &gt; Medicine'),
			JHtml::_('select.option', 'Science &amp; Medicine &gt; Natural Sciences', 'Science &amp; Medicine &gt; Natural Sciences'),
			JHtml::_('select.option', 'Science &amp; Medicine &gt; Social Sciences', 'Science &amp; Medicine &gt; Social Sciences'),
			JHtml::_('select.option', 'Society &amp; Culture', 'Society &amp; Culture'),
			JHtml::_('select.option', 'Society &amp; Culture &gt; History', 'Society &amp; Culture &gt; History'),
			JHtml::_('select.option', 'Society &amp; Culture &gt; Personal Journals', 'Society &amp; Culture &gt; Personal Journals'),
			JHtml::_('select.option', 'Society &amp; Culture &gt; Philosophy', 'Society &amp; Culture &gt; Philosophy'),
			JHtml::_('select.option', 'Society &amp; Culture &gt; Places &amp; Travel', 'Society &amp; Culture &gt; Places &amp; Travel'),
			JHtml::_('select.option', 'Sports &amp; Recreation', 'Sports &amp; Recreation'),
			JHtml::_('select.option', 'Sports &amp; Recreation &gt; Amateur', 'Sports &amp; Recreation &gt; Amateur'),
			JHtml::_('select.option', 'Sports &amp; Recreation &gt; College &amp; High School', 'Sports &amp; Recreation &gt; College &amp; High School'),
			JHtml::_('select.option', 'Sports &amp; Recreation &gt; Outdoor', 'Sports &amp; Recreation &gt; Outdoor'),
			JHtml::_('select.option', 'Sports &amp; Recreation &gt; Professional', 'Sports &amp; Recreation &gt; Professional'),
			JHtml::_('select.option', 'Technology', 'Technology'),
			JHtml::_('select.option', 'Technology &gt; Gadgets', 'Technology &gt; Gadgets'),
			JHtml::_('select.option', 'Technology &gt; Tech News', 'Technology &gt; Tech News'),
			JHtml::_('select.option', 'Technology &gt; Podcasting', 'Technology &gt; Podcasting'),
			JHtml::_('select.option', 'Technology &gt; Software How-To', 'Technology &gt; Software How-To'),
			JHtml::_('select.option', 'TV &amp; Film', 'TV &amp; Film')
		);

		return $options;
	}
}
