<?php

namespace Tenguyama\Holidays\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Api\AttributeGroupRepositoryInterface;
use Magento\Eav\Model\Entity\Attribute\GroupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;

class InstallProductAttribute implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private EavSetupFactory $eavSetupFactory;
    private SetFactory $attributeSetFactory;
    private AttributeGroupRepositoryInterface $attributeGroupRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        SetFactory $attributeSetFactory,
        AttributeGroupRepositoryInterface $attributeGroupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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

        // Запит на отримання всіх груп з кодом "product-details"
//        $connection = $this->moduleDataSetup->getConnection();
//        $select = $connection->select()
//            ->from(['eag' => $connection->getTableName('eav_attribute_group')])
//            ->where('eag.attribute_group_code = ?', 'product-details');
//
//        $attributeGroups = $connection->fetchAll($select);
//
//        // Проходимо по всіх групах і додаємо атрибут
//        foreach ($attributeGroups as $attributeGroup) {
//            $eavSetup->addAttributeToGroup(
//                \Magento\Catalog\Model\Product::ENTITY,
//                $attributeGroup['attribute_set_id'],
//                $attributeGroup['attribute_group_id'],
//                'is_festive',
//                9999
//            );
//        }

//  Варіант без "прямого запиту до бд"
        $attributeSets = $this->attributeSetFactory->create()->getCollection();

        foreach ($attributeSets as $attributeSet) {
            $attributeSetId = $attributeSet->getId();

            // Отримуємо групу "product-details" для цього атрибут-сету
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('attribute_group_code', 'product-details', 'eq')
                ->addFilter('attribute_set_id', $attributeSetId, 'eq')
                ->create();
            $attributeGroupCollection = $this->attributeGroupRepository->getList($searchCriteria);

            foreach ($attributeGroupCollection->getItems() as $attributeGroup) {
                if ($attributeGroup->getAttributeGroupCode() === 'product-details') {
                    $eavSetup->addAttributeToGroup(
                        Product::ENTITY,
                        $attributeSetId,
                        $attributeGroup->getAttributeGroupId(),
                        'is_festive',
                        9999
                    );
                }
            }
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
