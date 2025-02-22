<?php
namespace Tenguyama\Holidays\Model;

use Magento\Framework\Model\AbstractModel;
use Tenguyama\Holidays\Model\ResourceModel\Holiday as HolidayResourceModel;
use Tenguyama\Holidays\Model\HolidayCustomerGroupFactory;
use Tenguyama\Holidays\Model\ResourceModel\HolidayCustomerGroup\CollectionFactory as HolidayCustomerGroupCollectionFactory;

class Holiday extends AbstractModel { //implements IdentityInterface {

    const CACHE_TAG = 'tenguyama_holidays';
    protected $_cacheTag = 'tenguyama_holidays';
    protected $_eventPrefix = 'tenguyama_holidays';


    protected $holidayCustomerGroupFactory;
    protected $holidayCustomerGroupCollectionFactory;

    public function __construct(
        HolidayCustomerGroupFactory $holidayCustomerGroupFactory,
        HolidayCustomerGroupCollectionFactory $holidayCustomerGroupCollectionFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $collection = null
    ) {
        $this->holidayCustomerGroupFactory = $holidayCustomerGroupFactory;  // додано
        $this->holidayCustomerGroupCollectionFactory = $holidayCustomerGroupCollectionFactory;  // додано
        parent::__construct($context, $registry, $resource, $collection);
    }

    protected function _construct()
    {
        $this->_init(HolidayResourceModel::class);
    }

    /**
     * Отримує групи користувачів для конкретного свята
     *
     * @param int $holidayId
     * @return \Tenguyama\Holidays\Model\ResourceModel\HolidayCustomerGroup\Collection
     */
    public function getCustomerGroupsForHoliday($holidayId)
    {
        // Отримуємо колекцію з групами для свят
        $collection = $this->holidayCustomerGroupCollectionFactory->create();
        $collection->addFieldToFilter('holiday_id', $holidayId); // фільтруємо по id свята

        return $collection;
    }
}
