<?php

namespace Drupal\d8_mail\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\d8\D8HooksBase;

/**
 * Hook implementations for d8_mail.
 */
final class D8MailHooks extends D8HooksBase {

  /**
   * Implements hook_library_info_alter().
   */
  #[Hook('library_info_alter')]
  public function libraryInfoAlter(array &$libraries, string $extension): void {
    if ($extension === 'symfony_mailer') {
      unset($libraries['test']['css']['theme']['css/test.email.css']);
    }
  }

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(
    array $existing,
    string $type,
    string $theme,
    string $path,
  ): array {
    $name = 'email_wrap';
    return ["{$name}__html" => ['base hook' => $name]];
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter().
   */
  #[Hook('theme_suggestions_email_wrap_alter')]
  public function themeSuggestionsEmailWrapAlter(
    array &$suggestions,
    array $variables,
  ): void {
    if ($variables['is_html']) {
      $suggestions[] = "{$variables['theme_hook_original']}__html";
    }
  }

}
