<?php
namespace Tenguyama\Holidays\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Tenguyama\Holidays\ViewModel\HolidayViewModel;

class CustomAttribute extends Template
{
    private HolidayViewModel $holidayViewModel;

    public function __construct(
        Context $context,
        HolidayViewModel $holidayViewModel,
        array $data = []
    ) {
        $this->holidayViewModel = $holidayViewModel;
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
}
