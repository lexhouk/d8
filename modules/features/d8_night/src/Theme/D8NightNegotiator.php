<?php

namespace Drupal\d8_night\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\DefaultNegotiator;

/**
 * Determines the night theme of the site.
 */
class D8NightNegotiator extends DefaultNegotiator {

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    return !\Drupal::service('router.admin_context')->isAdminRoute();
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    $theme = parent::determineActiveTheme($route_match);
    return $theme . (!empty($_SESSION['d8_night']) ? '_night' : '');
  }

}
