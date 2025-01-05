<?php
namespace Tenguyama\Holidays\Model\ResourceModel;
class Holiday extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }
    protected function _construct()
    {
        $this->_init('tenguyama_holidays', 'holiday_id');
    }
}
