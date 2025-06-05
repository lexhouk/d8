<?php

namespace Drupal\d8_theming\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for d8_theming.
 */
final class D8ThemingHooks {

  /**
   * Implements hook_link_alter().
   */
  #[Hook('link_alter')]
  public function linkAlter(array &$variables): void {
    $attributes = &$variables['options']['attributes'];

    if (
      !str_starts_with($attributes['id'] ?? '', 'toolbar-link-') &&
      array_intersect($attributes['class'] ?? [], ['btn', 'toolbar-icon']) === []
    ) {
      $attributes['class'][] = 'link-secondary';
    }
  }

}
