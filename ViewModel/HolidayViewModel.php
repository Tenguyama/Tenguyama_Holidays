<?php
namespace Tenguyama\Holidays\ViewModel;

use DateTime;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tenguyama\Holidays\Model\ResourceModel\Holiday\CollectionFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class HolidayViewModel implements ArgumentInterface
{
    private $holidayCollectionFactory;
    private $timezone;

    public function __construct(
        TimezoneInterface $timezone,
        CollectionFactory $holidayCollectionFactory
    ) {
        $this->timezone = $timezone;
        $this->holidayCollectionFactory = $holidayCollectionFactory;
    }

    public function getCurrentHoliday()
    {
//        $currentDate = (new DateTime())->format('Y-m-d');
        $currentDate =  $this->timezone->date(new DateTime())->format('Y-m-d');

        $collection = $this->holidayCollectionFactory->create();
        $collection->addFieldToFilter('is_active', ['eq' => 1])
            ->addFieldToFilter('start_date', ['lteq' => $currentDate])
            ->addFieldToFilter('end_date', ['gteq' => $currentDate]);


        return $collection->getFirstItem();
    }

}
