<?php
namespace Tenguyama\Holidays\Model\ResourceModel\HolidayCustomerGroup;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tenguyama\Holidays\Model\HolidayCustomerGroup as Model;
use Tenguyama\Holidays\Model\ResourceModel\HolidayCustomerGroup as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }


}


