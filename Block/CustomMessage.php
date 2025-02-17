<?php
namespace Tenguyama\Holidays\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Tenguyama\Holidays\ViewModel\HolidayViewModel;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class CustomMessage extends Template
{
    private HolidayViewModel $holidayViewModel;
    private TimezoneInterface $timezone;
    private ScopeConfigInterface $scopeConfig;

    private const XML_PATH_ENABLED = 'holidays/general/enable';


    public function __construct(
        Context $context,
        TimezoneInterface $timezone,
        HolidayViewModel $holidayViewModel,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->timezone = $timezone;
        $this->holidayViewModel = $holidayViewModel;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    public function getCurrentHolidayMessage(): ?string
    {
        if (!$this->isModuleEnabled()) {
            return null;
        }

        $holiday = $this->holidayViewModel->getCurrentHoliday();

        if (!$holiday || !$holiday->getId()) {
            return null;
        }

        return sprintf(
                "%s: %s (%s - %s)",
                $holiday->getName(),
                $holiday->getGreetingText(),
                $this->timezone->date($holiday->getStartDate())->format('Y-m-d H:i:s'),
                $this->timezone->date($holiday->getEndDate())->format('Y-m-d H:i:s')
        );
    }


    private function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
}
