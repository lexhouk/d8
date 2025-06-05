<?php

namespace Drupal\d8;

use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Url;

/**
 * Hook implementations for d8.
 */
abstract class D8HooksBase {

  use StringTranslationTrait;

  /**
   * D8HooksBase constructor.
   *
   * @param \Drupal\Core\Extension\ModuleExtensionList $moduleExtensionList
   *   The module extension list.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation
   *   The string translation.
   */
  public function __construct(
    private readonly ModuleExtensionList $moduleExtensionList,
    private readonly TranslationInterface $translation,
  ) {
    $this->setStringTranslation($translation);
  }

  /**
   * Implements hook_help().
   */
  #[Hook('help')]
  public function help(
    string $route_name,
    RouteMatchInterface $route_match,
  ): string {
    $output = '';
    $name = explode('\\', get_class($this))[1];

    if ($route_name !== "help.page.$name") {
      return $output;
    }

    $info = $this->moduleExtensionList->getExtensionInfo($name);

    if (($name = $this->module()) !== NULL) {
      $url = Url::fromRoute('help.page', ['name' => $name]);
    }
    else {
      foreach ($info['dependencies'] as $dependency) {
        $dependency = explode(':', $dependency);

        if ($dependency[0] === $dependency[1]) {
          $url = Url::fromRoute('help.page', ['name' => $dependency[0]]);
        }
      }
    }

    if (
      isset($url) &&
      preg_match(
        '/^Provides a wrapper for the (.+) module(| and related)\.$/',
        $info['description'],
        $matches,
      )
    ) {
      $name = str_replace($matches[1], '<a href=":url">:name</a>', $matches[0]);

      $info = [
        ':url' => $url->toString(),
        ':name' => $matches[1],
      ];

      $output = "<h3>{$this->t('About')}</h3><p>{$this->t($name, $info)}</p>";
    }

    return $output;
  }

  /**
   * Gets the module name.
   *
   * Return the machine name of the module being wrapped if it cannot be
   * automatically determined from the dependency list.
   */
  protected function module(): ?string {
    return NULL;
  }

}
