<?php

namespace Drupal\d8_mail;

use Drupal\Core\StringTranslation\StringTranslationTrait as CoreStringTranslationTrait;
use Drupal\d8\D8BuilderBase;
use Drupal\service\DateFormatterTrait;
use Drupal\service\RequestStackTrait;
use Drupal\service\StringTranslationTrait;
use Drupal\service\TimeTrait;

/**
 * Provides functionality for styling E-mail letters.
 *
 * @internal
 *   This is an internal utility class wrapping hook implementations.
 */
class D8MailBuilder extends D8BuilderBase {

  use CoreStringTranslationTrait;
  use DateFormatterTrait;
  use RequestStackTrait;

  use StringTranslationTrait {
    StringTranslationTrait::getStringTranslation insteadof CoreStringTranslationTrait;
  }

  use TimeTrait;

  /**
   * {@inheritdoc}
   */
  protected function addServices(): static {
    return parent::addServices()
      ->addDateFormatter()
      ->addRequestStack()
      ->addStringTranslation()
      ->addTime();
  }

  /**
   * Preprocess theme variables for the email_wrap__html theme hook.
   *
   * @param $variables
   *   The variables array.
   *
   * @see d8_mail_preprocess_email_wrap__html()
   */
  public function preprocessHtmlEmailWrapper(array &$variables): void {
    $variables['logo'] =
      $this->requestStack()->getCurrentRequest()->getSchemeAndHttpHost() .
      theme_get_setting('logo')['url'];

    /** @var \Drupal\symfony_mailer\InternalEmailInterface $email */
    $email = $variables['email'];

    $parameter = (array) $email->getParam('legacy_message');
    $account = $parameter['params']['account'] ?? $email->getAccount();

    $variables['welcome'] = $this->t(
      'Hello @recipient-name,',
      ['@recipient-name' => $account->getDisplayName()],
    );

    $variables['year'] = $this->dateFormatter()->format(
      $this->time()->getRequestTime(),
      'html_year',
    );

    $variables['site_name'] = $this->configFactory()->get('system.site')
      ->get('name');
  }

}
