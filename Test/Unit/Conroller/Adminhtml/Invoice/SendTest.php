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

namespace Pmclain\Twilio\Test\Unit\Controller\Adminhtml\Invoice;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Pmclain\Twilio\Controller\Adminhtml\Invoice\Send;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Message\Manager;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Backend\Model\Session;
use Magento\Backend\Helper\Data;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\ObjectManager\ObjectManager;
use Pmclain\Twilio\Model\Adapter\Order\Invoice as InvoiceAdapter;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;

class SendTest extends \PHPUnit_Framework_TestCase
{
  /** @var \Pmclain\Twilio\Controller\Adminhtml\Invoice\Send */
  protected $sendController;

  /** @var \Magento\Backend\App\Action\Context|MockObject */
  protected $contextMock;

  /** @var \Magento\Sales\Api\InvoiceRepositoryInterface|MockObject */
  protected $invoiceRepositoryMock;

  /** @var \Psr\Log\LoggerInterface|MockObject */
  protected $loggerMock;

  /** @var \Magento\Framework\App\ResponseInterface|MockObject */
  protected $responseMock;

  /** @var \Magento\Framework\App\Request\Http|MockObject */
  protected $requestMock;

  /** @var \Magento\Framework\Message\Manager|MockObject */
  protected $messageManagerMock;

  /** @var \Magento\Sales\Model\Order\Invoice|MockObject */
  protected $invoiceMock;

  /** @var \Magento\Backend\Model\Session|MockObject */
  protected $sessionMock;

  /** @var \Magento\Backend\Helper\Data|MockObject */
  protected $helperMock;

  /** @var \Magento\Backend\Model\View\Result\Redirect|MockObject */
  protected $resultRedirectMock;

  /** @var \Magento\Framework\ObjectManager\ObjectManager|MockObject */
  protected $objectManager;

  /** @var \Pmclain\Twilio\Model\Adapter\Order\Invoice|MockObject */
  protected $invoiceAdapterMock;

  /** @var \Magento\Sales\Model\Order|MockObject */
  protected $orderMock;

  /** @var \Magento\Sales\Model\Order\Address|MockObject */
  protected $addressMock;

