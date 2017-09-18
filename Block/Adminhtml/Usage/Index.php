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

namespace Pmclain\Twilio\Block\Adminhtml\Usage;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Twilio\Rest\ClientFactory;
use Twilio\Rest\Client;
use Pmclain\Twilio\Helper\Data as Helper;

class Index extends Template
{
    const TODAY = 'today';
    const LAST_MONTH = 'lastMonth';
    const THIS_MONTH = 'thisMonth';

    /** @var \Pmclain\Twilio\Helper\Data */
    protected $_helper;

    /** @var \Twilio\Rest\ClientFactory */
    protected $_twilioClientFactory;

    public function __construct(
        Context $context,
        ClientFactory $twilioClientFactory,
        Helper $helper
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_twilioClientFactory = $twilioClientFactory;
    }

    public function getUsageToday()
    {
        return $this->_getUsage(self::TODAY);
    }

    public function getMtdUsage()
    {
        return $this->_getUsage(self::THIS_MONTH);
    }

    public function getLastMonthUseage()
    {
        return $this->_getUsage(self::LAST_MONTH);
    }

    /**
     * @return \Twilio\Rest\Client
     */
    protected function _initTwilioClient()
    {
        return $this->_twilioClientFactory->create([
            'username' => $this->_helper->getAccountSid(),
            'password' => $this->_helper->getAccountAuthToken()
        ]);
    }

    protected function _getUsage($range)
    {
        $client = $this->_initTwilioClient();
        $result = $client->usage->records->{$range}->read(['category' => 'sms'])[0];

        $useage = [];
        $useage['count'] = $result->count;
        $useage['price'] = '$' . $result->price;

        return $useage;
    }
}
