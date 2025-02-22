<?php
namespace Tenguyama\Holidays\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Tenguyama\Holidays\Model\Source\HolidayCustomerGroupOptions;
use Magento\Framework\App\RequestInterface;


class HolidayOptions implements ArrayInterface
{

    protected HolidayCustomerGroupOptions $holidayGroupOptions;
    protected RequestInterface $request;


    public function __construct(
        HolidayCustomerGroupOptions $holidayGroupOptions,
        RequestInterface $request
    ) {
        $this->holidayGroupOptions = $holidayGroupOptions;
        $this->request = $request;
    }


    public function getHolidayId()
    {
        // Отримуємо ідентифікатор свята з параметрів запиту
        return $this->request->getParam('holiday_id');
    }

    public function toOptionArray()
    {
        $holidayId = $this->getHolidayId();

        // Викликаємо метод для отримання опцій
        $result = $this->holidayGroupOptions->toOptionArray($holidayId);

        return $result;
    }
}
