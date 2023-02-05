/**
 * @file
 * Checkbox behaviors.
 */

(function ($, Drupal) {

  /**
   * Include special theming for checkbox form elements.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.d8ThemeCheckbox = {
    attach(context) {
      $('input[type="checkbox"]').checkboxX({ threeState: false });
    },
  };

})(jQuery, Drupal);
