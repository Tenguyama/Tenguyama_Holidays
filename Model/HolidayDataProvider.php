<?php
namespace Tenguyama\Holidays\Model;

use Tenguyama\Holidays\Model\ResourceModel\Holiday\CollectionFactory as HolidayCollectionFactory;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as CustomerGroupCollectionFactory;
use Tenguyama\Holidays\Model\ResourceModel\HolidayCustomerGroup\CollectionFactory as HolidayCustomerGroupCollectionFactory;

class HolidayDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var array
     */
    protected $loadedData;
    protected CustomerGroupCollectionFactory $customerGroupCollectionFactory;
    protected HolidayCustomerGroupCollectionFactory $holidayCustomerGroupCollectionFactory;
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        HolidayCollectionFactory $holidayCollectionFactory,
        CustomerGroupCollectionFactory $customerGroupCollectionFactory,
        HolidayCustomerGroupCollectionFactory $holidayCustomerGroupCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $holidayCollectionFactory->create();
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
        $this->holidayCustomerGroupCollectionFactory = $holidayCustomerGroupCollectionFactory;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }


    public function getData()
    {
        $items = $this->collection->getItems();
        $allCustomerGroups = $this->getCustomerGroupOptions();
        foreach ($items as $holiday) {
            $holidayData = $holiday->getData();
            $selectedGroups = $this->getSelectedCustomerGroups($holiday->getHolidayId());
            if (empty($selectedGroups)) {
                $selectedGroups = [reset($allCustomerGroups)['value']];
            }
            $holidayData['customer_group'] = $selectedGroups;
            $this->loadedData[$holiday->getHolidayId()] = $holidayData;
        }
        return $this->loadedData;
    }



    /**
     * Отримуємо групи користувачів для конкретного свята
     */
    protected function getSelectedCustomerGroups($holidayId)
    {
        $collection = $this->holidayCustomerGroupCollectionFactory->create();
        $collection->addFieldToFilter('holiday_id', $holidayId);
        return $collection->getColumnValues('customer_group_id'); // Отримуємо масив ID груп
    }

    public function getCustomerGroupOptions()
    {
        $groups = $this->customerGroupCollectionFactory->create()->toOptionArray();
        return $groups;
    }
}
