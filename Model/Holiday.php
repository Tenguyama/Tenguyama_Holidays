<?php
namespace Tenguyama\Holidays\Model;
class Holiday extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Tenguyama\Holidays\Model\ResourceModel\Holiday');
    }
}
