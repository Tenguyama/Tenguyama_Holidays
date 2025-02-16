<?php
namespace Tenguyama\Holidays\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use \Tenguyama\Holidays\ViewModel\HolidayViewModel;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
class CustomMessage extends Template
{
    private $holidayViewModel;
    private $timezone;

    public function __construct(
        Context $context,
        TimezoneInterface $timezone,
        HolidayViewModel $holidayViewModel,
        array $data = []
    ) {
        $this->timezone = $timezone;
        $this->holidayViewModel = $holidayViewModel;
        parent::__construct($context, $data);
    }

    public function getCurrentHolidayMessage(): string|null
    {
        $holiday = $this->holidayViewModel->getCurrentHoliday();

        if ($holiday && $holiday->getId()) {
            return sprintf(
                "%s: %s (%s - %s)",
                $holiday->getName(),
                $holiday->getGreetingText(),
                $holiday->getStartDate(),
                $holiday->getEndDate()
            );
        }

        return null;
    }
}
