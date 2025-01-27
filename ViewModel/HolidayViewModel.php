<?php
namespace Tenguyama\Holidays\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tenguyama\Holidays\Model\ResourceModel\Holiday\Collection as CollectionFactory;

class HolidayViewModel implements ArgumentInterface
{
    private $holidayCollectionFactory;

    public function __construct(
        CollectionFactory $holidayCollectionFactory
    ) {
        $this->holidayCollectionFactory = $holidayCollectionFactory;
    }

    public function getCurrentHoliday()
    {
        $currentDate = (new \DateTime())->format('Y-m-d');

        $collection = $this->holidayCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('start_date', ['lteq' => $currentDate])
            ->addFieldToFilter('end_date', ['gteq' => $currentDate]);

        return $collection->getFirstItem();
    }

}
