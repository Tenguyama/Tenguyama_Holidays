<?php

namespace Tenguyama\Holidays\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Delete extends Action{

    protected $modelHoliday;

    public function __construct(
        Context $context,
        \Tenguyama\Holidays\Model\Holiday $holidayFactory
    ){
        parent::__construct($context);
        $this->modelHoliday = $holidayFactory;
    }


    protected function _isAllowed(){
        return $this->_authorization->isAllowed('Tenguyama_Holidays::holiday_delete');
    }

    public function execute(){
        $id = $this->getRequest()->getParam('holiday_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->modelHoliday;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Holiday deleted successfully.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('Holiday does not exist.'));
        return $resultRedirect->setPath('*/*/');
    }

}
