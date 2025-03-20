<?php

/**
 * @file
 * Provides installation profile for Drupal 10/11.
 *
 * It is based on the most common modules and themes that form the basis for
 * creating a stable site.
 */

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Implements hook_form_FORM_ID_alter().
 */
function d8_form_install_configure_form_alter(
  array &$form,
  FormStateInterface $form_state,
): void {
  $db = Database::getConnectionInfo();
  $form['admin_account']['account']['name']['#default_value'] = $db['default']['username'];
  $form['update_notifications']['update_status_module']['#default_value'] = [1];

  $form['#submit'][] = '_d8_install_configure_form_submit';
}

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
 * Saves the site name and E-mail in states to re-save these two records later.
 *
 * @see \Drupal\d8\Controller\D8WelcomeController::page()
 */
function _d8_install_configure_form_submit(
  array &$form,
  FormStateInterface $form_state,
): void {
  global $install_state;

  if (empty($install_state['config_install_path'])) {
    $values = [];

    foreach (['name', 'mail'] as $key) {
      $values[$key] = (string) $form_state->getValue("site_$key");
    }

    \Drupal::state()->set('d8', array_filter($values));
  }
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
