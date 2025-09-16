(function ($, Drupal, once) {
  'use strict';

  // One behavior to initialize both sliders.
  Drupal.behaviors.nlSliders = {
    attach: function (context) {
      // Results carousel
      $(once('nl-slick-litigation-results', '.litigation-results', context)).each(function () {
        const $slider = $(this);
        if ($slider.children().length) {
          $slider.slick({
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
        }
      });

      // "You may also like" carousel
      $(once('nl-slick-posts-container', '.posts-container', context)).each(function () {
        const $slider = $(this);
        if ($slider.children().length) {
          $slider.slick({
            dots: true,
            infinite: false,
            speed: 300,
            slidesToShow: 2,
            slidesToScroll: 2,
            responsive: [
              {
                breakpoint: 991,
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
        }
      });
    }
  };
})(jQuery, Drupal, once);
