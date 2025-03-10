<?php
namespace Tenguyama\Holidays\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Tenguyama\Holidays\ViewModel\HolidayViewModel;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class CustomMessage extends Template
{
    private HolidayViewModel $holidayViewModel;
    private TimezoneInterface $timezone;


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

    public function getCurrentHolidayMessage(): ?string
    {
        $holiday = $this->holidayViewModel->getCurrentHoliday();

        if (!$holiday || !$holiday->getId()) {
            return null;
        }

        return sprintf(
                "%s: %s (%s - %s) <br> Sales: %s",
                $holiday->getName(),
                $holiday->getGreetingText(),
                $this->timezone->date($holiday->getStartDate())->format('Y-m-d H:i:s'),
                $this->timezone->date($holiday->getEndDate())->format('Y-m-d H:i:s'),
                $holiday->getDiscount()."% !!!"
        );
    }
}
