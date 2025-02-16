<?php

namespace Tenguyama\Holidays\Controller\Adminhtml\Index;

use DateTime;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Tenguyama\Holidays\Model\Holiday;
use Zend_Db_Expr;

class Save extends Action{

    protected $holidayModel;
    protected $adminsession;
    private $timezone;


    public function __construct(
        Action\Context $context,
        TimezoneInterface $timezone,
        Holiday $holidayModel,
        Session $adminsession
    ){
        parent::__construct($context);
        $this->holidayModel = $holidayModel;
        $this->adminsession = $adminsession;
        $this->timezone = $timezone;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $holidayId = $this->getRequest()->getParam('holiday_id');

        $validationResult = $this->validateBeforeSave($data, $holidayId);
        if ($validationResult) {
            return $validationResult;
        }

        try {
            $holiday = $this->holidayModel->load($holidayId);
            $holiday->setData($data);
            $holiday->save();

            $this->messageManager->addSuccessMessage(__('You saved the Holiday.'));
            $this->adminsession->setFormData(false);

            return $this->handleRedirect($holiday);
        } catch (\Exception $e) {
            return $this->returnWithError(__('Something went wrong while saving the Holiday.'), $holidayId);
        }
    }

    private function validateBeforeSave(array &$data, $holidayId)
    {
        if (empty($data['name']) || empty($data['start_date']) || empty($data['end_date']) || empty($data['exact_date'])) {
            return $this->returnWithError(__('Name, Exact Date, Start Date and End Date are required.'), $holidayId);
        }

        /*
        TODO треба щось думати з таймзоною, бо
        -значення яке вводиться в інпут має таким і зберігатись, бо користувач ставить його вдповідно своєї таймзони
        -відповідно дефотлну обробку маженти в гріді адмінки я вимикаю, щоб користувач бачив те що і створював
        -але буде присутня певна невідповідність на фронті

        як вихід переробити з звичайної дати на дату-час, тоді впринципі все ок буде - певне так і зроблю перед тим як робити знижку
        */


        $startDateUtc = (new DateTime($data['start_date']))->format('Y-m-d');
        $endDateUtc = (new DateTime($data['end_date']))->format('Y-m-d');
        $exactDateUtc = (new DateTime($data['exact_date']))->format('Y-m-d');

        $data['start_date'] = $startDateUtc;
        $data['end_date'] = $endDateUtc;
        $data['exact_date'] = $exactDateUtc;

        if ($endDateUtc < $startDateUtc) {
            return $this->returnWithError(__('End Date cannot be earlier than Start Date'), $holidayId);
        }
        if ($exactDateUtc < $startDateUtc || $exactDateUtc > $endDateUtc) {
            return $this->returnWithError(__('Exact Date must be within the Start and End Date range'), $holidayId);
        }
        if ($this->isOverlappingHoliday($startDateUtc, $endDateUtc, $holidayId)) {
            return $this->returnWithError(__('The selected date range overlaps with an existing holiday.'), $holidayId);
        }

        return null;
    }

    /**
     * Перевіряє, чи перетинається новий період із вже існуючими.
     */
    private function isOverlappingHoliday($startDate, $endDate, $excludeId = null)
    {
        $connection = $this->holidayModel->getResource()->getConnection();



        // Підготовлений запит
        $select = $connection->select()
            ->from($this->holidayModel->getResource()->getTable('tenguyama_holidays'),
                ['count' => new \Zend_Db_Expr('COUNT(*)')]
            )
            ->where("
                holiday_id != '".$excludeId."'
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

}
