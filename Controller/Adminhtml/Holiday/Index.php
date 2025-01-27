<?php
namespace Tenguyama\Holidays\Controller\Adminhtml\Holiday;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context ;
use \Magento\Framework\View\Result\PageFactory;
class Index extends Action
{
    protected $resultPageFactory = false;
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Tenguyama_Holidays::holiday');
    }
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Holiday')));
        return $resultPage;
    }
}
