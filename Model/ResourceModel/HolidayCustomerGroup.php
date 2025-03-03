<?php
namespace Tenguyama\Holidays\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
class HolidayCustomerGroup extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('tenguyama_holiday_customer_groups', 'id');
    }

    public function getIdFieldName()
    {
        return 'id';
    }
}
