<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Vgroup65\News\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

/**
 * Patch is mechanism, that allows to do atomic upgrade data changes
 */
class Patch implements SchemaPatchInterface, PatchVersionInterface
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

        // Get news_category table
        $tableName = $this->moduleDataSetup->getTable('news_category');
        // Check if the table already exists
        if ($this->moduleDataSetup->getConnection()->isTableExists($tableName) != true) {
            // Create news_category table
            $table = $this->moduleDataSetup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'category_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    'ID'
                )
                ->addColumn(
                    'title',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Title'
                )
                ->addColumn(
                    'category_url',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Name'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false],
                    'Updated At'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Status'
                )
                ->setComment('News Category Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $this->moduleDataSetup->getConnection()->createTable($table);
        }

        //create news list table
        $table = $this->moduleDataSetup->getTable('news_list');

        if ($this->moduleDataSetup->getConnection()->isTableExists($table) != true) {
            $table = $this->moduleDataSetup->getConnection()
                ->newTable($table)
                ->addColumn(
                    'news_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    'News Id'
                )
                ->addColumn(
                    'category_id',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'category ID'
                )
                ->addColumn(
                    'title',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'News Title'
                )
                ->addColumn(
                    'url_identifier',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Url Identifier'
                )
                ->addColumn(
                    'description',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'News Description'
                )
                ->addColumn(
                    'image',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'News Image'
                )
                ->addColumn(
                    'publish_date',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'News Image'
                )
                ->addColumn(
                    'store_view',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'News Store View'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false],
                    'News Craeted At'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Status'
                )
                ->setComment('News List Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $this->moduleDataSetup->getConnection()->createTable($table);
        }

        //create category news junction

        $table = $this->moduleDataSetup->getTable('category_news_junction');

        if ($this->moduleDataSetup->getConnection()->isTableExists($table) != true) {
            $table = $this->moduleDataSetup->getConnection()
                ->newTable($table)
                ->addColumn(
                    'category_news_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    'Category News Id'
                )
                ->addColumn(
                    'category_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Category ID'
                )
                ->addColumn(
                    'news_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'News ID'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false],
                    'News Craeted At'
                )
                ->setComment('Category News Junction Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $this->moduleDataSetup->getConnection()->createTable($table);
        }

        //create category news configuration
        $table = $this->moduleDataSetup->getTable('news_configuration');
        if ($this->moduleDataSetup->getConnection()->isTableExists($table) != true) {
            $table = $this->moduleDataSetup->getConnection()
                ->newTable($table)
                ->addColumn(
                    'news_config',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    'Category News Id'
                )
                ->addColumn(
                    'no_of_news',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Category ID'
                )
                ->addColumn(
                    'auto_rotate',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Auto Rotate'
                )
                ->addColumn(
                    'top_menu_text',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Tope menu text'
                )
                ->addColumn(
                    'display_top_menu',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Dislay top menu'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false],
                    'News Craeted At'
                )
                ->setComment('News Configurtion Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $this->moduleDataSetup->getConnection()->createTable($table);
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

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '2.0.6';
    }
}
