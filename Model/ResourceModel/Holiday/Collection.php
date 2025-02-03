<?php
namespace Tenguyama\Holidays\Model\ResourceModel\Holiday;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'holiday_id';
    protected $_eventPrefix = 'tenguyama_holidays_holiday_collection';
    protected $_eventObject = 'holiday_collection';

    protected function _construct()
    {
        $this->_init(
            'Tenguyama\Holidays\Model\Holiday',
            'Tenguyama\Holidays\Model\ResourceModel\Holiday'
        );
    }
}

