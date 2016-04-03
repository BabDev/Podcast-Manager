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
    throw new Error('fieldPodcastMedia JavaScript requires jQuery')
}

!function (jQuery, document) {
    'use strict';

    /**
     * Instantiate the fieldPodcastMedia object
     *
     * @param {String} container
     * @param {Object} options
     */
    jQuery.fieldPodcastMedia = function (container, options) {
        this.defaults = {
            basepath: '', // base path to file
            buttonClear: '.button-clear', // selector for button to clear the value
            buttonSelect: '.button-select', // selector for button to change the value
            buttonSaveSelected: '.button-save-selected', // selector for button to save the selected value
            input: '.field-podcastmedia-input', // selector for the input
            url: 'index.php?option=com_podcastmedia&tmpl=component', // url for load the iframe
            modal: '.modal', // modal selector
            modalWidth: '100%', // modal width
            modalHeight: '300px' // modal height
        },

        // Merge options with defaults
        this.options = jQuery.extend({}, this.defaults, options);

        // Set up elements
        this.$container = jQuery(container);
        this.$modal = this.$container.find(this.options.modal);
        this.$modalBody = this.$modal.children('.modal-body');
        this.$input = this.$container.find(this.options.input);
        this.$buttonSelect = this.$container.find(this.options.buttonSelect);
        this.$buttonClear = this.$container.find(this.options.buttonClear);

        // Bind events
        this.$buttonSelect.on('click', this.modalOpen.bind(this));
        this.$buttonClear.on('click', this.clearValue.bind(this));
        this.$modal.on('hide', this.removeIframe.bind(this));
    };

    jQuery.fieldPodcastMedia.prototype = {
        /**
         * Open the modal
         */
        modalOpen: function () {
            var $iframe = jQuery('<iframe>', {
                name: 'field-podcastmedia-modal',
                src: this.options.url.replace('{field-podcastmedia-id}', this.$input.attr('id')),
                width: this.options.modalWidth,
                height: this.options.modalHeight
            });
            this.$modalBody.append($iframe);
            this.$modal.modal('show');
            jQuery('body').addClass('modal-open');

            var self = this; // save context

            $iframe.load(function () {
                var content = jQuery(this).contents();

                // bind insert
                content.on('click', self.options.buttonSaveSelected, function () {
                    var value = content.find('#f_url').val();

                    if (value) {
                        self.setValue(value);
                    }

                    self.modalClose.call(self);
                });

                // bind cancel
                content.on('click', '.button-cancel', function () {
                    jQuery('body').removeClass('modal-open');
                    this.modalClose.bind(self);
                });
            });
        },

        /**
         * Close the modal
         */
        modalClose: function () {
            this.$modal.modal('hide');
            jQuery('body').removeClass('modal-open');
            this.$modalBody.empty();
        },

        /**
         * Clear the iframe
         */
        removeIframe: function () {
            this.$modalBody.empty();
            jQuery('body').removeClass('modal-open');
        },

        /**
         * Set the value
         *
         * @param {String} value
         */
        setValue: function (value) {
            this.$input.val(value).trigger('change');
        },

        /**
         * Clear the value
         */
        clearValue: function () {
            this.setValue('');
        }
    };

    /**
     * Instantiate the media field
     *
     * @param {Object} options
     */
    jQuery.fn.fieldPodcastMedia = function (options) {
        return this.each(function () {
            var $el = jQuery(this), instance = $el.data('fieldPodcastMedia');

            if (!instance) {
                var options = options || {},
                    data = $el.data();

                // Check options in the element
                for (var p in data) {
                    if (data.hasOwnProperty(p)) {
                        options[p] = data[p];
                    }
                }

                instance = new jQuery.fieldPodcastMedia(this, options);
                $el.data('fieldPodcastMedia', instance);
            }
        });
    };

    // Initialise all defaults
    jQuery(document).ready(function () {
        jQuery('.field-podcastmedia-wrapper').fieldPodcastMedia();
    });
}(jQuery, document);
