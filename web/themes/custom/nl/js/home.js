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

})(jQuery, Drupal);
