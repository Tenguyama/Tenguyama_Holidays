<?php
namespace Tenguyama\Holidays\Model\ResourceModel\Holiday;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tenguyama\Holidays\Model\Holiday as HolidayModel;
use Tenguyama\Holidays\Model\ResourceModel\Holiday as HolidayResourceModel;
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'holiday_id';
    protected $_eventPrefix = 'tenguyama_holidays_holiday_collection';
    protected function _construct()
    {
        $this->_init(
            HolidayModel::class,
            HolidayResourceModel::class
        );
    }
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

}

