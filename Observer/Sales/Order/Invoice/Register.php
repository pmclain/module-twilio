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

namespace Pmclain\Twilio\Observer\Sales\Order\Invoice;

use Magento\Framework\Event\ObserverInterface;
use Pmclain\Twilio\Helper\Data as Helper;
use Pmclain\Twilio\Model\Adapter\Order\Invoice as InvoiceAdapter;

class Register implements ObserverInterface
{
    /**
     * @var \Pmclain\Twilio\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Pmclain\Twilio\Model\Adapter\Order\Invoice
     */
    protected $_invoiceAdapter;

    public function __construct(
        Helper $helper,
        InvoiceAdapter $invoiceAdapter
    ) {
        $this->_helper = $helper;
        $this->_invoiceAdapter = $invoiceAdapter;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @var \Magento\Sales\Model\Order\Invoice $invoice
     * @return \Magento\Framework\Event\Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_helper->isTwilioEnabled()) {
            return $observer;
        }

        $invoice = $observer->getInvoice();
        $order = $invoice->getOrder();

        $billingAddress = $order->getBillingAddress();

        if ($billingAddress->getSmsAlert()) {
            $this->_invoiceAdapter->sendOrderSms($invoice);
        }

        return $observer;
    }
}
