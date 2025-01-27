<?php
namespace Tenguyama\Holidays\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
class Holiday extends AbstractModel implements IdentityInterface {

    const CACHE_TAG = 'tenguyama_holidays';
    protected $_cacheTag = 'tenguyama_holidays';
    protected $_eventPrefix = 'tenguyama_holidays';

    protected function _construct()
    {
        $this->_init('Tenguyama\Holidays\Model\ResourceModel\Holiday');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
