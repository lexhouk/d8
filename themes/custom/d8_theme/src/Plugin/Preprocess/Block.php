<?php

namespace Drupal\d8_theme\Plugin\Preprocess;

use Drupal\bootstrap\Plugin\Preprocess\PreprocessBase;
use Drupal\bootstrap\Plugin\Preprocess\PreprocessInterface;
use Drupal\bootstrap\Utility\Variables;

/**
 * Pre-processes variables for the "block" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @BootstrapPreprocess("block")
 */
class Block extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    parent::preprocessVariables($variables);

    if ($variables->plugin_id === 'dark_mode_switch_block') {
      $variables->addClass(['navbar-form', 'navbar-right']);
    }
  }

}