  protected function setUp() {
    $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

    $this->contextMock = $this->getMockBuilder(Context::class)
      ->disableOriginalConstructor()
      ->setMethods([
        'getRequest',
        'getResponse',
        'getMessageManager',
        'getRedirect',
        'getObjectManager',
        'getSession',
        'getHelper',
        'getResultRedirectFactory',
      ])
      ->getMock();

    $this->invoiceRepositoryMock = $this->getMockBuilder(InvoiceRepositoryInterface::class)
      ->getMockForAbstractClass();

    $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
      ->getMockForAbstractClass();

    $resultRedirectFactory = $this->getMockBuilder(RedirectFactory::class)
      ->disableOriginalConstructor()
      ->setMethods(['create'])
      ->getMock();

    $this->responseMock = $this->getMockBuilder(ResponseInterface::class)
      ->disableOriginalConstructor()
      ->setMethods(['setRedirect', 'sendResponse'])
      ->getMock();

    $this->requestMock = $this->getMockBuilder(Http::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->messageManagerMock = $this->getMockBuilder(Manager::class)
      ->disableOriginalConstructor()
      ->setMethods(['addSuccessMessage', 'addErrorMessage'])
      ->getMock();

    $this->invoiceMock = $this->getMockBuilder(Invoice::class)
      ->disableOriginalConstructor()
      ->setMethods(['getOrder', 'getEntityId'])
      ->getMock();

    $this->sessionMock = $this->getMockBuilder(Session::class)
      ->disableOriginalConstructor()
      ->setMethods(['setIsUrlNotice'])
      ->getMock();

    $this->helperMock = $this->getMockBuilder(Data::class)
      ->disableOriginalConstructor()
      ->setMethods(['getUrl'])
      ->getMock();

    $this->resultRedirectMock = $this->getMockBuilder(Redirect::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->invoiceAdapterMock = $this->getMockBuilder(InvoiceAdapter::class)
      ->disableOriginalConstructor()
      ->setMethods(['sendOrderSms'])
      ->getMock();

    $this->orderMock = $this->getMockBuilder(Order::class)
      ->disableOriginalConstructor()
      ->setMethods(['getShippingAddress'])
      ->getMock();

    $this->addressMock = $this->getMockBuilder(Address::class)
      ->disableOriginalConstructor()
      ->setMethods(['getSmsAlert'])
      ->getMock();

    $resultRedirectFactory->expects($this->any())
      ->method('create')
      ->willReturn($this->resultRedirectMock);

    $this->contextMock->expects($this->once())
      ->method('getMessageManager')
      ->willReturn($this->messageManagerMock);

    $this->contextMock->expects($this->once())
      ->method('getRequest')
      ->willReturn($this->requestMock);

    $this->contextMock->expects($this->once())
      ->method('getResponse')
      ->willReturn($this->responseMock);

    $this->contextMock->expects($this->once())
      ->method('getObjectManager')
      ->willReturn($this->objectManager);

    $this->contextMock->expects($this->once())
      ->method('getSession')
      ->willReturn($this->sessionMock);

    $this->contextMock->expects($this->once())
      ->method('getHelper')
      ->willReturn($this->helperMock);

    $this->contextMock->expects($this->once())
      ->method('getResultRedirectFactory')
      ->willReturn($resultRedirectFactory);

    $this->sendController = $objectManagerHelper->getObject(
      Send::class,
      [
        'context' => $this->contextMock,
        'request' => $this->requestMock,
        'response' => $this->responseMock,
        '_invoiceRepository' => $this->invoiceRepositoryMock,
        '_invoiceAdapter' => $this->invoiceAdapterMock,
        'logger' => $this->loggerMock,
      ]
    );
  }

  public function testExecute() {
    $invoiceId = '3';

    $this->requestMock->expects($this->once())
      ->method('getParam')
      ->willReturn($invoiceId);

    $this->resultRedirectMock->expects($this->once())
      ->method('setPath')
      ->with(
        'sales/invoice/view',
        ['invoice_id' => $invoiceId]
      )
      ->willReturnSelf();

    $this->invoiceRepositoryMock->expects($this->once())
      ->method('get')
      ->with($invoiceId)
      ->willReturn($this->invoiceMock);

    $this->invoiceMock->expects($this->once())
      ->method('getEntityId')
      ->willReturn($invoiceId);

    $this->invoiceMock->expects($this->once())
      ->method('getOrder')
      ->willReturn($this->orderMock);

    $this->orderMock->expects($this->once())
      ->method('getShippingAddress')
      ->willReturn($this->addressMock);

    $this->addressMock->expects($this->once())
      ->method('getSmsAlert')
      ->willReturn('1');

    $this->invoiceAdapterMock->expects($this->once())
      ->method('sendOrderSms')
      ->with($this->invoiceMock)
      ->willReturnSelf();

    $this->messageManagerMock->expects($this->once())
      ->method('addSuccessMessage')
      ->with('The SMS has been sent.')
      ->willReturnSelf();

    $this->resultRedirectMock->expects($this->once())
      ->method('setPath')
      ->with(
          'sales/invoice/view',
          ['invoice_id' => $invoiceId]
        );

    $this->assertEquals(
      $this->resultRedirectMock,
      $this->sendController->execute()
    );
  }

  public function testExecuteWithoutSmsOptIn() {
    $invoiceId = '3';

    $this->requestMock->expects($this->once())
      ->method('getParam')
      ->willReturn($invoiceId);

    $this->resultRedirectMock->expects($this->once())
      ->method('setPath')
      ->with(
        'sales/invoice/view',
        ['invoice_id' => $invoiceId]
      )
      ->willReturnSelf();

    $this->invoiceRepositoryMock->expects($this->once())
      ->method('get')
      ->with($invoiceId)
      ->willReturn($this->invoiceMock);

    $this->invoiceMock->expects($this->once())
      ->method('getEntityId')
      ->willReturn($invoiceId);

    $this->invoiceMock->expects($this->once())
      ->method('getOrder')
      ->willReturn($this->orderMock);

    $this->orderMock->expects($this->once())
      ->method('getShippingAddress')
      ->willReturn($this->addressMock);

    $this->addressMock->expects($this->once())
      ->method('getSmsAlert')
      ->willReturn('0');

    $this->invoiceAdapterMock->expects($this->never())
      ->method('sendOrderSms');

    $this->messageManagerMock->expects($this->once())
      ->method('addErrorMessage')
      ->with('The shipping telephone number did not opt-in for SMS notifications.')
      ->willReturnSelf();

    $this->resultRedirectMock->expects($this->once())
      ->method('setPath')
      ->with(
        'sales/invoice/view',
        ['invoice_id' => $invoiceId]
      );

    $this->assertEquals(
      $this->resultRedirectMock,
      $this->sendController->execute()
    );
  }

  public function testExecuteNoInvoiceId() {
    $this->requestMock->expects($this->once())
      ->method('getParam')
      ->willReturn(null);

    $this->invoiceRepositoryMock->expects($this->once())
      ->method('get')
      ->with(null)
      ->willThrowException(
        new \Magento\Framework\Exception\NoSuchEntityException(__('Requested entity doesn\'t exist'))
      );

    $this->messageManagerMock->expects($this->once())
      ->method('addErrorMessage')
      ->with('This invoice no longer exists.')
      ->willReturnSelf();

    $this->resultRedirectMock->expects($this->once())
      ->method('setPath')
      ->with('sales/invoice/*')
      ->willReturnSelf();

    $this->assertEquals(
      $this->resultRedirectMock,
      $this->sendController->execute()
    );
  }
}