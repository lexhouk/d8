<?php

namespace Drupal\d8_link\Hook;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\d8\D8HooksBase;

/**
 * Hook implementations for d8_link.
 */
final class D8LinkHooks extends D8HooksBase {

  /**
   * {@inheritdoc}
   */
  protected function module(): ?string {
    return 'extlink';
  }

  /**
   * Implements hook_page_attachments().
   */
  #[Hook('page_attachments')]
  public function pageAttachments(array &$attachments): void {
    $attachments['#attached']['library'][] = 'd8_link/base';
  }

  /**
   * Implements hook_page_attachments_alter().
   */
  #[Hook('page_attachments_alter')]
  public function pageAttachmentsAlter(&$attachments): void {
    $parents = ['#attached', 'drupalSettings', 'data', $this->module()];

    if (NestedArray::keyExists($attachments, $parents)) {
      $parents[] = 'extTargetAppendNewWindowLabel';

      NestedArray::setValue($attachments, $parents, '');
    }
  }

}
