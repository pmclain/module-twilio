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

namespace Pmclain\Twilio\Test\Unit\Plugin\Sales\Block\Adminhtml\Order\Invoice;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Pmclain\Twilio\Plugin\Sales\Block\Adminhtml\Order\Invoice\View as ViewPlugin;
use Pmclain\Twilio\Helper\Data;
use Magento\Sales\Block\Adminhtml\Order\Invoice\View as InvoiceView;
use Magento\Framework\AuthorizationInterface;
use Magento\Sales\Model\Order\Invoice;

class ViewTest extends \PHPUnit_Framework_TestCase
{
  /** @var \Pmclain\Twilio\Plugin\Sales\Block\Adminhtml\Order\Invoice\View */
  protected $viewPlugin;

  /** @var \Pmclain\Twilio\Helper\Data|MockObject */
  protected $helperMock;

  /** @var \Magento\Sales\Block\Adminhtml\Order\Invoice\View|MockObject */
  protected $invoiceViewMock;

  /** @var \Magento\Framework\AuthorizationInterface|MockObject */
  protected $authorizationMock;

  /** @var \Magento\Sales\Model\Order\Invoice|MockObject */
  protected $invoiceMock;

  protected function setUp() {
    $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

    $this->helperMock = $this->getMockBuilder(Data::class)
      ->disableOriginalConstructor()
      ->setMethods(['isInvoiceMessageEnabled'])
      ->getMock();

    $this->invoiceViewMock = $this->getMockBuilder(InvoiceView::class)
      ->disableOriginalConstructor()
      ->setMethods(['addButton', 'getInvoice'])
      ->getMock();

    $this->authorizationMock = $this->getMockBuilder(AuthorizationInterface::class)
      ->disableOriginalConstructor()
      ->setMethods(['isAllowed'])
      ->getMockForAbstractClass();

    $this->invoiceMock = $this->getMockBuilder(Invoice::class)
      ->disableOriginalConstructor()
      ->setMethods(['getId'])
      ->getMock();

    $this->invoiceViewMock->expects($this->any())
      ->method('getInvoice')
      ->willReturn($this->invoiceMock);

    $this->viewPlugin = $objectManager->getObject(
      ViewPlugin::class,
      [
        '_helper' => $this->helperMock,
        '_authorization' => $this->authorizationMock,
      ]
    );
  }

  public function testBeforeSetLayout() {
    $this->helperMock->expects($this->any())
      ->method('isInvoiceMessageEnabled')
      ->willReturn(true);

    $this->authorizationMock->expects($this->any())
      ->method('isAllowed')
      ->willReturn(true);

    $this->invoiceViewMock->expects($this->once())
      ->method('addButton')
      ->willReturnSelf();

    $this->viewPlugin->beforeSetLayout($this->invoiceViewMock);
  }

  public function testBeforeSetLayoutWithoutAccess() {
    $this->helperMock->expects($this->any())
      ->method('isInvoiceMessageEnabled')
      ->willReturn(true);

    $this->authorizationMock->expects($this->any())
      ->method('isAllowed')
      ->willReturn(false);

    $this->invoiceViewMock->expects($this->never())
      ->method('addButton')
      ->willReturnSelf();

    $this->viewPlugin->beforeSetLayout($this->invoiceViewMock);
  }
}