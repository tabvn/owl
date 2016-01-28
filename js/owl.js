(function ($) {
    Drupal.behaviors.owl = {
        attach: function (context, settings) {
            $('.owl-field-formatter', context).each(function () {
                var $this = $(this);
                var $this_settings = $.parseJSON($this.attr('data-settings'));
                $this.owlCarousel($this_settings);

            });

        }
    };
})(jQuery);