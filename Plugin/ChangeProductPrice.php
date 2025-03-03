<?php
namespace Tenguyama\Holidays\Plugin;

use Magento\Catalog\Model\Product;
use Tenguyama\Holidays\ViewModel\HolidayViewModel;

class ChangeProductPrice {

    private HolidayViewModel $holidayViewModel;

    public function __construct(HolidayViewModel $holidayViewModel)
    {
        $this->holidayViewModel = $holidayViewModel;
    }
    public function afterGetPrice(Product $subject, $result)
    {
        // Чи продукту присвоєний атрибут "святковий"
        if ($subject->getIsFestive() == '1') {
            // Отримуємо свято, якщо воно є
            $holiday = $this->holidayViewModel->getCurrentHoliday();

            if ($holiday) {
                // Якщо свято є, отримуємо знижку з нього
                $discount = $holiday->getDiscount();

                if ($discount) {
                    // Якщо знижка є, застосовуємо її до ціни
                    $discountedPrice = $result - ($result * ($discount / 100));


                    // Встановлюємо знижку як спеціальну ціну
                    // $subject->setSpecialPrice($discountedPrice);

                    return $discountedPrice;
                }
            }
        }
        return $result;
    }
}
