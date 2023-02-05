(function ($, Drupal) {

  Drupal.behaviors.d8ThemeCheckbox = {
    attach: function () {
      $('input[type="checkbox"]').checkboxX({ threeState: false });
    }
  };

})(jQuery, Drupal);
