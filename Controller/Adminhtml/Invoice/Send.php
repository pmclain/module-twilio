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

namespace Pmclain\Twilio\Controller\Adminhtml\Invoice;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Pmclain\Twilio\Model\Adapter\Order\Invoice as InvoiceAdapter;

class Send extends Action
{
  /**
   * Authorization level of a basic admin session
   * @see _isAllowed()
   */
  const ADMIN_RESOURCE = 'Pmclain_Twilio::sms';

  /** @var \Magento\Sales\Api\InvoiceRepositoryInterface */
  protected $_invoiceRepository;

  /** @var \Pmclain\Twilio\Model\Adapter\Order\Invoice */
  protected $_invoiceAdapter;

  public function __construct(
    InvoiceRepositoryInterface $invoiceRepository,
    InvoiceAdapter $invoiceAdapter,
    Context $context
  ) {
    parent::__construct($context);
    $this->_invoiceAdapter = $invoiceAdapter;
    $this->_invoiceRepository = $invoiceRepository;
  }

  public function execute() {
    $invoice = $this->_initInvoice();
    if($invoice) {
      $resultRedirect = $this->resultRedirectFactory->create()->setPath(
        'sales/invoice/view',
        ['invoice_id' => $invoice->getEntityId()]
      );

      $order = $invoice->getOrder();

      /** TODO: this is not clean */
      if(!$shippingAddress = $order->getShippingAddress()) {
        $this->messageManager->addErrorMessage(__('The order does not have a shipping address.'));

        return $resultRedirect;
      }

      if($shippingAddress->getSmsAlert()) {
        $result = $this->_invoiceAdapter->sendOrderSms($invoice);
        $this->messageManager->addSuccessMessage(__('The SMS has been sent.'));

        return $resultRedirect;
      }

      $this->messageManager->addErrorMessage(__('The shipping telephone number did not opt-in for SMS notifications.'));

      return $resultRedirect;
    }

    return $this->resultRedirectFactory->create()->setPath('sales/invoice/*');
  }

  /**
   * @return false|\Magento\Sales\Model\Order\Invoice
   */
  protected function _initInvoice() {
    $id = $this->getRequest()->getParam('id');
    try {
      $invoice = $this->_invoiceRepository->get($id);
    }catch (NoSuchEntityException $e) {
      $this->messageManager->addErrorMessage(__('This invoice no longer exists.'));
      return false;
    }catch (InputException $e) {
      $this->messageManager->addErrorMessage(__('This invoice no longer exists.'));
      return false;
    }

    return $invoice;
  }
}