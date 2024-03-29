<?php

namespace Drupal\d8\Controller;

use Drupal\system\Controller\DbUpdateController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller routines for database update routes.
 */
class D8UpdateController extends DbUpdateController {

  /**
   * {@inheritdoc}
   */
  protected function selection(Request $request): array {
    $build = parent::selection($request);

    if (isset($build['start'])) {
      $build['start']['#open'] = TRUE;
    }

    return $build;
  }

}
