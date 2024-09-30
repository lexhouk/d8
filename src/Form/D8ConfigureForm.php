<?php

namespace Drupal\d8\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Installer\Form\SiteConfigureForm;
use Drupal\service\ConfigFactoryTrait;
use Drupal\service\ConfigFormBaseTrait;

/**
 * Provides the site configuration form.
 *
 * @phpstan-ignore-next-line
 */
class D8ConfigureForm extends SiteConfigureForm {

  use ConfigFormBaseTrait;
  use ConfigFactoryTrait;

  /**
   * {@inheritdoc}
   */
  protected function addServices(): static {
    return $this->addConfigFactory();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(
    array &$form,
    FormStateInterface $form_state
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
  }

}
