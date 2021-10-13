(function ($, Drupal) {

    $(document).ready(function ($) {
        $('.litigation-results').slick({
            dots: true,
            infinite: false,
            speed: 300,
            slidesToShow: 3,
            slidesToScroll: 3
        });
    });

    // Our Results Read More.
    $(".view-results .read-more").click(function(e) {
        let short = $(".inner-text .short-text");
        let long = $(".inner-text .long-text");

        short.addClass("hide");
        short.removeClass("show");

        long.addClass("show");
        long.removeClass("hide");

        $(".read-more").hide();
    });

})(jQuery, Drupal);
