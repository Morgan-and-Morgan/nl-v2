(function ($, Drupal) {

    $(document).ready(function ($) {
        // Our Results Carousel.
        $('.litigation-results').slick({
            dots: true,
            infinite: false,
            speed: 300,
            slidesToShow: 3,
            slidesToScroll: 3,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });

        // Our Results Read More.
        $( ".view-results .read-more" ).each(function() {
            $(this).click(function () {
                let short = $(".inner-text .short-text");
                let long = $(".inner-text .long-text");

                short.addClass("hide");
                short.removeClass("show");

                long.addClass("show");
                long.removeClass("hide");

                $(this).closest(".read-more").hide();
            });
        });
    });

})(jQuery, Drupal);
