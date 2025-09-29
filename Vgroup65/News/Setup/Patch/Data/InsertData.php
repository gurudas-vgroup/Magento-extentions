<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Vgroup65\News\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch is mechanism, that allows to do atomic upgrade data changes
 */
class InsertData implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $tableName = $this->moduleDataSetup->getTable('news_configuration');
        // Check if the table already exists
        if ($this->moduleDataSetup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $defaultData =
                [
                'no_of_news' => '0',
                'auto_rotate' => '0',
                'top_menu_text' => 'News Sample',
                'display_top_menu' => '1',
            ];

            $this->moduleDataSetup->getConnection()->insert(
                $tableName,
                $defaultData
            );
        }

    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }
}
