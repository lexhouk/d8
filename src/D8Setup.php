<?php

namespace Drupal\d8;

use Drupal\Core\Config\FileStorage;
use Drupal\Core\Config\InstallStorage;
use Drupal\Core\Entity\Controller\VersionHistoryController;
use Drupal\Core\Site\Settings;
use Drupal\Core\StringTranslation\StringTranslationTrait as CoreStringTranslationTrait;
use Drupal\service\EntityTypeManagerTrait;
use Drupal\service\ExtensionPathResolverTrait;
use Drupal\service\ModuleInstallerTrait;
use Drupal\service\ModuleListTrait;
use Drupal\service\StateTrait;
use Drupal\service\StringTranslationTrait;
use Drupal\user\UserInterface;

/**
 * Provides functionality to set up installation profile.
 *
 * @internal
 *   This is an internal utility class wrapping hook implementations.
 */
class D8Setup extends D8BuilderBase {

  use CoreStringTranslationTrait;
  use EntityTypeManagerTrait;
  use ExtensionPathResolverTrait;
  use ModuleInstallerTrait;
  use ModuleListTrait;
  use StateTrait;

  use StringTranslationTrait {
    StringTranslationTrait::getStringTranslation insteadof CoreStringTranslationTrait;
  }

  /**
   * {@inheritdoc}
   */
  protected function addServices(): static {
    return parent::addServices()
      ->addEntityTypeManager()
      ->addExtensionPathResolver()
      ->addModuleInstaller()
      ->addModuleList()
      ->addState()
      ->addStringTranslation();
  }

  /**
   * Perform setup tasks when the module is installed.
   *
   * @see d8_install()
   */
  public function install(): void {
    $this->moduleInstaller()->install([
      'd8_setting',
      'd8_menu',
      'd8_seven',
      'd8_theming',
      'd8_ban',
      'd8_standwithukraine',
      'd8_log',
      'd8_link',
      'd8_mail',
      'recaptcha',
    ]);

    $path = $this->extensionPathResolver()->getPath('module', 'd8_captcha') .
      DIRECTORY_SEPARATOR . InstallStorage::CONFIG_INSTALL_DIRECTORY;

    foreach ((new FileStorage($path))->listAll() as $name) {
      $this->configFactory()->getEditable($name)->delete();
    }

    $this->moduleInstaller()->install(['d8_captcha'], FALSE);

    // Populate the default shortcut set.
    $shortcut = $this->entityTypeManager()->getStorage('shortcut')->create([
      'shortcut_set' => 'default',
      'title' => $this->t('Features'),
      'weight' => -20,
      'link' => [
        'uri' => 'internal:/admin/config/development/features',
      ],
    ]);

    $shortcut->save();

    $sandbox = [];

    $this->access($sandbox);

    $this->module('d8_' . ($module = 'config2php'), $module);

    $this->state()->set('features.current_bundle', 'd8');

    $account = $this->entityTypeManager()->getStorage('user')->load(1);

    if ($account instanceof UserInterface) {
      $account->addRole('administrator');
      $account->save();
    }
  }

  /**
   * Grant access to the search tab for a role with access to toolbar.
   */
  private function access(array &$sandbox): void {
    $storage = $this->entityTypeManager()->getStorage('user_role');

    if (!isset($sandbox['total'])) {
      $sandbox['total'] = $storage->getQuery()
        ->count()
        ->accessCheck(FALSE)
        ->execute();

      $sandbox['offset'] = 0;

      $sandbox['limit'] = Settings::get(
        'entity_update_batch_size',
        VersionHistoryController::REVISIONS_PER_PAGE,
      );
    }

    $role_ids = $storage->getQuery()
      ->range($sandbox['offset'], $sandbox['limit'])
      ->accessCheck(FALSE)
      ->execute();

    /** @var \Drupal\user\RoleInterface $role */
    foreach ($storage->loadMultiple($role_ids) as $role_id => $role) {
      if (
        $role->hasPermission('access toolbar') &&
        !$role->hasPermission('use admin toolbar search')
      ) {
        user_role_grant_permissions($role_id, ['use admin toolbar search']);
      }
    }

    $sandbox['offset'] += count($role_ids);
    $sandbox['#finished'] = $sandbox['offset'] / $sandbox['total'];
  }

  /**
   * (Un)install modules with optional checking of some other module.
   *
   * @param array|string $target
   *   The names of modules from this installation profile or drupal.org.
   * @param array|string|null $source
   *   (optional) The names of modules from drupal.org. If this parameter is
   *   specified then before installing/uninstalling target modules, it will be
   *   checked if the source modules exist. Defaults to NULL.
   * @param bool $uninstall
   *   (optional) TRUE, if modules should be installed. Defaults to FALSE.
   */
  public function module(
    array|string $target,
    array|string $source = NULL,
    bool $uninstall = FALSE
  ): void {
    if ($source !== NULL) {
      foreach ((array) $source as $name) {
        if (!$this->moduleList()->exists($name)) {
          return;
        }
      }
    }

    $method = ($uninstall ? 'un' : '') . 'install';
    $this->moduleInstaller()->$method((array) $target, !$uninstall);
  }

}
