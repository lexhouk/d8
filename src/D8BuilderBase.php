<?php

namespace Drupal\d8;

use Drupal\service\ClassResolverBase;
use Drupal\service\ConfigFactoryTrait;

/**
 * Provides a base for hook wrappers.
 *
 * @internal
 *   This is an internal utility class wrapping hook implementations.
 */
abstract class D8BuilderBase extends ClassResolverBase {

  use ConfigFactoryTrait;

  /**
   * {@inheritdoc}
   */
  protected function addServices(): static {
    return $this->addConfigFactory();
  }

}
