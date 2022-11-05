<?php

namespace BarrelStrength\SproutDataStudio;

use BarrelStrength\Sprout\core\db\MigrationHelper;
use BarrelStrength\Sprout\core\db\SproutPluginMigrationInterface;
use BarrelStrength\Sprout\core\db\SproutPluginMigrator;
use BarrelStrength\Sprout\core\editions\Edition;
use BarrelStrength\Sprout\core\modules\Modules;
use BarrelStrength\Sprout\datastudio\DataStudioModule;
use Craft;
use craft\base\Plugin;
use craft\db\MigrationManager;
use craft\errors\MigrationException;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\UrlHelper;
use yii\base\Event;
use yii\base\InvalidConfigException;

class SproutDataStudio extends Plugin implements SproutPluginMigrationInterface
{
    public const EDITION_LITE = 'lite';
    public const EDITION_PRO = 'pro';

    public string $minVersionRequired = '3.10.1';

    /**
     * @inheritDoc
     */
    public static function editions(): array
    {
        return [
            self::EDITION_LITE,
            self::EDITION_PRO,
        ];
    }

    public static function getSchemaDependencies(): array
    {
        return [
            DataStudioModule::class,
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public function getMigrator(): MigrationManager
    {
        return SproutPluginMigrator::make($this);
    }

    public string $schemaVersion = '0.0.1';

    public function init(): void
    {
        parent::init();

        Event::on(
            Modules::class,
            Modules::EVENT_REGISTER_SPROUT_AVAILABLE_MODULES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = DataStudioModule::class;
            }
        );
        $this->instantiateSproutModules();
        $this->grantModuleEditions();
    }

    protected function instantiateSproutModules(): void
    {
        DataStudioModule::isEnabled() && DataStudioModule::getInstance();
    }

    protected function grantModuleEditions(): void
    {
        if ($this->edition === self::EDITION_PRO) {
            DataStudioModule::isEnabled() && DataStudioModule::getInstance()->grantEdition(Edition::PRO);
        }
    }

    /**
     * @throws MigrationException
     */
    protected function afterInstall(): void
    {
        MigrationHelper::runMigrations($this);

        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            return;
        }

        // Redirect to welcome page
        $url = UrlHelper::cpUrl('sprout/welcome/data-studio');
        Craft::$app->getResponse()->redirect($url)->send();
    }

    /**
     * @throws MigrationException
     * @throws InvalidConfigException
     */
    protected function beforeUninstall(): void
    {
        MigrationHelper::runUninstallMigrations($this);
    }
}
