<?php

namespace Tenguyama\Holidays\Setup\Patch\Data;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
class InstallPensionersCustomerGroup implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private GroupRepositoryInterface $groupRepository;
    private GroupInterfaceFactory $groupFactory;
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        GroupRepositoryInterface $groupRepository,
        GroupInterfaceFactory $groupFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->groupRepository = $groupRepository;
        $this->groupFactory = $groupFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        // Створюємо нову групу "Pensioners"
        $customerGroup = $this->groupFactory->create();
        $customerGroup->setCode('Pensioners');
        // 3 - це стандартний ID для "Retail Customer", принайні всі стандартні групи мають саме такий ідентифікатор
        $customerGroup->setTaxClassId(3);

        // Перевіряємо, чи група вже існує, перш ніж зберігати
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $existingGroups = $this->groupRepository->getList($searchCriteria);
        foreach ($existingGroups->getItems() as $group) {
            if ($group->getCode() === 'Pensioners') {
                return;
            }
        }
        $this->groupRepository->save($customerGroup);

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
