<?php

namespace Drupal\d8\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\recaptcha\Form\ReCaptchaAdminSettingsForm;
use Drupal\service\ConfigFormBaseTrait;
use Drupal\service\ModuleInstallerTrait;
use Drupal\service\StringTranslationTrait;

/**
 * Configure reCAPTCHA settings for this profile.
 */
class D8CaptchaForm extends ReCaptchaAdminSettingsForm {

  use ConfigFormBaseTrait;
  use ModuleInstallerTrait;
  use StringTranslationTrait;

  /**
   * The fields are split by section which should be hidden.
   */
  protected const array FIELDS = [
    'widget' => ['recaptcha_size'],
    'general' => ['recaptcha_site_key', 'recaptcha_secret_key'],
  ];

  /**
   * {@inheritdoc}
   */
  protected function addServices(): static {
    return $this->addModuleInstaller()->addStringTranslation();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'install_captcha_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      ...parent::getEditableConfigNames(),
      'recaptcha_preloader.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(
    array $form,
    FormStateInterface $form_state
  ): array {
    $form = parent::buildForm($form, $form_state);

    $form['#title'] = $this->t('reCAPTCHA settings');

    $field = &$form['widget']['recaptcha_size'];
    $field['#type'] = 'radios';

    if (!empty($field['#default_value'])) {
      $field['#options'][''] = $this->t('Normal');
      $field['#options'][$field['#default_value']] .= " ({$this->t('default')})";
    }

    foreach (static::FIELDS as $section => $fields) {
      $form[$section]['#type'] = 'container';
      $form[$section]['#weight'] = 1 - intval($section === key(static::FIELDS));

      foreach (Element::children($form[$section]) as $key) {
        if (!in_array($key, $fields)) {
          $form[$section][$key]['#access'] = FALSE;
        }
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(
    array &$form,
    FormStateInterface $form_state
  ): void {
    parent::submitForm($form, $form_state);

    if ($form_state->getValue('recaptcha_size') !== 'invisible') {
      $this->moduleInstaller()->install(['recaptcha_preloader']);
    }

    drupal_flush_all_caches();
  }

}
