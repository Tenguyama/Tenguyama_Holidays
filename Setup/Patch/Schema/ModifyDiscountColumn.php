<?php

namespace Tenguyama\Holidays\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class ModifyDiscountColumn implements SchemaPatchInterface
{
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $this->moduleDataSetup->getConnection()->modifyColumn(
            $this->moduleDataSetup->getTable('tenguyama_holidays'),
            'discount',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '5,2',
                'nullable' => false,
                'default' => '0.00',
                'comment' => 'Discount Percentage',
            ]
        );

        $this->moduleDataSetup->endSetup();
    }
}
