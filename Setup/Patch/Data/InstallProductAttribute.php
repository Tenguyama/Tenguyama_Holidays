<?php

namespace Tenguyama\Holidays\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InstallProductAttribute implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private EavSetupFactory $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        // Додаємо атрибут "is_festive"
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'is_festive',
            [
                'type' => 'int',
                'label' => 'Festive',
                'input' => 'boolean',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
                'searchable' => false,
                'filterable' => true,
                'comparable' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false
            ]
        );

        /**
         * TODO я не впевнений що цей варіант правильний.
         * Але ціль виконується, а саме переглянувши які зв'язки у атрибута NEW (147 id у мене в бд)
         * Так як не зрозуміло який з product-details відповідає за відображення в адмінці (цих груп 5 з id: 7,30,40,50,60,70,80,90)
         * У всіх них різні attr_set_id, які як я зрозумів один з них дефолтний - інші під категорії (Bag\Gear і тд)
         * і типу якщо я беру $attributeSetId = $eavSetup->getDefaultAttributeSetId(\Magento\Catalog\Model\Product::ENTITY);
         * то додаю атрибут тільки в дефолтний продутк детейлс (id 7), через що в категоріях де є свій продукт детейлс мій атрибут не відображається
         */
        // Запит на отримання всіх груп з кодом "product-details"
        $connection = $this->moduleDataSetup->getConnection();
        $select = $connection->select()
            ->from(['eag' => $connection->getTableName('eav_attribute_group')])
            ->where('eag.attribute_group_code = ?', 'product-details');

        $attributeGroups = $connection->fetchAll($select);

        // Проходимо по всіх групах і додаємо атрибут
        foreach ($attributeGroups as $attributeGroup) {
            $eavSetup->addAttributeToGroup(
                \Magento\Catalog\Model\Product::ENTITY,
                $attributeGroup['attribute_set_id'],
                $attributeGroup['attribute_group_id'],
                'is_festive',
                9999
            );
        }

        $this->moduleDataSetup->endSetup();
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
