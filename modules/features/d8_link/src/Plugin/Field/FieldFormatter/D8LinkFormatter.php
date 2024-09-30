<?php

namespace Drupal\d8_link\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\d8_link\D8Url;
use Drupal\link\LinkItemInterface;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;

/**
 * Plugin implementation of the 'd8_link' formatter.
 */
#[FieldFormatter(
  id: 'd8_link',
  label: new TranslatableMarkup('D8+ Link'),
  description: new TranslatableMarkup('Remove the URL scheme and default sub-domain from the link title.'),
  field_types: ['link'],
)]
class D8LinkFormatter extends LinkFormatter {

  /**
   * {@inheritdoc}
   */
  protected function buildUrl(LinkItemInterface $item): Url {
    if (($url = parent::buildUrl($item))->isExternal()) {
      $url = D8Url::fromUri($url->getUri(), $url->getOptions());

      $modify = &drupal_static('d8_link');

      $modify = TRUE;
    }

    return $url;
  }

}
