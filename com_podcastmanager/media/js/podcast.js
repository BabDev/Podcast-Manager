/**
 * Podcast Manager for Joomla!
 *
 * @package		PodcastManager
 * @subpackage	com_podcastmanager
 *
 * @copyright	Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

// Only define the PodcastManager namespace if not defined.
PodcastManager = window.PodcastManager || {};

(function (PodcastManager, document) {
    "use strict";

    /**
     * Parse the metadata for a podcast
     */
    PodcastManager.parseMetadata = function () {
        var fileName = jQuery('input[id=jform_filename]').val();

        jQuery.post(
            'index.php?option=com_podcastmanager&task=podcast.getMetadata&format=json',
            {filename: fileName},
            function (r) {
                if (r.success) {
                    Joomla.renderMessages(r.messages);

                    jQuery.each(r.data, function (key, value) {
                        jQuery('input[id=jform_' + key + ']').val(value);
                    });
                } else {
                    Joomla.renderMessages({message: [r.message], error: ['warning']});
                }
            }
        );
    };
}(PodcastManager, document));
