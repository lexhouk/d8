<?php

/**
 * @file
 * Provides installation profile for Drupal 10/11.
 *
 * It is based on the most common modules and themes that form the basis for
 * creating a stable site.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Implements hook_form_FORM_ID_alter().
 */
function d8_form_user_login_form_alter(
  array &$form,
  FormStateInterface $form_state,
): void {
  $form['#submit'][] = '_d8_user_login_form_submit';
}

/**
 * Redirect on the front page after logging in if this page is set.
 */
function _d8_user_login_form_submit(
  array &$form,
  FormStateInterface $form_state,
): void {
  if (!\Drupal::request()->request->has('destination')) {
    $path = \Drupal::configFactory()->get('system.site')->get('page.front');

    if (!empty($path)) {
      $form_state->setRedirectUrl(Url::fromUserInput($path));
    }
  }
}
