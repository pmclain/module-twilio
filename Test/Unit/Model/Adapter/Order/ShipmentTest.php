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

namespace Pmclain\Twilio\Test\Unit\Model\Adapter\Order;

use Pmclain\Twilio\Model\Adapter\Order\Shipment as ShipmentAdapter;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Shipment;
use Pmclain\Twilio\Helper\Data as Helper;
use Twilio\Rest\ClientFactory as TwilioClientFactory;
use Twilio\Rest\Client as TwilioClient;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Psr\Log\LoggerInterface;
use Pmclain\Twilio\Helper\MessageTemplateParser;
use Magento\Sales\Model\Order\Shipment\Track;
use Magento\Store\Model\StoreManagerInterface;

/** @codeCoverageIgnore */
class ShipmentTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Pmclain\Twilio\Model\Adapter\Order\Shipment */
    protected $shipmentAdapter;

    /** @var \Magento\Sales\Model\Order\Shipment|MockObject */
    protected $shipmentMock;

    /** @var \Magento\Sales\Model\Order|MockObject */
    protected $orderMock;

    /** @var \Magento\Sales\Model\Order\Address|MockObject */
    protected $addressMock;

    /** @var \Pmclain\Twilio\Helper\Data|MockObject */
    protected $helperMock;

    /** @var \Twilio\Rest\ClientFactory|MockObject */
    protected $twilioClientFactoryMock;

    /** @var \Twilio\Rest\Client|MockObject */
    protected $twilioClientMock;

    /** @var \Twilio\Rest\Api\V2010\Account\MessageList|MockObject */
    protected $twilioMessagesMock;

    /** @var \Psr\Log\LoggerInterface|MockObject */
    protected $loggerMock;

    /** @var \Pmclain\Twilio\Helper\MessageTemplateParser */
    protected $messageTemplateParser;

    /** @var \Magento\Sales\Model\Order\Shipment\Track|MockObject */
    protected $trackMock;

    /** @var \Magento\Store\Model\StoreManagerInterface|MockObject */
    protected $storeManager;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->helperMock = $this->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'isShipmentMessageEnabled',
                'getRawShipmentMessage',
                'getAccountSid',
                'getAccountAuthToken',
                'getTwilioPhone',
                'isLogEnabled'
            ])
            ->getMock();

        $this->helperMock->expects($this->once())
            ->method('isShipmentMessageEnabled')
            ->willReturn(true);
        $this->helperMock->expects($this->once())
            ->method('getRawShipmentMessage')
            ->willReturn('You order has shipped! Shipment {{shipment.increment_id}} has tracking number {{shipment.trackingnumber}}.');
        $this->helperMock->expects($this->once())
            ->method('getAccountSid')
            ->willReturn('accountsid');
        $this->helperMock->expects($this->once())
            ->method('getAccountAuthToken')
            ->willReturn('accounttoken');
        $this->helperMock->expects($this->once())
            ->method('getTwilioPhone')
            ->willReturn('5559285362');
        $this->helperMock->expects($this->once())
            ->method('isLogEnabled')
            ->willReturn(false);

        $this->storeManager = $this->getMockForAbstractClass(
            StoreManagerInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getWebsite', 'getStore', 'getName', 'getWebsiteId']
        );

        $this->storeManager->expects($this->any())
            ->method('getWebsite')
            ->willReturnSelf();

        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->willReturnSelf();

        $this->loggerMock = $this->getMockForAbstractClass(
            LoggerInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['addCritical']
        );

        $this->twilioMessagesMock = $this->getMockBuilder(MessageList::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->twilioMessagesMock->expects($this->once())
            ->method('create');

        $this->twilioClientMock = $this->getMockBuilder(TwilioClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->twilioClientMock->messages = $this->twilioMessagesMock;

        $this->twilioClientFactoryMock = $this->getMockBuilder(TwilioClientFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->twilioClientFactoryMock->expects($this->once())
            ->method('create')
            ->with(['username' => 'accountsid', 'password' => 'accounttoken'])
            ->willReturn($this->twilioClientMock);

        $this->addressMock = $this->getMockBuilder(Address::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTelephone'])
            ->getMock();

        $this->addressMock->expects($this->once())
            ->method('getTelephone')
            ->willReturn('5553821442');

        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getIncrementId',
                'getTotalQtyOrdered',
                'getCustomerFirstname',
                'getCustomerLastname',
                'getGrandTotal',
                'getStoreName',
                'getShippingAddress'
            ])
            ->getMock();

        $this->orderMock->expects($this->atLeastOnce())
            ->method('getShippingAddress')
            ->willReturn($this->addressMock);

        $this->orderMock->expects($this->once())
            ->method('getIncrementId')
            ->willReturn('100000123');

        $this->trackMock = $this->getMockBuilder(Track::class)
            ->disableOriginalConstructor()
            ->setMethods(['getNumber'])
            ->getMock();

        $this->trackMock->expects($this->any())
            ->method('getNumber')
            ->willReturn('1z9827983742234');

        $this->shipmentMock = $this->getMockBuilder(Shipment::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getOrder',
                'getTotalQty',
                'getTracks',
                'getIncrementId'
            ])
            ->getMock();

        $this->shipmentMock->expects($this->atLeastOnce())
            ->method('getOrder')
            ->willReturn($this->orderMock);

        $this->shipmentMock->expects($this->once())
            ->method('getTracks')
            ->willReturnSelf();

        $this->messageTemplateParser = $objectManager->getObject(MessageTemplateParser::class);

        $this->shipmentAdapter = $objectManager->getObject(
            ShipmentAdapter::class,
            [
                '_helper' => $this->helperMock,
                '_logger' => $this->loggerMock,
                '_twilioClientFactory' => $this->twilioClientFactoryMock,
                '_messageTemplateParser' => $this->messageTemplateParser,
                '_storeManager' => $this->storeManager
            ]
        );
    }

    public function testSendOrderSms()
    {
        $this->shipmentAdapter->sendOrderSms($this->shipmentMock);
    }
}
