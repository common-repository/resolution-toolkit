"use strict";

/*
 * Flickr Feed
 */
jQuery( document ).ready( function() { 

    jQuery('.flickr-wrap').each( function() {
		
		var $this    = jQuery(this);
		var flickrID = $this.data('flickr-id');
		var limit    = $this.data('limit');

        $this.jflickrfeed({
            limit: limit,
            qstrings: {
                id: flickrID
            },
            itemTemplate:
                '<li class="flickr-badge-image">' +
                '<a rel="prettyPhoto[kopa-flickr]" href="{{image}}" title="{{title}}">' +
                '<img src="{{image_s}}" alt="{{title}}" width="95px" height="95px" />' +
                '</a>' +
                '</li>'
        }, function(data) {
            jQuery("a[rel^='prettyPhoto']").prettyPhoto({
                show_title: false,
                deeplinking:false
            });
        });
        
    });

});