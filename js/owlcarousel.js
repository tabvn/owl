/**
 * @file
 */

(function ($) {
    Drupal.behaviors.owl = {
        attach: function (context, settings) {
            $('.owl-slider-wrapper', context).each(function () {
                var $this = $(this);
                var $this_settings = $.parseJSON($this.attr('data-settings'));
                $this.owlCarousel($this_settings);

            });

        }
    };
})(jQuery);
