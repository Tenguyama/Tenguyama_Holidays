<?php

namespace Tenguyama\Holidays\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Add extends Action{

    public function execute(){
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Add New Holiday'));
        return $resultPage;
    }
}
