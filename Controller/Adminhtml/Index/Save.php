<?php

namespace Tenguyama\Holidays\Controller\Adminhtml\Index;

use DateTime;
use DateTimeZone;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Tenguyama\Holidays\Model\Holiday;
use Tenguyama\Holidays\Model\HolidayCustomerGroupFactory;
use Tenguyama\Holidays\Model\ResourceModel\HolidayCustomerGroup\CollectionFactory as HolidayCustomerGroupCollectionFactory;


class Save extends Action{

    protected HolidayCustomerGroupFactory $holidayCustomerGroupFactory;
    protected HolidayCustomerGroupCollectionFactory $holidayCustomerGroupCollectionFactory;
    protected Holiday $holidayModel;
    protected Session $adminsession;
    private TimezoneInterface $timezone;


    public function __construct(
        Action\Context $context,
        TimezoneInterface $timezone,
        Holiday $holidayModel,
        Session $adminsession,
        HolidayCustomerGroupFactory $holidayCustomerGroupFactory,
        HolidayCustomerGroupCollectionFactory $holidayCustomerGroupCollectionFactory
    ){
        parent::__construct($context);
        $this->holidayModel = $holidayModel;
        $this->adminsession = $adminsession;
        $this->timezone = $timezone;
        $this->holidayCustomerGroupFactory = $holidayCustomerGroupFactory;
        $this->holidayCustomerGroupCollectionFactory = $holidayCustomerGroupCollectionFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        //Беру ідентифікатор для перевірки пететину, щоб прибрати з перевірки даний запис, якщо йде оновлення
        $holidayId = $this->getRequest()->getParam('holiday_id');

        $validationResult = $this->validateBeforeSave($data, $holidayId);
        if ($validationResult) {
            return $validationResult;
        }

        try {
            if (!empty($data['holiday_id'])) {
                $holiday = $this->holidayModel->load($data['holiday_id']);
            } else {
                $holiday = $this->holidayModel;
            }
            $holiday->setData($data);
            $holiday->save();
            $holidayId = $holiday->getId();

            // Важливо саме після сейву св'ята, щоб якщо новий запис створюється я отримав актуальний ідентифікатор
            if (isset($data['customer_group'])) {
                $this->saveCustomerGroups($holidayId, $data['customer_group']);
            }


            $this->messageManager->addSuccessMessage(__('You saved the Holiday.'));
            $this->adminsession->setFormData(false);

            return $this->handleRedirect($holiday);
        } catch (\Exception $e) {
            return $this->returnWithError(__('Something went wrong while saving the Holiday.'), $holidayId);
        }
    }

    private function validateBeforeSave(array &$data, $holidayId)
    {
        /**
         * Хоч додав валідацію в формі, але про всяк випадок залишу продубльовним перед обробкою
         */
        if (empty($data['name']) ||
            empty($data['discount']) ||
            empty($data['customer_group']) ||
            empty($data['start_date']) ||
            empty($data['end_date']) ||
            empty($data['exact_date']))
        {
            return $this->returnWithError(__('Name, Exact Date, Start Date and End Date are required.'), $holidayId);
        }

        $userTimezone = $this->timezone->getConfigTimezone();

        $data['start_date'] = $this->convertToUtc($data['start_date'], $userTimezone);
        $data['end_date'] = $this->convertToUtc($data['end_date'], $userTimezone);
        $data['exact_date'] = $this->convertToUtc($data['exact_date'], $userTimezone);

        if ($data['end_date'] < $data['start_date']) {
            return $this->returnWithError(__('End Date cannot be earlier than Start Date'), $holidayId);
        }
        if ($data['exact_date'] < $data['start_date'] || $data['exact_date'] > $data['end_date']) {
            return $this->returnWithError(__('Exact Date must be within the Start and End Date range'), $holidayId);
        }
        if ($this->isOverlappingHoliday($data['start_date'], $data['end_date'], $holidayId)) {
            return $this->returnWithError(__('The selected date range overlaps with an existing holiday.'), $holidayId);
        }

        return null;
    }

    /**
     * Перевіряє, чи перетинається новий період із вже існуючими.
     */
    private function isOverlappingHoliday($startDate, $endDate, $holidayId = null)
    {
        $connection = $this->holidayModel->getResource()->getConnection();

        // Підготовлений запит
        $select = $connection->select()
            ->from($this->holidayModel->getResource()->getTable('tenguyama_holidays'),
                ['count' => new \Zend_Db_Expr('COUNT(*)')]
            )
            ->where("
                holiday_id != '".$holidayId."'
                AND
                (
                    (start_date <= '".$startDate."' AND end_date >= '".$startDate."')
                    OR
                    (start_date <= '".$endDate."' AND end_date >= '".$endDate."')
                    OR
                    (start_date >= '".$startDate."'  AND end_date <= '".$endDate."' )
                )
            ");

        $result = $connection->fetchOne($select);
        return (int)$result > 0;
    }
    private function convertToUtc($date, $userTimezone)
    {
        /*
         * Логіка наступна:
         * - дата яку вводить користувач вже в його таймзоні (нехай +3, не суть)
         * - для коректного відображення на фронті і в гріді адмінки, я маю конвертувати її в +0 при збереженні
         * - після чого Magento\Framework\Stdlib\DateTime\TimezoneInterface - без проблем з +0 зробе ту, яка задана в адмінці
         */
        $dateTime = new DateTime($date . ' 00:00:00', new DateTimeZone($userTimezone));
        $dateTime->setTimezone(new DateTimeZone('UTC'));
        return $dateTime->format('Y-m-d H:i:s');
    }
    /**
     * Повертає користувача назад з помилкою.
     */
    private function returnWithError($message, $holidayId)
    {
        $this->messageManager->addErrorMessage($message);
        return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['holiday_id' => $holidayId]);
    }

    /**
     * Обробляє редірект після успішного збереження.
     */
    private function handleRedirect($holiday)
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit', [
                'holiday_id' => $holiday->getHolidayId(),
                '_current' => true
            ]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Збереження зв'язків багато до багатьох відповідно свят до груп користувачів
     */
    protected function saveCustomerGroups($holidayId, $customerGroupIds)
    {
        // Отримуємо всі поточні групи для цього holiday_id
        $existingGroups = $this->holidayCustomerGroupCollectionFactory->create()
            ->addFieldToFilter('holiday_id', $holidayId)
            ->getColumnValues('customer_group_id');

        $customerGroupIds = array_map('intval', (array) $customerGroupIds);

        // Визначаємо групи, які треба додати
        $groupsToAdd = array_diff($customerGroupIds, $existingGroups);
        // Визначаємо групи, які треба видалити
        $groupsToRemove = array_diff($existingGroups, $customerGroupIds);

        $connection = $this->holidayCustomerGroupFactory->create()->getResource()->getConnection();
        $tableName = $connection->getTableName('tenguyama_holiday_customer_groups');

        if (!empty($groupsToRemove)) {
            $connection->delete($tableName, [
                'holiday_id = ?' => $holidayId,
                'customer_group_id IN (?)' => $groupsToRemove
            ]);
        }

        foreach ($groupsToAdd as $groupId) {
            $newEntry = $this->holidayCustomerGroupFactory->create();
            $newEntry->setData([
                'holiday_id' => $holidayId,
                'customer_group_id' => $groupId
            ]);
            $newEntry->save();
        }
    }

}
