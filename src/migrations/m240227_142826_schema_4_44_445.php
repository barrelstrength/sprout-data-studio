<?php

namespace BarrelStrength\SproutDataStudio\migrations;

use BarrelStrength\Sprout\core\db\m000000_000000_sprout_plugin_migration;
use BarrelStrength\Sprout\core\db\SproutPluginMigrationInterface;
use BarrelStrength\SproutDataStudio\SproutDataStudio;

class m240227_142826_schema_4_44_445 extends m000000_000000_sprout_plugin_migration
{
    public function getPluginInstance(): SproutPluginMigrationInterface
    {
        return SproutDataStudio::getInstance();
    }
}