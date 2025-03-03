<?php

namespace Tenguyama\Holidays\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class CreateHolidayCustomerGroupTable implements SchemaPatchInterface
{
    private $moduleDataSetup;
    private $schemaSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        SchemaSetupInterface $schemaSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->schemaSetup = $schemaSetup;
    }

    public function apply()
    {
        $setup = $this->moduleDataSetup;
        $schemaSetup = $this->schemaSetup;

        $setup->startSetup();

        if (!$setup->getConnection()->isTableExists($setup->getTable('tenguyama_holiday_customer_groups'))) {
            $table = $setup->getConnection()->newTable($setup->getTable('tenguyama_holiday_customer_groups'))
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true, 'primary' => true, 'identity' => true],
                    'ID'
                )
                ->addColumn(
                    'holiday_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Holiday ID'
                )
                ->addColumn(
                    'customer_group_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Customer Group ID'
                )
                ->addIndex(
                    $schemaSetup->getIdxName('tenguyama_holiday_customer_groups', ['holiday_id', 'customer_group_id'], \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                    ['holiday_id', 'customer_group_id'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addForeignKey(
                    $schemaSetup->getFkName('tenguyama_holiday_customer_groups', 'holiday_id', 'tenguyama_holidays', 'holiday_id'),
                    'holiday_id',
                    $setup->getTable('tenguyama_holidays'),
                    'holiday_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $schemaSetup->getFkName('tenguyama_holiday_customer_groups', 'customer_group_id', 'customer_group', 'customer_group_id'),
                    'customer_group_id',
                    $setup->getTable('customer_group'),
                    'customer_group_id',
                    Table::ACTION_CASCADE
                );

            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}

