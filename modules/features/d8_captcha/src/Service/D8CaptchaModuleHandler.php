<?php

namespace Drupal\d8_captcha\Service;

use Drupal\Core\Extension\ModuleHandler;

/**
 * Class D8CaptchaModuleHandler.
 *
 * @package Drupal\d8_captcha\Service
 */
class D8CaptchaModuleHandler extends ModuleHandler {

  /**
   * {@inheritdoc}
   */
  public function invoke($module, $hook, array $args = []) {
    $data = parent::invoke($module, $hook, $args);

    if ($hook === 'captcha' && $args[0] === 'generate') {
      if ($module === 'recaptcha' && $args[1] === 'reCAPTCHA') {
        $data['form']['recaptcha_widget']['#suffix'] = '';
      }
      elseif (
        $module === 'recaptcha_preloader' &&
        $args[1] === 'reCAPTCHA with preloader' &&
        isset($data['form']['#attached']['library'])
      ) {
        unset($data['form']['#attached']['library']);
      }
    }

    return $data;
  }

}