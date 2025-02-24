<?php
namespace Tenguyama\Holidays\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Tenguyama\Holidays\Model\ResourceModel\Holiday\CollectionFactory;

class MassStatus extends Action
{
    protected Filter $filter;

    protected CollectionFactory $collectionFactory;

    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {

//        echo '<pre>';
//        var_dump($this->getRequest()->getParams());
//        echo '</pre>';
//        die;

        $statusValue = $this->getRequest()->getParam('status');
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        foreach ($collection as $item) {
//  так як "статус" у нас в таблиці це is_active,
//  то використаю трохи іншу констрункцію,
//  але в загальному функціонал масового ввімкнення\вимкнення св'ят є актуальним для даного завдання
//            $item->setStatus($statusValue);



            $item->setIsActive($statusValue);
//  на випадок якщо "магічний" метод __call() у AbstractModel не спарсить назву поля
//            $item->setData('is_active', $statusValue);

            $item->save();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been modified.', $collection->getSize()));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
