<?php
namespace Tenguyama\Holidays\Block;

use Magento\Framework\View\Element\Template;
class CustomMessage extends Template
{
    private $holidayViewModel;

    public function __construct(
        Template\Context $context,
        \Tenguyama\Holidays\ViewModel\HolidayViewModel $holidayViewModel,
        array $data = []
    ) {
        $this->holidayViewModel = $holidayViewModel;
        parent::__construct($context, $data);
    }

    public function getCurrentHolidayMessage(): string|null
    {
        $holiday = $this->holidayViewModel->getCurrentHoliday();

        if ($holiday && $holiday->getId()) {
            return sprintf(
                "Current Holiday: %s (%s - %s)",
                $holiday->getName(),
                $holiday->getStartDate(),
                $holiday->getEndDate() ?? 'N/A'
            );
        }

//        return "TEST";
        return null;
    }
}
