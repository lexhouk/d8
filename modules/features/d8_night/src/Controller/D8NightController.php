<?php

namespace Drupal\d8_night\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller routines for D8+ Night routes.
 */
class D8NightController extends ControllerBase {

  /**
   * Change the Bootstrap CDN theme according to light or dark mode.
   *
   * @param int $mode
   *   The one from the two following values indicates what mode will be
   *   activated:
   *   - '1': Dark mode.
   *   - '0': Light mode.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function switch($mode) {
    if ($update = !empty($mode) !== !empty($_SESSION['d8_night'])) {
      $_SESSION['d8_night'] = $mode;
    }

    return new JsonResponse(['update' => $update]);
  }

}
