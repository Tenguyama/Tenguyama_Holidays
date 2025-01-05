<?php
namespace Tenguyama\Holidays\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use Zend_Db_Exception;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists('tenguyama_holidays')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('tenguyama_holidays')
                )->addColumn(
                    'holiday_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Holiday ID'
                )->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Holiday Name'
                )->addColumn(
                    'start_date',
                    Table::TYPE_DATE,
                    null,
                    ['nullable' => false],
                    'Start Date'
                )->addColumn(
                    'exact_date',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false],
                    'Exact Date'
                )->addColumn(
                    'end_date',
                    Table::TYPE_DATE,
                    null,
                    ['nullable' => true],
                    'End Date'
                )->addColumn(
                    'greeting_text',
                    Table::TYPE_TEXT,
                    '256',
                    [],
                    'Greeting Text'
                )->addColumn(
                    'is_active',
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Is Active'
                )->setComment('Holiday Information Table');

            $installer->getConnection()->createTable($table);
            $table->addIndex(
                $installer->getIdxName('tenguyama_holidays', ['start_date', 'end_date']),
                ['start_date', 'end_date']
            );

        }

        $installer->endSetup();
    }
}


