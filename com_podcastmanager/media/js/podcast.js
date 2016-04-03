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

if (typeof jQuery === 'undefined') {
    throw new Error('PodcastManager JavaScript requires jQuery')
}

if (typeof Joomla === 'undefined') {
    throw new Error('PodcastManager JavaScript requires the Joomla core JavaScript API')
}

!function (jQuery, Joomla, window) {
    'use strict';

    window.PodcastManager = {
        /**
         * Parse the metadata for a podcast
         */
        parseMetadata: function () {
            var fileName = jQuery('input[id=jform_filename]').val(),
                spinner = jQuery('#loading');

            jQuery.ajax(
                'index.php?option=com_podcastmanager&task=podcast.getMetadata&format=json',
                {
                    type: 'POST',
                    data: {filename: fileName},
                    beforeSend: function (jqXHR, settings) {
                        if (spinner.length) {
                            spinner.css('display', 'block');
                        }
                    },
                    success: function (response, textStatus, xhr) {
                        if (response.success) {
                            Joomla.renderMessages(response.messages);

                            jQuery.each(response.data, function (key, value) {
                                jQuery('input[id=jform_' + key + ']').val(value);
                            });
                        } else {
                            Joomla.renderMessages({message: [response.message], error: ['warning']});
                        }
                    },
                    complete: function (jqXHR, textStatus) {
                        if (spinner.length) {
                            spinner.css('display', 'none');
                        }
                    }
                }
            );
        }
    }
}(jQuery, Joomla, window);
