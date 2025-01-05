<?php
namespace Tenguyama\Holidays\Model\ResourceModel\Holiday;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Tenguyama\Holidays\Model\Holiday',
            'Tenguyama\Holidays\Model\ResourceModel\Holiday'
        );
    }
}
