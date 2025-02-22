<?php
namespace Tenguyama\Holidays\Model;

use Magento\Framework\Model\AbstractModel;
use Tenguyama\Holidays\Model\ResourceModel\HolidayCustomerGroup as HolidayCustomerGroupResourceModel;
class HolidayCustomerGroup extends AbstractModel {

    const CACHE_TAG = 'tenguyama_holiday_customer_groups';
    protected $_cacheTag = 'tenguyama_holiday_customer_groups';
    protected $_eventPrefix = 'tenguyama_holiday_customer_groups';

    protected function _construct()
    {
        $this->_init(HolidayCustomerGroupResourceModel::class);
    }

}
