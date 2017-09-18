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

namespace Pmclain\Twilio\Test\Unit\Observer\Sales\Order\Invoice;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Pmclain\Twilio\Observer\Sales\Order\Invoice\Register;
use Magento\Framework\Event\Observer;
use Pmclain\Twilio\Model\Adapter\Order\Invoice as InvoiceAdapter;
use Pmclain\Twilio\Helper\Data as Helper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Invoice;

/** @codeCoverageIgnore */
class RegisterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Pmclain\Twilio\Observer\Sales\Order\Invoice\Register */
    protected $register;

    /** @var \Magento\Framework\Event\Observer|MockObject */
    protected $observerMock;

    /** @var \Pmclain\Twilio\Model\Adapter\Order\Invoice|MockObject */
    protected $invoiceAdapterMock;

    /** @var \Pmclain\Twilio\Helper\Data|MockObject */
    protected $helperMock;

    /** @var \Magento\Sales\Model\Order|MockObject */
    protected $orderMock;

    /** @var \Magento\Sales\Model\Order\Address|MockObject */
    protected $addressMock;

    /** @var \Magento\Sales\Model\Order\Invoice|MockObject */
    protected $invoiceMock;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->helperMock = $this->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->setMethods(['isTwilioEnabled'])
            ->getMock();

        $this->observerMock = $this->getMockBuilder(Observer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getInvoice'])
            ->getMock();

        $this->invoiceMock = $this->getMockBuilder(Invoice::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder'])
            ->getMock();

        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBillingAddress'])
            ->getMock();

        $this->addressMock = $this->getMockBuilder(Address::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSmsAlert'])
            ->getMock();

        $this->invoiceAdapterMock = $this->getMockBuilder(InvoiceAdapter::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendOrderSms'])
            ->getMock();

        $this->register = $objectManager->getObject(
            Register::class,
            [
                '_helper' => $this->helperMock,
                '_invoiceAdapter' => $this->invoiceAdapterMock
            ]
        );
    }

    public function testExecute()
    {
        $this->helperMock->expects($this->once())
            ->method('isTwilioEnabled')
            ->willReturn(true);

        $this->observerMock->expects($this->once())
            ->method('getInvoice')
            ->willReturn($this->invoiceMock);

        $this->invoiceMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($this->orderMock);

        $this->orderMock->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($this->addressMock);

        $this->addressMock->expects($this->once())
            ->method('getSmsAlert')
            ->willReturn(true);

        $this->invoiceAdapterMock->expects($this->once())
            ->method('sendOrderSms')
            ->with($this->invoiceMock)
            ->willReturnSelf();

        $this->register->execute($this->observerMock);
    }

    public function testExecuteWithSmsOptOut()
    {
        $this->helperMock->expects($this->once())
            ->method('isTwilioEnabled')
            ->willReturn(true);

        $this->observerMock->expects($this->once())
            ->method('getInvoice')
            ->willReturn($this->invoiceMock);

        $this->invoiceMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($this->orderMock);

        $this->orderMock->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($this->addressMock);

        $this->addressMock->expects($this->once())
            ->method('getSmsAlert')
            ->willReturn(false);

        $this->invoiceAdapterMock->expects($this->never())
            ->method('sendOrderSms');

        $this->register->execute($this->observerMock);
    }

    public function testExecuteWithModuleDisabled()
    {
        $this->helperMock->expects($this->once())
            ->method('isTwilioEnabled')
            ->willReturn(false);

        $this->observerMock->expects($this->never())
            ->method('getInvoice');

        $this->register->execute($this->observerMock);
    }
}
