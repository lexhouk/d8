<?php

namespace Drupal\d8\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Installer\Form\SiteConfigureForm;
use Drupal\service\ConfigFactoryTrait;
use Drupal\service\ConfigFormBaseTrait;
use Drupal\service\StateTrait;

/**
 * Provides the site configuration form.
 *
 * @phpstan-ignore-next-line
 */
class D8ConfigureForm extends SiteConfigureForm {

  use ConfigFormBaseTrait;
  use ConfigFactoryTrait;
  use StateTrait;

  /**
   * {@inheritdoc}
   */
  protected function creation(): static {
    return $this->addConfigFactory();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(
    array $form,
    FormStateInterface $form_state,
  ): array {
    $form = parent::buildForm($form, $form_state);

    $form['admin_account']['account']['name']['#default_value'] = Database::getConnectionInfo()['default']['username'];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(
    array &$form,
    FormStateInterface $form_state,
  ): void {
    parent::submitForm($form, $form_state);

    if ($form_state->getValue('enable_update_status_module')) {
      if (!$form_state->getValue('enable_update_status_emails')) {
        $this->resetConfigFactory();
      }

      $this->config('update.settings')
        ->set('check.disabled_extensions', TRUE)
        ->set('news', TRUE)
        ->set('notification.threshold', 'security')
        ->save(TRUE);
    }

    // Saves the site name and E-mail in states to re-save these two records
    // later.
    // @see \Drupal\d8\Controller\D8WelcomeController::page()

    global $install_state;

    if (empty($install_state['config_install_path'])) {
      $values = [];

      foreach (['name', 'mail'] as $key) {
        $values[$key] = (string) $form_state->getValue("site_$key");
      }

      $this->state()->set('d8', array_filter($values));
    }
  }

}
