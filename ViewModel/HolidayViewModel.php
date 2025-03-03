<?php
namespace Tenguyama\Holidays\ViewModel;

use DateTime;
use Magento\Framework\Registry;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tenguyama\Holidays\Model\ResourceModel\Holiday\CollectionFactory as HolidayCollectionFactory;
use Tenguyama\Holidays\Model\ResourceModel\HolidayCustomerGroup\CollectionFactory as HolidayCustomerGroupCollectionFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
class HolidayViewModel implements ArgumentInterface
{
    protected PriceHelper $priceHelper;
    private Registry $_registry;
    private HolidayCollectionFactory $holidayCollectionFactory;
    private HolidayCustomerGroupCollectionFactory $holidayCustomerGroupCollectionFactory;
    private ProductCollectionFactory $productCollectionFactory;
    private TimezoneInterface $timezone;
    private ScopeConfigInterface $scopeConfig;
    protected Session $customerSession;
    private const XML_PATH_ENABLED = 'holidays/general/enable';

    public function __construct(
        Registry $registry,
        Session $customerSession,
        TimezoneInterface $timezone,
        ScopeConfigInterface $scopeConfig,
        PriceHelper $priceHelper,
        HolidayCollectionFactory $holidayCollectionFactory,
        HolidayCustomerGroupCollectionFactory $holidayCustomerGroupCollectionFactory,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->_registry = $registry;
        $this->timezone = $timezone;
        $this->scopeConfig = $scopeConfig;
        $this->priceHelper = $priceHelper;
        $this->holidayCollectionFactory = $holidayCollectionFactory;
        $this->holidayCustomerGroupCollectionFactory = $holidayCustomerGroupCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->customerSession = $customerSession;
    }

    public function getCurrentHoliday()
    {
        if (!$this->isModuleEnabled()) {
            return null;
        }

        $currentDate =  $this->timezone->date(new DateTime())->format('Y-m-d H:i:s');
        $collection = $this->holidayCollectionFactory->create();
        $collection->addFieldToFilter('is_active', ['eq' => 1])
            ->addFieldToFilter('start_date', ['lteq' => $currentDate])
            ->addFieldToFilter('end_date', ['gteq' => $currentDate]);

        $holiday = $collection->getFirstItem();

        if(!$holiday || !$holiday->getId()) {
            return null;
        }

        $customerGroupId = $this->getCustomerGroupId();
        $holidayCustomerGroups = $this->getCustomerGroupsForHoliday($holiday->getHolidayId());
        $groupIds = array_column($holidayCustomerGroups, 'customer_group_id');

        if(!in_array($customerGroupId, $groupIds)) {
            return null;
        }

        return $holiday;
    }

    private function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    private function getCustomerGroupId(): int
    {
        if ($this->customerSession->isLoggedIn()) {
            return (int) $this->customerSession->getCustomerGroupId();
        }

        return GroupInterface::NOT_LOGGED_IN_ID;
    }

    public function getCustomerGroupsForHoliday($holidayId)
    {
        // Отримуємо колекцію з групами для свят
        $collection = $this->holidayCustomerGroupCollectionFactory->create();
        $collection->addFieldToFilter('holiday_id', $holidayId); // фільтруємо по id свята
        return $collection->getData();
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    public function getOldPrice($product)
    {
        if (!$product) {
            return null;
        }

//        $oldPrice = $product->getFinalPrice();
        $oldPrice = $product->getOrigData('price');
        return $this->priceHelper->currency($oldPrice, true, false);

    }

    public function hasSpecialPrice($product)
    {
        if (!$product) {
            return false;
        }

        return $product->getFinalPrice() < $product->getData('price');
//        return $product->getOrigData('price') < $product->getData('price');
    }


    public function getHolidayProductAttribute($product): ?string
    {
        if (!$product || $product->getIsFestive() != '1') {
            return null;
        }

        $holiday = $this->getCurrentHoliday();
        if (!$holiday || !$holiday->getId()) {
            return null;
        }

        return sprintf(
            "Festive discount: %s ",
            $holiday->getDiscount()."%"
        );
    }



}
