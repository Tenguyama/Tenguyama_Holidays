<?php
namespace Tenguyama\Holidays\Model;

use Tenguyama\Holidays\Model\ResourceModel\Holiday\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var array
     */
    protected $loadedData;

    // @codingStandardsIgnoreStart
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $holidayCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $holidayCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    // @codingStandardsIgnoreEnd

    public function getData()
    {

        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();

        foreach ($items as $holiday) {
            $this->loadedData[$holiday->getHolidayId()] = $holiday->getData();
        }
        return $this->loadedData;
    }
}
