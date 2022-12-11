<?php

namespace Drupal\d8\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller routines for installation profile routes.
 */
class D8MaintenanceController extends ControllerBase {

  /**
   * Switch maintenance mode.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Information about the current HTTP request.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   The redirect response.
   */
  public function action(Request $request) {
    $this->doAction();

    return $request->server->has('HTTP_REFERER')
      ? new RedirectResponse($request->server->get('HTTP_REFERER'))
      : $this->redirect('<front>');
  }

  /**
   * Execute the main process.
   */
  protected function doAction() {
    $this->state()->set(
      'system.maintenance_mode',
      !$this->state()->get('system.maintenance_mode')
    );
  }

}
