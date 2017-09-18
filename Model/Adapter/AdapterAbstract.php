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

namespace Pmclain\Twilio\Model\Adapter;

use Pmclain\Twilio\Helper\Data as Helper;
use Pmclain\Twilio\Helper\MessageTemplateParser;
use Twilio\Rest\ClientFactory as TwilioClientFactory;
use Twilio\Rest\Client as TwilioClient;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Pmclain\Twilio\Model\LogFactory;

abstract class AdapterAbstract
{
    /**
     * @var \Pmclain\Twilio\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Twilio\Rest\ClientFactory
     */
    protected $_twilioClientFactory;

    /**
     * @var string
     */
    protected $_message;

    /**
     * @var string
     */
    protected $_recipientPhone;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Pmclain\Twilio\Helper\MessageTemplateParser
     */
    protected $_messageTemplateParser;

    /**
     * @var \Twilio\Rest\Api\V2010\Account\MessageInstance
     */
    protected $_smsStatus;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface;
     */
    protected $_storeManager;

    protected $_twilioLogRepository;

    protected $_twilioLogFactory;

    protected $_hasError;

    /**
     * AdapterAbstract constructor.
     * @param Helper $helper
     * @param TwilioClientFactory $twilioClientFactory
     * @param LoggerInterface $logger
     * @param MessageTemplateParser $messageTemplateParser
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Helper $helper,
        TwilioClientFactory $twilioClientFactory,
        LoggerInterface $logger,
        MessageTemplateParser $messageTemplateParser,
        StoreManagerInterface $storeManager,
        \Pmclain\Twilio\Model\LogRepository $logRepository,
        \Pmclain\Twilio\Model\LogFactory $logFactory
    ) {
        $this->_helper = $helper;
        $this->_twilioClientFactory = $twilioClientFactory;
        $this->_logger = $logger;
        $this->_messageTemplateParser = $messageTemplateParser;
        $this->_storeManager = $storeManager;
        $this->_twilioLogRepository = $logRepository;
        $this->_twilioLogFactory = $logFactory;
    }

    /**
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance
     */
    protected function _sendSms()
    {
        $client = $this->_twilioClientFactory->create([
            'username' => $this->_helper->getAccountSid(),
            'password' => $this->_helper->getAccountAuthToken()
        ]);

        return $client->messages->create(
            $this->_recipientPhone,
            [
                'from' => $this->_helper->getTwilioPhone(),
                'body' => $this->_message
            ]
        );
    }

    /**
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance
     */
    public function getSmsStatus()
    {
        return $this->_smsStatus;
    }

    /**
     * @param int $entityId
     * @param int $entityTypeId
     */
    protected function _logResult($entityId, $entityTypeId)
    {
        if (!$this->_helper->isLogEnabled()) {
            return;
        }

        $log = $this->_twilioLogFactory->create();

        $log->setEntityId($entityId);
        $log->setEntityTypeId($entityTypeId);
        $log->setRecipientPhone($this->_recipientPhone);
        $log->setIsError($this->_hasError);
        $log->setResult($this->_smsStatus);

        $this->_twilioLogRepository->save($log);
    }
}
