<?php

namespace Tenguyama\Holidays\Setup\Patch\Schema;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
class AddColumn implements SchemaPatchInterface
{
    private $moduleDataSetup;public function __construct(
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
        $this->moduleDataSetup->getConnection()->addColumn(
            $this->moduleDataSetup->getTable('tenguyama_holidays'),
            'discount',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '4,2', // Дозволяє значення до 99.99
                'nullable' => false,
                'default' => '0.00',
                'comment' => 'Discount Percentage',
            ]
        );
        $this->moduleDataSetup->endSetup();
    }
}
