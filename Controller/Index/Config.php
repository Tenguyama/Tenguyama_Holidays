<?php
namespace Tenguyama\Holidays\Controller\Index;

use \Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Action\Context;
use \Tenguyama\Holidays\Helper\Data as Helper;
class Config extends Action
{
    protected $helperData;
    public function __construct(
        Context $context,
        Helper $helperData
    ){
        $this->helperData = $helperData;
        return parent::__construct($context);
    }
    public function execute()
    {
        // TODO: for testing module config settings
        echo 'Status: ' . $this->helperData->getGeneralConfig('enable');
        echo '<br>';
        echo 'Title: ' . $this->helperData->getGeneralConfig('display_text');
        exit();
    }
}
