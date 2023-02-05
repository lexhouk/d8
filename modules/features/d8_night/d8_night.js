(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.d8Night = {
    attach: function attach(context, settings) {
      var name = 'Drupal.d8_night.reload';

      function check(night) {
        $.ajax({
          url: Drupal.url('night/' + (night ? 1 : 0)),
          dataType: 'json',
          success: function (data) {
            if (data.update) {
              localStorage.setItem(name, 'y');
              window.location.reload();
            }
          }
        });
      }

      if (localStorage.getItem(name)) {
        localStorage.removeItem(name);
      }
      else {
        var night;

        if ('theme' in localStorage) {
          night = localStorage.theme === 'true';
        }
        else {
          night = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }

        if (settings.d8_night !== night) {
          check(night);
        }
      }

      var $element = $('#dark-mode-switch', context);

      $element
        .val($element.is(':checked') + 0)
        .on('change', function () {
          check($(this).is(':checked'));
        });
    }
  };
})(jQuery, Drupal, drupalSettings);
