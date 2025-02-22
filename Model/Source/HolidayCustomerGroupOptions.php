<?php
namespace Tenguyama\Holidays\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as CustomerGroupCollectionFactory;
use Tenguyama\Holidays\Model\Holiday as HolidayModel;

class HolidayCustomerGroupOptions extends AbstractSource
{
    protected HolidayModel $holidayModel;
    protected CustomerGroupCollectionFactory $customerGroupCollectionFactory;


    public function __construct(
        CustomerGroupCollectionFactory $customerGroupCollectionFactory,
        HolidayModel $holidayModel

    ) {
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
        $this->holidayModel = $holidayModel;
    }

    public function getAllOptions($holidayId = null)
    {
        $options = [];

        $groups = $this->getHolidayGroups($holidayId);

        foreach ($groups as $group) {
            $options[] = [
                'label' => $group['label'],
                'value' => $group['value'],
                'selected' => $group['selected'],
            ];
        }

        return $options;
    }

    public function toOptionArray($holidayId = null)
    {
        return $this->getAllOptions($holidayId);
    }

    protected function getHolidayGroups($holidayId)
    {
        $allCustomerGroups = $this->getCustomerGroupOptions();

        if (!empty($holidayId)) {
            $selectedGroupsCollection = $this->holidayModel->getCustomerGroupsForHoliday($holidayId);
            $selectedGroups = $selectedGroupsCollection->getSize() > 0
                ? $selectedGroupsCollection->getColumnValues('customer_group_id')
                : [];
        }

        if (empty($selectedGroups)) {
            $selectedGroups = [reset($allCustomerGroups)['value']];
        }

        foreach ($allCustomerGroups as &$group) {
            $group['selected'] = in_array($group['value'], $selectedGroups);
        }
        unset($group);

        return $allCustomerGroups;
    }
    protected function getCustomerGroupOptions()
    {
        return $this->customerGroupCollectionFactory->create()->toOptionArray();
    }
}
