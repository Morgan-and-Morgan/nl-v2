const $ = jQuery;

/**
 after a set number of characters, truncate the contents of a container and replace with a prompt to 'Read More' (this
 string is a configurable parameter)
 **/
const ReadMoreModel = Backbone.Model.extend({
    defaults: {
        maxChars: null,
        overflowChars: null,
        promptText: null,

        allText: null,
        shownText: null
    },

    isShowingAllText() {
        return this.get('allText') === this.get('shownText');
    },

    truncatedText() {
        return this.get('allText').substr(0, this.get('maxChars'));
    }
});

const ReadMoreView = Backbone.View.extend({
    template: _.template("<p><%= text %><% if (showReadMore) { %><%= overflowChars %><span class='read-more-btn'><%= promptText %></span><% } %></p>"),

    events: {
        'click .read-more-btn': 'loadAllText'
    },

    initialize() {
        this.model.on('change:shownText', this.render, this);
        this.model.set({shownText: this.model.truncatedText()})
    },

    loadAllText() {
        this.model.set({shownText: this.model.get('allText')});
    },

    render() {
        this.$el.html(this.template({
            showReadMore: !this.model.isShowingAllText(),
            overflowChars: this.model.get('overflowChars'),
            promptText: this.model.get('promptText'),
            text: this.model.get('shownText')
        }));
    }
});


let createReadMore = (textEl, options) => {
    let defaults = {
        promptText: "Read More",
        maxChars: 60,
        overflowChars: "..."
    };

    options = $.extend({}, defaults, options);

    let readMoreModel = new ReadMoreModel({
        maxChars: options.maxChars,
        overflowChars: options.overflowChars,
        promptText: options.promptText,
        allText: $(textEl).text()
    });

    new ReadMoreView({
        model: readMoreModel,
        el: textEl
    });
};


let isMobile = () => {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
};

// categorize the screen size as either small, medium, or large, represented as 1,2,3, respectively
let screenCategory = () => {
    let mapping = {
        1: size => size <= 700,
        2: size => size > 700 && size <= 1200,
        3: size => size > 1200
    };

    let size = window.innerWidth;

    for (let mkey in mapping) {
        let mkeyFits = mapping[mkey](size);

        if (mapping.hasOwnProperty(mkey) && mkeyFits) {
            return mkey;
        }
    }
};


$(document).ready(() => {

    // Testimonials Read More.
    $('.testimonials-container .results-list li article .inner-text .field--name-body').each(function() {
        createReadMore(this, {
            maxChars: isMobile() ? 150 : 285
        });
    });

    // Our Results Read More.
    $('.more-results .results-list .view-results li .inner-text').each(function() {
        createReadMore(this, {
            maxChars: isMobile() ? 150 : 285
        });
    });


    $(".moreBox").slice(0, 3).show();
    if ($(".blogBox:hidden").length != 0) {
        $("#loadMore").show();
    }
    $("#loadMore").on('click', function (e) {
        e.preventDefault();
        $(".moreBox:hidden").slice(0, 5).slideDown();
        if ($(".moreBox:hidden").length == 0) {
            $("#loadMore").fadeOut('slow');
        }
    });

});
