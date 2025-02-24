<?php
namespace Tenguyama\Holidays\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Tenguyama\Holidays\ViewModel\HolidayViewModel;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
class CustomAttribute extends Template
{
    private ProductCollectionFactory $productCollectionFactory;
    private HolidayViewModel $holidayViewModel;
    public function __construct(
        Context $context,
        HolidayViewModel $holidayViewModel,
        ProductCollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->holidayViewModel = $holidayViewModel;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getHolidayProductAttribute(): ?string
    {
        // Перевіряєм чи є свято, якщо свята нема - то інформація, що пордукт святковий є зайвою
        $holiday = $this->holidayViewModel->getCurrentHoliday();

        if (!$holiday || !$holiday->getId()) {
            return null;
        }

        // Отримуємо поточний продукт
        $product = $this->holidayViewModel->getCurrentProduct();

        if (!$product || $product->getIsFestive() != '1') {
            return null;
        }

        return sprintf(
            "Festive product: has %s discount",
            $holiday->getDiscount()."%"
        );
    }


    public function getCategoryProductAttribute($productId): ?string
    {
        $holiday = $this->holidayViewModel->getCurrentHoliday();
        if (!$holiday || !$holiday->getId()) {
            return null;
        }


        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['eq' => $productId]);
        $product = $collection->getFirstItem();

        if (!$product || $product->getIsFestive() != '1') {
            return null;
        }

        return sprintf(
            "Festive discount: %s ",
            $holiday->getDiscount()."%"
        );

    }

}
