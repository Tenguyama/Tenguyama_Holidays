<?php
namespace Tenguyama\Holidays\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
class Holiday extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('tenguyama_holidays', 'holiday_id');
    }
}
