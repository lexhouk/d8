<?php

namespace Drupal\d8_theme\Plugin\Preprocess;

use Drupal\bootstrap\Plugin\Preprocess\Table as BootstrapTable;
use Drupal\bootstrap\Utility\Variables;
use Drupal\Core\Template\Attribute;

/**
 * Pre-processes variables for the "table" theme hook.
 *
 * @ingroup plugins_preprocess
 */
#[BootstrapPreprocess('table')]
class Table extends BootstrapTable {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables): void {
    parent::preprocessVariables($variables);

    $classes = ['table-wrapper'];

    if ($variables['responsive']) {
      $classes[] = 'table-responsive';
    }

    if (!isset($variables['wide'])) {
      $wide = $variables->getContext(
        'wide',
        $this->theme->getSetting('table_wide'),
      );

      $variables['wide'] = !!(int) $wide;
    }

    if ($variables['wide']) {
      $classes[] = 'table-wide';
    }

    $variables['wrapper_attributes'] = new Attribute(['class' => $classes]);
  }

}
