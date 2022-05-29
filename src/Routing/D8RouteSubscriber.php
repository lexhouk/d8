<?php

namespace Drupal\d8\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\d8\Controller\D8Controller;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class D8RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('system.site_maintenance_mode')) {
      $route->setDefaults([
        '_controller' => D8Controller::class . '::maintenance',
      ]);
    }
  }

}