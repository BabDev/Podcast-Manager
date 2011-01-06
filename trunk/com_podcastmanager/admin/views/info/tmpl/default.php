<?php
/**
* Podcast Manager for Joomla!
*
* @version		$Id$
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base() . '/components/com_podcastmanager/views/info/info.css');

//TODO: Rewrite this page

?>
<div class="podcastmessage">
Thank you for installing this component! Be sure to install the accompanying module and plugin. While feedback is greatly appreciated, please read to the end of this document before sending in bug reports. You can also browse the forums at <a href="http://www.jlleblanc.com">jlleblanc.com</a> for answers to frequently asked questions.<br /><br />
</div>

<div class="podcastheader">How the suite works</div>
<div class="divider"></div>
<div class="podcastmessage">
<p>The Podcast Suite is a set of extensions designed to work with Joomla's core content management component. All podcast episodes are entered into standard Joomla! articles using a tag in the format of {enclose filenamehere.mp3}. The frontend of the component searches for these tags in published articles and adds them to your podcast feed. The plugin turns the tags into links (by default) or MP3 players so that site visitors can preview your podcast before subscribing. (You can use your favorite brower-based MP3 player through the Custom HTML feature if you wish.) The module provides a link to your podcast: both the feed itself and a handy iTunes subscribe link.</p>
</div>

<div class="podcastheader">What to expect</div>
<div class="divider"></div>
<div class="podcastmessage">

<p>When you select Podcast &gt; Manage Clips from the components menu, you will be presented with a list of all the files in the folder being used for podcasts (default is images/stories, this can be changed by clicking the Parameters button at the top). The Published column shows if the podcast appears within any article in the system, while the Metadata column shows whether or not iTunes data has been added for the file. To add files to the folder, use FTP as PHP configurations are usually not configured to handle large MP3s.</p>

<p>Clicking on filename links currently point to pages where you can edit show notes and add iTunes-specific information.</p>

<p>If you want to include a file from an external server, use a tag like this in your article: {enclose http://www.otherserver.com/file23.mp3 8474349 audio/mpeg}.  The first parameter is the filename, the second is the file length in bytes, and the third is the encoding type.</p>

<p>Before using the podcast component, change the configuration settings by clicking the Parameters button on the Manage Clips page.</p>
</div>

<div class="podcastheader">Upgrading from Podcast Suite for Joomla! 1.0</div>
<div class="divider"></div>
<div class="podcastmessage">
<p>Migrator files are available at <a href="http://www.jlleblanc.com">jlleblanc.com</a> that plug into the com_migrator <a href="http://joomlacode.org/gf/project/pasamioprojects/frs/?action=FrsReleaseBrowse&amp;frs_package_id=2588">component</a> written by Sam Moffatt. These will extract the metadata from your 1.0 website and add it to the SQL migration.</p>
</div>

<div class="podcastheader">Translations</div>
<div class="divider"></div>
<div class="podcastmessage">
<p>
All text used in the interface for Podcast Suite is translation-ready; existing translation files can be found in /administrator/components/com_podcast/languages. Existing translations are provided thanks to the following people:
</p>
<ul>
<li><b>Français</b> - <a href="http://www.soirees-terribles.be">Nicolas Boseret</a></li>
<li><b>Deutsch</b> - <a href="http://www.nik-o-mat.de">Niko Winckel</a></li>
</ul>
</div>

<div class="podcastheader">Credits</div>
<div class="divider"></div>
<div class="podcastmessage">
<p>
The following projects were used in the creation of the suite:
</p>
<ul>
<li><b><a href="http://www.getid3.org">getID3</a></b> - James Heinrich - GPL v2 license</li>
<li><b><a href="http://musicplayer.sourceforge.net">XSPF Player</a></b> - Fabricio Zuardi (see xspf_license.txt in plugin for license details)</li>
</ul>
</div>