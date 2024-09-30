<?php

namespace Drupal\d8_theme\Plugin\Alter;

use Drupal\bootstrap\Plugin\Alter\ThemeSuggestions as ThemeSuggestionsBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\service\PluginBaseTrait;
use Drupal\service\RouteMatchTrait;

/**
 * Implements hook_theme_suggestions_alter().
 *
 * @ingroup plugins_alter
 *
 * @BootstrapAlter("theme_suggestions")
 */
class ThemeSuggestions extends ThemeSuggestionsBase implements ContainerFactoryPluginInterface {

  use PluginBaseTrait;
  use RouteMatchTrait;

  /**
   * {@inheritdoc}
   */
  protected function addServices(): static {
    return $this->addRouteMatch();
  }

  /**
   * Dynamic alter method for "maintenance_page".
   */
  protected function alterMaintenance_page(): void {
    if ($this->routeMatch()->getRouteName() !== 'system.db_update') {
      $this->addSuggestion($this->originalHook . '__guest');
    }
  }

}
