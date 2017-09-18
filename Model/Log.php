<?php
/**
 * Pmclain_Twilio extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GPL v3 License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/gpl.txt
 *
 * @category       Pmclain
 * @package        Twilio
 * @copyright      Copyright (c) 2017
 * @license        https://www.gnu.org/licenses/gpl.txt GPL v3 License
 */

namespace Pmclain\Twilio\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Pmclain\Twilio\Api\Data\LogInterface;

class Log extends AbstractModel implements IdentityInterface, LogInterface
{
    const CACHE_TAG = 'pmclain_twilio_log';

    /** @var string */
    protected $_cacheTag = 'pmclain_twilio_log';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Pmclain\Twilio\Model\ResourceModel\Log::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData('entity_id');
    }

    /**
     * @return int
     */
    public function getEntityTypeId()
    {
        return $this->getData('entity_type_id');
    }

    /**
     * @return string
     */
    public function getRecipientPhone()
    {
        return $this->getData('recipient_phone');
    }

    /**
     * @return boolean
     */
    public function getIsError()
    {
        return $this->getData('is_error');
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->getData('result');
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->getData('timestamp');
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData('id', $id);
    }

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData('entity_id', $entityId);
    }

    /**
     * @param int $entityTypeId
     * @return $this
     */
    public function setEntityTypeId($entityTypeId)
    {
        return $this->setData('entity_type_id', $entityTypeId);
    }

    /**
     * @param string $recipientPhone
     * @return $this
     */
    public function setRecipientPhone($recipientPhone)
    {
        return $this->setData('recipient_phone', $recipientPhone);
    }

    /**
     * @param int|boolean $isError
     * @return $this
     */
    public function setIsError($isError)
    {
        return $this->setData('is_error', $isError);
    }

    /**
     * @param string $result
     * @return $this
     */
    public function setResult($result)
    {
        return $this->setData('result', $result);
    }
}
