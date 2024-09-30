<?php

namespace Drupal\d8_log\Plugin\EmailAdjuster;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\service\PluginBaseTrait;
use Drupal\service\RendererTrait;
use Drupal\symfony_mailer\EmailInterface;
use Drupal\symfony_mailer\Processor\EmailAdjusterBase;

/**
 * Defines the Log Email Adjuster.
 *
 * @EmailAdjuster(
 *   id = "log",
 *   label = @Translation("Log"),
 *   description = @Translation("Error & Exception to HTML."),
 *   weight = 800,
 * )
 */
class LogEmailAdjuster extends EmailAdjusterBase implements ContainerFactoryPluginInterface {

  use PluginBaseTrait;
  use RendererTrait;

  /**
   * {@inheritdoc}
   */
  protected function addServices(): static {
    return $this->addRenderer();
  }

  /**
   * Gets text with paths and/or namespaces as filtered HTML.
   *
   * @param string $content
   *   The data.
   */
  private function cell(string $content): array {
    return [
      '#markup' => preg_replace('#(\w+[/\\\]+)(\w+)#', '$1<wbr>$2', $content),
      '#allowed_tags' => ['wbr'],
    ];
  }

  /**
   * {@inheritdoc}
   *
   * @see exception_mailer_mail()
   */
  public function postRender(EmailInterface $email): void {
    $body = preg_split('/\s*\n+\s*/', trim($email->getHtmlBody()));

    // Convert log information to a table and show it without introduction and
    // site name.
    $elements[] = [
      '#theme' => 'table',
      '#rows' => array_map(
        function (string $item): array {
          $item = explode(':', $item, 2);
          array_walk($item, 'trim');
          $item[1] = ['data' => $this->cell($item[1])];
          return $item;
        },
        array_filter(
          array_slice($body, 2, 10),
          fn(string $item): bool => !preg_match('/:\s*$/', $item),
        ),
      ),
    ];

    // Delete line numerations from backtrace lines and show these lines.
    $elements[] = [
      '#theme' => 'item_list',
      '#list_type' => 'ol',
      '#items' => array_map(
        fn(string $item): array => $this
          ->cell(preg_replace('/^#\d+\s+/', '', $item)),
        array_slice($body, 12, -1),
      ),
      '#empty' => $this->t('Backtrace data is absent.'),
    ];

    $email->setHtmlBody(
      implode(
        '<hr>',
        array_map(
          fn(array $element): string => $this->renderer()->render($element),
          $elements,
        ),
      ),
    );
  }

}
