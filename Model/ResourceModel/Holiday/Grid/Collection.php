<?php
namespace Tenguyama\Holidays\Model\ResourceModel\Holiday\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\Document as HolidayModel;
use Tenguyama\Holidays\Model\ResourceModel\Holiday\Collection as HolidayCollection;
use Magento\Framework\Api\Search\SearchResultInterface;
use Tenguyama\Holidays\Model\Holiday;
class Collection extends HolidayCollection implements SearchResultInterface
{

    protected $aggregations;

    protected $_eventPrefix = 'tenguyama_holidays_holiday_grid_collection';
    protected $_eventObject = 'holiday_grid_collection';
    protected Holiday $holidayModel;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = HolidayModel::class,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
        $this->holidayModel = \Magento\Framework\App\ObjectManager::getInstance()->create(Holiday::class); // створення об'єкта Holiday
    }

    public function getAggregations()
    {
        return $this->aggregations;
    }

    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    public function getSearchCriteria()
    {
        return null;
    }

    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    public function getTotalCount()
    {
//        return $this->getSize();
        try {
            return $this->getSize();
        } catch (\Exception $e) {
            error_log('[GRID ERROR] ' . $e->getMessage());
            return 0;
        }
    }

    public function setTotalCount($totalCount)
    {
        return $this;
    }

    public function setItems(array $items = null)
    {
        return $this;
    }
    public function getItems()
    {
        return parent::getItems(); // викликає метод батьківського класу для отримання елементів
    }
}
