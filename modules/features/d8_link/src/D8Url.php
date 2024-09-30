<?php

namespace Drupal\d8_link;

use Drupal\Core\GeneratedUrl;
use Drupal\Core\Url;

/**
 * Defines an object that holds information about a URL.
 */
class D8Url extends Url {

  /**
   * {@inheritdoc}
   */
  public function toString(
    $collect_bubbleable_metadata = FALSE
  ): GeneratedUrl|string {
    $url = parent::toString($collect_bubbleable_metadata);

    $modify = &drupal_static('d8_link');

    if (!empty($modify)) {
      if ($this->unrouted) {
        $url = preg_replace('#^https*://(www\.|)#', '', $url);
      }

      $modify = FALSE;
    }

    return $url;
  }

}
